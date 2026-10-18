
<?php
session_start();

$pagina_activa = 'estudiantes';
$rol_requerido = 'administrador';

require_once __DIR__ . '/verificar_rol.php';
require_once __DIR__ . '/../config/database.php';

$id_usuario_actual = (int) $_SESSION['id_usuario'];
$id_estudiante = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_estudiante || $id_estudiante < 1) {
    $_SESSION['mensaje_estado'] = 'El estudiante seleccionado no es válido.';
    $_SESSION['tipo_mensaje_estado'] = 'error';
    header('Location: estudiantes.php');
    exit;
}

$errores = [];

$datos_estudiante = [
    'id_tipo_documento' => '',
    'numero_documento' => '',
    'nombres' => '',
    'apellidos' => '',
    'fecha_nacimiento' => '',
    'id_curso' => '',
    'tipo_sangre' => '',
    'direccion_residencia' => '',
    'telefono_contacto' => '',
    'condiciones_especiales' => ''
];

$acudientes = [];

$tipos_documento = [];
$cursos = [];

$tipos_sangre = [
    'A+', 'A-', 'B+', 'B-',
    'AB+', 'AB-', 'O+', 'O-'
];

$parentescos = [
    'Madre', 'Padre', 'Abuelo/a',
    'Hermano/a', 'Tío/a', 'Tutor/a',
    'Otro'
];

// Escapar los valores mostrados en HTML.
function escapar_html($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

try {
    // Cargar el estudiante.
    $consulta_estudiante = $pdo->prepare("
        SELECT *
        FROM estudiantes
        WHERE id_estudiante = :id
        LIMIT 1
    ");

    $consulta_estudiante->execute([
        'id' => $id_estudiante
    ]);

    $estudiante = $consulta_estudiante->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante) {
        $_SESSION['mensaje_estado'] = 'El estudiante solicitado no existe.';
        $_SESSION['tipo_mensaje_estado'] = 'error';
        header('Location: estudiantes.php');
        exit;
    }

    foreach ($datos_estudiante as $campo => $valor) {
        $datos_estudiante[$campo] = (string) ($estudiante[$campo] ?? '');
    }

    // Cargar los tipos de documento activos y el tipo actual, aunque esté inactivo.
    $consulta_tipos_documento = $pdo->prepare("
        SELECT id_tipo_documento, nombre, abreviatura, activo
        FROM tipos_documento
        WHERE activo = 1 OR id_tipo_documento = :id_actual
        ORDER BY nombre ASC
    ");

    $consulta_tipos_documento->execute([
        'id_actual' => (int) $datos_estudiante['id_tipo_documento']
    ]);

    $tipos_documento = $consulta_tipos_documento->fetchAll(PDO::FETCH_ASSOC);

    // Cargar los cursos activos y el curso actual, aunque esté inactivo.
    $consulta_cursos = $pdo->prepare("
        SELECT id_curso, grado, grupo, activo
        FROM cursos
        WHERE activo = 1 OR id_curso = :id_actual
        ORDER BY CAST(grado AS UNSIGNED), CAST(grupo AS UNSIGNED)
    ");

    $consulta_cursos->execute([
        'id_actual' => (int) $datos_estudiante['id_curso']
    ]);

    $cursos = $consulta_cursos->fetchAll(PDO::FETCH_ASSOC);

    // Cargar todos los acudientes relacionados con el estudiante.
    $consulta_acudientes = $pdo->prepare("
        SELECT
            a.id_acudiente,
            a.numero_documento,
            a.nombres,
            a.apellidos,
            a.telefono_contacto,
            a.correo_electronico,
            a.direccion_residencia,
            ea.parentesco
        FROM estudiante_acudiente ea
        INNER JOIN acudientes a
            ON a.id_acudiente = ea.id_acudiente
        WHERE ea.id_estudiante = :id_estudiante
        ORDER BY ea.id_estudiante_acudiente ASC
    ");

    $consulta_acudientes->execute([
        'id_estudiante' => $id_estudiante
    ]);

    $acudientes = $consulta_acudientes->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    error_log('Error al cargar estudiante para edición: ' . $e->getMessage());

    $_SESSION['mensaje_estado'] = 'No fue posible cargar la información del estudiante.';
    $_SESSION['tipo_mensaje_estado'] = 'error';
    header('Location: estudiantes.php');
    exit;
}

// Procesar el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos_estudiante as $campo => $valor) {
        $datos_estudiante[$campo] = trim((string) ($_POST[$campo] ?? ''));
    }

    $acudientes_recibidos = $_POST['acudientes'] ?? [];
    $acudientes = [];

    if (is_array($acudientes_recibidos)) {
        foreach ($acudientes_recibidos as $acudiente) {
            if (!is_array($acudiente)) {
                continue;
            }

            $acudientes[] = [
                'id_acudiente' => trim((string) ($acudiente['id_acudiente'] ?? '')),
                'numero_documento' => trim((string) ($acudiente['numero_documento'] ?? '')),
                'nombres' => trim((string) ($acudiente['nombres'] ?? '')),
                'apellidos' => trim((string) ($acudiente['apellidos'] ?? '')),
                'telefono_contacto' => trim((string) ($acudiente['telefono_contacto'] ?? '')),
                'correo_electronico' => trim((string) ($acudiente['correo_electronico'] ?? '')),
                'direccion_residencia' => trim((string) ($acudiente['direccion_residencia'] ?? '')),
                'parentesco' => trim((string) ($acudiente['parentesco'] ?? ''))
            ];
        }
    }

    // Validar los datos básicos del estudiante.
    if (
        $datos_estudiante['id_tipo_documento'] === '' ||
        !ctype_digit($datos_estudiante['id_tipo_documento'])
    ) {
        $errores[] = 'Seleccione un tipo de documento válido.';
    }

    if ($datos_estudiante['numero_documento'] === '') {
        $errores[] = 'Ingrese el número de documento del estudiante.';
    } elseif (mb_strlen($datos_estudiante['numero_documento']) > 30) {
        $errores[] = 'El número de documento no puede superar los 30 caracteres.';
    }

    foreach (['nombres' => 'nombres', 'apellidos' => 'apellidos'] as $campo => $etiqueta) {
        if ($datos_estudiante[$campo] === '') {
            $errores[] = 'Ingrese los ' . $etiqueta . ' del estudiante.';
        } elseif (mb_strlen($datos_estudiante[$campo]) > 100) {
            $errores[] = 'Los ' . $etiqueta . ' no pueden superar los 100 caracteres.';
        }
    }

    if ($datos_estudiante['fecha_nacimiento'] !== '') {
        $fecha = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $datos_estudiante['fecha_nacimiento']
        );

        $errores_fecha = DateTimeImmutable::getLastErrors();

        if (
            !$fecha ||
            ($errores_fecha !== false &&
                ($errores_fecha['warning_count'] > 0 || $errores_fecha['error_count'] > 0)) ||
            $fecha->format('Y-m-d') !== $datos_estudiante['fecha_nacimiento']
        ) {
            $errores[] = 'Ingrese una fecha de nacimiento válida.';
        } elseif ($fecha > new DateTimeImmutable('today')) {
            $errores[] = 'La fecha de nacimiento no puede ser futura.';
        }
    }

    if (
        $datos_estudiante['id_curso'] === '' ||
        !ctype_digit($datos_estudiante['id_curso'])
    ) {
        $errores[] = 'Seleccione un curso válido.';
    }

    if (
        $datos_estudiante['tipo_sangre'] !== '' &&
        !in_array($datos_estudiante['tipo_sangre'], $tipos_sangre, true)
    ) {
        $errores[] = 'Seleccione un tipo de sangre válido.';
    }

    if (mb_strlen($datos_estudiante['direccion_residencia']) > 200) {
        $errores[] = 'La dirección del estudiante no puede superar los 200 caracteres.';
    }

    if (mb_strlen($datos_estudiante['telefono_contacto']) > 20) {
        $errores[] = 'El teléfono del estudiante no puede superar los 20 caracteres.';
    }

    // Validar los acudientes enviados.
    if (count($acudientes) === 0) {
        $errores[] = 'El estudiante debe tener al menos un acudiente.';
    }

    if (count($acudientes) > 20) {
        $errores[] = 'Puede registrar máximo 20 acudientes por estudiante.';
    }

    $ids_acudientes_enviados = [];
    $documentos_acudientes = [];

    foreach ($acudientes as $indice => $acudiente) {
        $numero = $acudiente['numero_documento'];
        $id_acudiente = $acudiente['id_acudiente'];

        if ($id_acudiente !== '' && !ctype_digit($id_acudiente)) {
            $errores[] = 'Se recibió un identificador de acudiente inválido.';
        } elseif ($id_acudiente !== '') {
            if (isset($ids_acudientes_enviados[$id_acudiente])) {
                $errores[] = 'Un mismo acudiente no puede repetirse en el formulario.';
            }

            $ids_acudientes_enviados[$id_acudiente] = true;
        }

        if ($acudiente['nombres'] === '') {
            $errores[] = 'Ingrese los nombres del acudiente ' . ($indice + 1) . '.';
        } elseif (mb_strlen($acudiente['nombres']) > 100) {
            $errores[] = 'Los nombres del acudiente ' . ($indice + 1) . ' superan los 100 caracteres.';
        }

        if ($acudiente['apellidos'] === '') {
            $errores[] = 'Ingrese los apellidos del acudiente ' . ($indice + 1) . '.';
        } elseif (mb_strlen($acudiente['apellidos']) > 100) {
            $errores[] = 'Los apellidos del acudiente ' . ($indice + 1) . ' superan los 100 caracteres.';
        }

        if ($acudiente['telefono_contacto'] === '') {
            $errores[] = 'Ingrese el teléfono del acudiente ' . ($indice + 1) . '.';
        } elseif (mb_strlen($acudiente['telefono_contacto']) > 20) {
            $errores[] = 'El teléfono del acudiente ' . ($indice + 1) . ' supera los 20 caracteres.';
        }

        if (
            $acudiente['correo_electronico'] !== '' &&
            (
                !filter_var($acudiente['correo_electronico'], FILTER_VALIDATE_EMAIL) ||
                mb_strlen($acudiente['correo_electronico']) > 150
            )
        ) {
            $errores[] = 'Ingrese un correo válido para el acudiente ' . ($indice + 1) . '.';
        }

        if (mb_strlen($acudiente['direccion_residencia']) > 200) {
            $errores[] = 'La dirección del acudiente ' . ($indice + 1) . ' supera los 200 caracteres.';
        }

        if ($acudiente['parentesco'] === '') {
            $errores[] = 'Seleccione el parentesco del acudiente ' . ($indice + 1) . '.';
        } elseif (!in_array($acudiente['parentesco'], $parentescos, true)) {
            $errores[] = 'Seleccione un parentesco válido para el acudiente ' . ($indice + 1) . '.';
        }

        if ($numero !== '') {
            if (mb_strlen($numero) > 30) {
                $errores[] = 'El documento del acudiente ' . ($indice + 1) . ' supera los 30 caracteres.';
            }

            if (isset($documentos_acudientes[$numero])) {
                $errores[] = 'El documento de un acudiente está repetido en el formulario.';
            }

            $documentos_acudientes[$numero] = true;
        }
    }

    // Guardar las modificaciones.
    if (empty($errores)) {
        try {
            $pdo->beginTransaction();

            // Comprobar que el estudiante todavía exista.
            $consulta_actual = $pdo->prepare("
                SELECT id_estudiante
                FROM estudiantes
                WHERE id_estudiante = :id
                FOR UPDATE
            ");

            $consulta_actual->execute([
                'id' => $id_estudiante
            ]);

            if (!$consulta_actual->fetchColumn()) {
                throw new RuntimeException('El estudiante ya no existe.');
            }

            // Comprobar que el tipo de documento esté activo o sea el actual.
            $consulta_tipo = $pdo->prepare("
                SELECT activo
                FROM tipos_documento
                WHERE id_tipo_documento = :id
                LIMIT 1
            ");

            $consulta_tipo->execute([
                'id' => (int) $datos_estudiante['id_tipo_documento']
            ]);

            $tipo_actual = $consulta_tipo->fetch(PDO::FETCH_ASSOC);

            if (!$tipo_actual) {
                throw new RuntimeException('El tipo de documento seleccionado no existe.');
            }

            if (
                (int) $tipo_actual['activo'] !== 1 &&
                (int) $datos_estudiante['id_tipo_documento'] !== (int) $estudiante['id_tipo_documento']
            ) {
                throw new RuntimeException('No puede seleccionar un tipo de documento inactivo.');
            }

            // Comprobar que el curso esté activo o sea el actual.
            $consulta_curso = $pdo->prepare("
                SELECT activo
                FROM cursos
                WHERE id_curso = :id
                LIMIT 1
            ");

            $consulta_curso->execute([
                'id' => (int) $datos_estudiante['id_curso']
            ]);

            $curso_actual = $consulta_curso->fetch(PDO::FETCH_ASSOC);

            if (!$curso_actual) {
                throw new RuntimeException('El curso seleccionado no existe.');
            }

            if (
                (int) $curso_actual['activo'] !== 1 &&
                (int) $datos_estudiante['id_curso'] !== (int) $estudiante['id_curso']
            ) {
                throw new RuntimeException('No puede asignar al estudiante a un curso inactivo.');
            }

            // Comprobar que el documento no esté asignado a otro estudiante.
            $consulta_documento = $pdo->prepare("
                SELECT id_estudiante
                FROM estudiantes
                WHERE id_tipo_documento = :tipo
                  AND numero_documento = :documento
                  AND id_estudiante <> :id_estudiante
                LIMIT 1
            ");

            $consulta_documento->execute([
                'tipo' => (int) $datos_estudiante['id_tipo_documento'],
                'documento' => $datos_estudiante['numero_documento'],
                'id_estudiante' => $id_estudiante
            ]);

            if ($consulta_documento->fetchColumn()) {
                throw new DomainException(
                    'Ya existe otro estudiante con ese tipo y número de documento.'
                );
            }

            // Actualizar el estudiante.
            $actualizar_estudiante = $pdo->prepare("
                UPDATE estudiantes SET
                    id_tipo_documento = :id_tipo_documento,
                    numero_documento = :numero_documento,
                    nombres = :nombres,
                    apellidos = :apellidos,
                    fecha_nacimiento = :fecha_nacimiento,
                    id_curso = :id_curso,
                    tipo_sangre = :tipo_sangre,
                    direccion_residencia = :direccion_residencia,
                    telefono_contacto = :telefono_contacto,
                    condiciones_especiales = :condiciones_especiales,
                    actualizado_por = :actualizado_por
                WHERE id_estudiante = :id_estudiante
            ");

            $actualizar_estudiante->execute([
                'id_tipo_documento' => (int) $datos_estudiante['id_tipo_documento'],
                'numero_documento' => $datos_estudiante['numero_documento'],
                'nombres' => $datos_estudiante['nombres'],
                'apellidos' => $datos_estudiante['apellidos'],
                'fecha_nacimiento' => $datos_estudiante['fecha_nacimiento'] !== ''
                    ? $datos_estudiante['fecha_nacimiento']
                    : null,
                'id_curso' => (int) $datos_estudiante['id_curso'],
                'tipo_sangre' => $datos_estudiante['tipo_sangre'] !== ''
                    ? $datos_estudiante['tipo_sangre']
                    : null,
                'direccion_residencia' => $datos_estudiante['direccion_residencia'] !== ''
                    ? $datos_estudiante['direccion_residencia']
                    : null,
                'telefono_contacto' => $datos_estudiante['telefono_contacto'] !== ''
                    ? $datos_estudiante['telefono_contacto']
                    : null,
                'condiciones_especiales' => $datos_estudiante['condiciones_especiales'] !== ''
                    ? $datos_estudiante['condiciones_especiales']
                    : null,
                'actualizado_por' => $id_usuario_actual,
                'id_estudiante' => $id_estudiante
            ]);

            // Obtener las relaciones actuales del estudiante.
            $consulta_relaciones = $pdo->prepare("
                SELECT id_acudiente
                FROM estudiante_acudiente
                WHERE id_estudiante = :id
            ");

            $consulta_relaciones->execute([
                'id' => $id_estudiante
            ]);

            $ids_acudientes_actuales = array_map(
                'intval',
                $consulta_relaciones->fetchAll(PDO::FETCH_COLUMN)
            );

            $ids_acudientes_conservados = [];

            // Preparar consultas para actualizar y crear acudientes.
            $actualizar_acudiente = $pdo->prepare("
                UPDATE acudientes SET
                    numero_documento = :numero_documento,
                    nombres = :nombres,
                    apellidos = :apellidos,
                    telefono_contacto = :telefono_contacto,
                    correo_electronico = :correo_electronico,
                    direccion_residencia = :direccion_residencia,
                    actualizado_por = :actualizado_por
                WHERE id_acudiente = :id_acudiente
            ");

            $insertar_acudiente = $pdo->prepare("
                INSERT INTO acudientes (
                    numero_documento,
                    nombres,
                    apellidos,
                    telefono_contacto,
                    correo_electronico,
                    direccion_residencia,
                    creado_por
                ) VALUES (
                    :numero_documento,
                    :nombres,
                    :apellidos,
                    :telefono_contacto,
                    :correo_electronico,
                    :direccion_residencia,
                    :creado_por
                )
            ");

            $insertar_relacion = $pdo->prepare("
                INSERT INTO estudiante_acudiente (
                    id_estudiante,
                    id_acudiente,
                    parentesco,
                    creado_por
                ) VALUES (
                    :id_estudiante,
                    :id_acudiente,
                    :parentesco,
                    :creado_por
                )
            ");

            $actualizar_relacion = $pdo->prepare("
                UPDATE estudiante_acudiente SET
                    parentesco = :parentesco,
                    actualizado_por = :actualizado_por
                WHERE id_estudiante = :id_estudiante
                  AND id_acudiente = :id_acudiente
            ");

            foreach ($acudientes as $acudiente) {
                $id_acudiente = $acudiente['id_acudiente'] !== ''
                    ? (int) $acudiente['id_acudiente']
                    : 0;

                if ($id_acudiente > 0) {
                    // Verificar que el acudiente pertenezca a este estudiante.
                    if (!in_array($id_acudiente, $ids_acudientes_actuales, true)) {
                        throw new DomainException(
                            'Se intentó modificar un acudiente que no está relacionado con este estudiante.'
                        );
                    }

                    $actualizar_acudiente->execute([
                        'numero_documento' => $acudiente['numero_documento'] !== ''
                            ? $acudiente['numero_documento']
                            : null,
                        'nombres' => $acudiente['nombres'],
                        'apellidos' => $acudiente['apellidos'],
                        'telefono_contacto' => $acudiente['telefono_contacto'],
                        'correo_electronico' => $acudiente['correo_electronico'] !== ''
                            ? $acudiente['correo_electronico']
                            : null,
                        'direccion_residencia' => $acudiente['direccion_residencia'] !== ''
                            ? $acudiente['direccion_residencia']
                            : null,
                        'actualizado_por' => $id_usuario_actual,
                        'id_acudiente' => $id_acudiente
                    ]);

                    $actualizar_relacion->execute([
                        'parentesco' => $acudiente['parentesco'],
                        'actualizado_por' => $id_usuario_actual,
                        'id_estudiante' => $id_estudiante,
                        'id_acudiente' => $id_acudiente
                    ]);
                } else {
                    // Crear el acudiente nuevo.
                    $insertar_acudiente->execute([
                        'numero_documento' => $acudiente['numero_documento'] !== ''
                            ? $acudiente['numero_documento']
                            : null,
                        'nombres' => $acudiente['nombres'],
                        'apellidos' => $acudiente['apellidos'],
                        'telefono_contacto' => $acudiente['telefono_contacto'],
                        'correo_electronico' => $acudiente['correo_electronico'] !== ''
                            ? $acudiente['correo_electronico']
                            : null,
                        'direccion_residencia' => $acudiente['direccion_residencia'] !== ''
                            ? $acudiente['direccion_residencia']
                            : null,
                        'creado_por' => $id_usuario_actual
                    ]);

                    $id_acudiente = (int) $pdo->lastInsertId();

                    $insertar_relacion->execute([
                        'id_estudiante' => $id_estudiante,
                        'id_acudiente' => $id_acudiente,
                        'parentesco' => $acudiente['parentesco'],
                        'creado_por' => $id_usuario_actual
                    ]);
                }

                $ids_acudientes_conservados[] = $id_acudiente;
            }

            // Retirar las relaciones de los acudientes eliminados del formulario.
            $eliminar_relacion = $pdo->prepare("
                DELETE FROM estudiante_acudiente
                WHERE id_estudiante = :id_estudiante
                  AND id_acudiente = :id_acudiente
            ");

            $eliminar_acudiente_si_no_tiene_relaciones = $pdo->prepare("
                DELETE FROM acudientes
                WHERE id_acudiente = :id_acudiente
                  AND NOT EXISTS (
                      SELECT 1
                      FROM estudiante_acudiente
                      WHERE estudiante_acudiente.id_acudiente = acudientes.id_acudiente
                  )
            ");

            foreach ($ids_acudientes_actuales as $id_actual) {
                if (!in_array($id_actual, $ids_acudientes_conservados, true)) {
                    $eliminar_relacion->execute([
                        'id_estudiante' => $id_estudiante,
                        'id_acudiente' => $id_actual
                    ]);

                    // Eliminar el registro únicamente si ningún estudiante lo utiliza.
                    $eliminar_acudiente_si_no_tiene_relaciones->execute([
                        'id_acudiente' => $id_actual
                    ]);
                }
            }

            $pdo->commit();

            $_SESSION['mensaje_estado'] = 'La información del estudiante se actualizó correctamente.';
            $_SESSION['tipo_mensaje_estado'] = 'exito';

            header('Location: estudiantes.php');
            exit;

        } catch (DomainException | RuntimeException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $errores[] = $e->getMessage();

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log('Error al editar estudiante: ' . $e->getMessage());
            $errores[] = 'Ocurrió un error al actualizar el estudiante. Verifique los datos e inténtelo nuevamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar estudiante | RedVital Colvatenza</title>

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/crear_estudiante.css">
</head>
<body>

<div class="estructura-administrativa">

    <?php require_once __DIR__ . '/includes/menu.php'; ?>

    <main class="contenido-administrativo">

        <section class="crear-usuario crear-estudiante">

            <header class="encabezado-formulario">
                <div>
                    <h1>Editar estudiante</h1>
                    <p>Modifique la información del estudiante y sus acudientes.</p>
                </div>

                <a href="estudiantes.php" class="boton-secundario">
                    Volver al listado
                </a>
            </header>

            <div class="contenido-formulario">

                <?php if (!empty($errores)): ?>
                    <div class="mensaje-formulario mensaje-error" role="alert">
                        <strong>No fue posible actualizar el estudiante:</strong>
                        <ul>
                            <?php foreach (array_unique($errores) as $error): ?>
                                <li><?= escapar_html($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <p class="introduccion-formulario">
                    Los campos marcados con <span class="campo-obligatorio">*</span>
                    son obligatorios.
                </p>

                <form
                    action="editar_estudiante.php?id=<?= $id_estudiante ?>"
                    method="POST"
                    id="formulario-estudiante"
                >

                    <section class="seccion-formulario">
                        <h2>Información del estudiante</h2>

                        <div class="campos-formulario">

                            <div class="campo-formulario">
                                <label for="id_tipo_documento">
                                    Tipo de documento <span class="campo-obligatorio">*</span>
                                </label>

                                <select name="id_tipo_documento" id="id_tipo_documento" required>
                                    <option value="">Seleccione una opción</option>

                                    <?php foreach ($tipos_documento as $tipo_documento): ?>
                                        <option
                                            value="<?= (int) $tipo_documento['id_tipo_documento'] ?>"
                                            <?= (string) $datos_estudiante['id_tipo_documento'] === (string) $tipo_documento['id_tipo_documento'] ? 'selected' : '' ?>
                                        >
                                            <?= escapar_html($tipo_documento['nombre']) ?>
                                            (<?= escapar_html($tipo_documento['abreviatura']) ?>)
                                            <?= (int) $tipo_documento['activo'] !== 1 ? ' - Inactivo (actual)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="campo-formulario">
                                <label for="numero_documento">
                                    Número de documento <span class="campo-obligatorio">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="numero_documento"
                                    id="numero_documento"
                                    maxlength="30"
                                    value="<?= escapar_html($datos_estudiante['numero_documento']) ?>"
                                    required
                                >
                            </div>

                            <div class="campo-formulario">
                                <label for="nombres">
                                    Nombres <span class="campo-obligatorio">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="nombres"
                                    id="nombres"
                                    maxlength="100"
                                    value="<?= escapar_html($datos_estudiante['nombres']) ?>"
                                    required
                                >
                            </div>

                            <div class="campo-formulario">
                                <label for="apellidos">
                                    Apellidos <span class="campo-obligatorio">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="apellidos"
                                    id="apellidos"
                                    maxlength="100"
                                    value="<?= escapar_html($datos_estudiante['apellidos']) ?>"
                                    required
                                >
                            </div>

                            <div class="campo-formulario">
                                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                                <input
                                    type="date"
                                    name="fecha_nacimiento"
                                    id="fecha_nacimiento"
                                    value="<?= escapar_html($datos_estudiante['fecha_nacimiento']) ?>"
                                >
                            </div>

                            <div class="campo-formulario">
                                <label for="id_curso">
                                    Curso <span class="campo-obligatorio">*</span>
                                </label>

                                <select name="id_curso" id="id_curso" required>
                                    <option value="">Seleccione un curso</option>

                                    <?php foreach ($cursos as $curso): ?>
                                        <option
                                            value="<?= (int) $curso['id_curso'] ?>"
                                            <?= (string) $datos_estudiante['id_curso'] === (string) $curso['id_curso'] ? 'selected' : '' ?>
                                        >
                                            <?= escapar_html($curso['grado'] . ' - ' . $curso['grupo']) ?>
                                            <?= (int) $curso['activo'] !== 1 ? ' - Inactivo (actual)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="campo-formulario">
                                <label for="tipo_sangre">Tipo de sangre</label>
                                <select name="tipo_sangre" id="tipo_sangre">
                                    <option value="">Seleccione una opción</option>
                                    <?php foreach ($tipos_sangre as $tipo_sangre): ?>
                                        <option
                                            value="<?= escapar_html($tipo_sangre) ?>"
                                            <?= $datos_estudiante['tipo_sangre'] === $tipo_sangre ? 'selected' : '' ?>
                                        >
                                            <?= escapar_html($tipo_sangre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="campo-formulario">
                                <label for="telefono_contacto">Teléfono de contacto</label>
                                <input
                                    type="tel"
                                    name="telefono_contacto"
                                    id="telefono_contacto"
                                    maxlength="20"
                                    value="<?= escapar_html($datos_estudiante['telefono_contacto']) ?>"
                                >
                            </div>

                            <div class="campo-formulario campo-completo">
                                <label for="direccion_residencia">Dirección de residencia</label>
                                <input
                                    type="text"
                                    name="direccion_residencia"
                                    id="direccion_residencia"
                                    maxlength="200"
                                    value="<?= escapar_html($datos_estudiante['direccion_residencia']) ?>"
                                >
                            </div>

                            <div class="campo-formulario campo-completo">
                                <label for="condiciones_especiales">Condiciones especiales</label>
                                <textarea
                                    name="condiciones_especiales"
                                    id="condiciones_especiales"
                                    rows="4"
                                ><?= escapar_html($datos_estudiante['condiciones_especiales']) ?></textarea>
                                <small>
                                    Registre información relevante para la atención del estudiante
                                    en caso de emergencia.
                                </small>
                            </div>

                        </div>
                    </section>

                    <section class="seccion-formulario seccion-acudientes">
                        <div class="encabezado-seccion">
                            <div>
                                <h2>Información de los acudientes</h2>
                                <p>Puede modificar, retirar o agregar acudientes.</p>
                            </div>
                        </div>

                        <div id="contenedor-acudientes">

                            <?php foreach ($acudientes as $indice => $acudiente): ?>
                                <fieldset class="contenedor-acudiente" data-acudiente>
                                    <legend>Acudiente <?= (int) $indice + 1 ?></legend>

                                    <input
                                        type="hidden"
                                        data-campo="id_acudiente"
                                        value="<?= (int) $acudiente['id_acudiente'] ?>"
                                    >

                                    <div class="campos-formulario">

                                        <div class="campo-formulario">
                                            <label data-etiqueta="numero_documento">
                                                Número de documento
                                            </label>
                                            <input
                                                type="text"
                                                data-campo="numero_documento"
                                                maxlength="30"
                                                value="<?= escapar_html($acudiente['numero_documento']) ?>"
                                            >
                                        </div>

                                        <div class="campo-formulario">
                                            <label data-etiqueta="parentesco">
                                                Parentesco <span class="campo-obligatorio">*</span>
                                            </label>
                                            <select data-campo="parentesco" required>
                                                <option value="">Seleccione una opción</option>
                                                <?php foreach ($parentescos as $parentesco): ?>
                                                    <option
                                                        value="<?= escapar_html($parentesco) ?>"
                                                        <?= $acudiente['parentesco'] === $parentesco ? 'selected' : '' ?>
                                                    >
                                                        <?= escapar_html($parentesco) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="campo-formulario">
                                            <label data-etiqueta="nombres">
                                                Nombres <span class="campo-obligatorio">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                data-campo="nombres"
                                                maxlength="100"
                                                value="<?= escapar_html($acudiente['nombres']) ?>"
                                                required
                                            >
                                        </div>

                                        <div class="campo-formulario">
                                            <label data-etiqueta="apellidos">
                                                Apellidos <span class="campo-obligatorio">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                data-campo="apellidos"
                                                maxlength="100"
                                                value="<?= escapar_html($acudiente['apellidos']) ?>"
                                                required
                                            >
                                        </div>

                                        <div class="campo-formulario">
                                            <label data-etiqueta="telefono_contacto">
                                                Teléfono <span class="campo-obligatorio">*</span>
                                            </label>
                                            <input
                                                type="tel"
                                                data-campo="telefono_contacto"
                                                maxlength="20"
                                                value="<?= escapar_html($acudiente['telefono_contacto']) ?>"
                                                required
                                            >
                                        </div>

                                        <div class="campo-formulario">
                                            <label data-etiqueta="correo_electronico">Correo electrónico</label>
                                            <input
                                                type="email"
                                                data-campo="correo_electronico"
                                                maxlength="150"
                                                value="<?= escapar_html($acudiente['correo_electronico']) ?>"
                                            >
                                        </div>

                                        <div class="campo-formulario campo-completo">
                                            <label data-etiqueta="direccion_residencia">Dirección de residencia</label>
                                            <input
                                                type="text"
                                                data-campo="direccion_residencia"
                                                maxlength="200"
                                                value="<?= escapar_html($acudiente['direccion_residencia']) ?>"
                                            >
                                        </div>

                                    </div>

                                    <button
                                        type="button"
                                        class="boton-quitar-acudiente"
                                        data-quitar-acudiente
                                    >
                                        Quitar acudiente
                                    </button>
                                </fieldset>
                            <?php endforeach; ?>

                            <?php if (empty($acudientes)): ?>
                                <p>Este estudiante todavía no tiene acudientes registrados.</p>
                            <?php endif; ?>

                        </div>

                        <button
                            type="button"
                            class="boton-agregar-acudiente"
                            id="agregar-acudiente"
                        >
                            + Agregar acudiente
                        </button>
                    </section>

                    <div class="acciones-formulario">
                        <a href="estudiantes.php" class="boton-cancelar">Cancelar</a>
                        <button type="submit" class="boton-guardar">
                            Guardar cambios
                        </button>
                    </div>

                </form>
            </div>
        </section>

    </main>
</div>

<template id="plantilla-acudiente">
    <fieldset class="contenedor-acudiente" data-acudiente>
        <legend>Acudiente</legend>

        <input type="hidden" data-campo="id_acudiente" value="">

        <div class="campos-formulario">

            <div class="campo-formulario">
                <label data-etiqueta="numero_documento">Número de documento</label>
                <input type="text" data-campo="numero_documento" maxlength="30">
            </div>

            <div class="campo-formulario">
                <label data-etiqueta="parentesco">
                    Parentesco <span class="campo-obligatorio">*</span>
                </label>
                <select data-campo="parentesco" required>
                    <option value="">Seleccione una opción</option>
                    <?php foreach ($parentescos as $parentesco): ?>
                        <option value="<?= escapar_html($parentesco) ?>">
                            <?= escapar_html($parentesco) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label data-etiqueta="nombres">
                    Nombres <span class="campo-obligatorio">*</span>
                </label>
                <input type="text" data-campo="nombres" maxlength="100" required>
            </div>

            <div class="campo-formulario">
                <label data-etiqueta="apellidos">
                    Apellidos <span class="campo-obligatorio">*</span>
                </label>
                <input type="text" data-campo="apellidos" maxlength="100" required>
            </div>

            <div class="campo-formulario">
                <label data-etiqueta="telefono_contacto">
                    Teléfono <span class="campo-obligatorio">*</span>
                </label>
                <input type="tel" data-campo="telefono_contacto" maxlength="20" required>
            </div>

            <div class="campo-formulario">
                <label data-etiqueta="correo_electronico">Correo electrónico</label>
                <input type="email" data-campo="correo_electronico" maxlength="150">
            </div>

            <div class="campo-formulario campo-completo">
                <label data-etiqueta="direccion_residencia">Dirección de residencia</label>
                <input type="text" data-campo="direccion_residencia" maxlength="200">
            </div>

        </div>

        <button type="button" class="boton-quitar-acudiente" data-quitar-acudiente>
            Quitar acudiente
        </button>
    </fieldset>
</template>

<script src="../assets/js/admin/menu.js"></script>
<script src="../assets/js/admin/crear_estudiante.js"></script>

</body>
</html>
