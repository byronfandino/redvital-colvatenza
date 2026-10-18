<?php
session_start();

$pagina_activa = 'estudiantes';
$rol_requerido = 'administrador';

require_once __DIR__ . '/verificar_rol.php';
require_once __DIR__ . '/../config/database.php';

$id_usuario_actual = (int) $_SESSION['id_usuario'];

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

$acudientes = [
    [
        'numero_documento' => '',
        'nombres' => '',
        'apellidos' => '',
        'telefono_contacto' => '',
        'correo_electronico' => '',
        'direccion_residencia' => '',
        'parentesco' => ''
    ]
];

$tipos_documento = [];
$cursos = [];

$tipos_sangre = [
    'A+', 'A-', 'B+', 'B-',
    'AB+', 'AB-', 'O+', 'O-'
];

// Cargar tipos de documento activos.
try {
    $consulta_tipos_documento = $pdo->query("
        SELECT id_tipo_documento, nombre, abreviatura
        FROM tipos_documento
        WHERE activo = 1
        ORDER BY nombre ASC
    ");

    $tipos_documento = $consulta_tipos_documento->fetchAll(PDO::FETCH_ASSOC);

    // Cargar cursos activos.
    $consulta_cursos = $pdo->query("
        SELECT id_curso, grado, grupo
        FROM cursos
        WHERE activo = 1
        ORDER BY CAST(grado AS UNSIGNED), CAST(grupo AS UNSIGNED)
    ");

    $cursos = $consulta_cursos->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Error al cargar datos del formulario: ' . $e->getMessage());
    $errores[] = 'No fue posible cargar los tipos de documento o los cursos.';
}

// Procesar el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos_estudiante as $campo => $valor) {
        $datos_estudiante[$campo] = trim((string) ($_POST[$campo] ?? ''));
    }

    $acudientes_recibidos = $_POST['acudientes'] ?? [];

    if (is_array($acudientes_recibidos)) {
        $acudientes = [];

        foreach ($acudientes_recibidos as $acudiente) {
            if (!is_array($acudiente)) {
                continue;
            }

            $acudientes[] = [
                'numero_documento' => trim((string) ($acudiente['numero_documento'] ?? '')),
                'nombres' => trim((string) ($acudiente['nombres'] ?? '')),
                'apellidos' => trim((string) ($acudiente['apellidos'] ?? '')),
                'telefono_contacto' => trim((string) ($acudiente['telefono_contacto'] ?? '')),
                'correo_electronico' => trim((string) ($acudiente['correo_electronico'] ?? '')),
                'direccion_residencia' => trim((string) ($acudiente['direccion_residencia'] ?? '')),
                'parentesco' => trim((string) ($acudiente['parentesco'] ?? ''))
            ];
        }
    } else {
        $acudientes = [];
    }

    // Validar los datos del estudiante.
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

    if ($datos_estudiante['nombres'] === '') {
        $errores[] = 'Ingrese los nombres del estudiante.';
    } elseif (mb_strlen($datos_estudiante['nombres']) > 100) {
        $errores[] = 'Los nombres no pueden superar los 100 caracteres.';
    }

    if ($datos_estudiante['apellidos'] === '') {
        $errores[] = 'Ingrese los apellidos del estudiante.';
    } elseif (mb_strlen($datos_estudiante['apellidos']) > 100) {
        $errores[] = 'Los apellidos no pueden superar los 100 caracteres.';
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

    // Validar los acudientes.
    if (count($acudientes) === 0) {
        $errores[] = 'Registre al menos un acudiente.';
    }

    if (count($acudientes) > 20) {
        $errores[] = 'Puede registrar máximo 20 acudientes por estudiante.';
    }

    $documentos_acudientes = [];

    foreach ($acudientes as $indice => $acudiente) {
        $numero = $acudiente['numero_documento'];

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
        } elseif (mb_strlen($acudiente['parentesco']) > 40) {
            $errores[] = 'El parentesco del acudiente ' . ($indice + 1) . ' supera los 40 caracteres.';
        }

        // Evitar repetir el mismo documento en el formulario.
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

    // Guardar el estudiante y sus acudientes.
    if (empty($errores)) {
        try {
            $pdo->beginTransaction();

            // Comprobar que el tipo de documento esté activo.
            $consulta_tipo = $pdo->prepare("
                SELECT id_tipo_documento
                FROM tipos_documento
                WHERE id_tipo_documento = :id
                  AND activo = 1
                LIMIT 1
            ");

            $consulta_tipo->execute([
                'id' => (int) $datos_estudiante['id_tipo_documento']
            ]);

            if (!$consulta_tipo->fetchColumn()) {
                throw new RuntimeException('El tipo de documento seleccionado no está disponible.');
            }

            // Comprobar que el curso esté activo.
            $consulta_curso = $pdo->prepare("
                SELECT id_curso
                FROM cursos
                WHERE id_curso = :id
                  AND activo = 1
                LIMIT 1
            ");

            $consulta_curso->execute([
                'id' => (int) $datos_estudiante['id_curso']
            ]);

            if (!$consulta_curso->fetchColumn()) {
                throw new RuntimeException('El curso seleccionado no está disponible.');
            }

            // Evitar documentos duplicados para el mismo tipo.
            $consulta_documento = $pdo->prepare("
                SELECT id_estudiante
                FROM estudiantes
                WHERE id_tipo_documento = :tipo
                  AND numero_documento = :documento
                LIMIT 1
            ");

            $consulta_documento->execute([
                'tipo' => (int) $datos_estudiante['id_tipo_documento'],
                'documento' => $datos_estudiante['numero_documento']
            ]);

            if ($consulta_documento->fetchColumn()) {
                throw new DomainException('Ya existe un estudiante con ese tipo y número de documento.');
            }

            // Insertar el estudiante.
            $insertar_estudiante = $pdo->prepare("
                INSERT INTO estudiantes (
                    id_tipo_documento,
                    numero_documento,
                    nombres,
                    apellidos,
                    fecha_nacimiento,
                    id_curso,
                    tipo_sangre,
                    direccion_residencia,
                    telefono_contacto,
                    condiciones_especiales,
                    creado_por
                ) VALUES (
                    :id_tipo_documento,
                    :numero_documento,
                    :nombres,
                    :apellidos,
                    :fecha_nacimiento,
                    :id_curso,
                    :tipo_sangre,
                    :direccion_residencia,
                    :telefono_contacto,
                    :condiciones_especiales,
                    :creado_por
                )
            ");

            $insertar_estudiante->execute([
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
                'creado_por' => $id_usuario_actual
            ]);

            $id_estudiante = (int) $pdo->lastInsertId();

            // Preparar las consultas de acudientes y relaciones.
            $buscar_acudiente = $pdo->prepare("
                SELECT id_acudiente
                FROM acudientes
                WHERE numero_documento = :documento
                LIMIT 1
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

            $acudientes_asociados = [];

            foreach ($acudientes as $acudiente) {
                $id_acudiente = null;

                // Si tiene documento, buscar si el acudiente ya está registrado.
                if ($acudiente['numero_documento'] !== '') {
                    $buscar_acudiente->execute([
                        'documento' => $acudiente['numero_documento']
                    ]);

                    $id_existente = $buscar_acudiente->fetchColumn();

                    if ($id_existente !== false) {
                        $id_acudiente = (int) $id_existente;
                    }
                }

                // Crear el acudiente si no existe o no tiene documento.
                if ($id_acudiente === null) {
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
                }

                // Evitar asociar dos veces al mismo acudiente.
                if (isset($acudientes_asociados[$id_acudiente])) {
                    throw new DomainException(
                        'Un mismo acudiente no puede repetirse para el mismo estudiante.'
                    );
                }

                $acudientes_asociados[$id_acudiente] = true;

                // Relacionar el acudiente con el estudiante.
                $insertar_relacion->execute([
                    'id_estudiante' => $id_estudiante,
                    'id_acudiente' => $id_acudiente,
                    'parentesco' => $acudiente['parentesco'],
                    'creado_por' => $id_usuario_actual
                ]);
            }

            $pdo->commit();

            $_SESSION['mensaje_estado'] = 'Estudiante registrado correctamente.';
            $_SESSION['tipo_mensaje_estado'] = 'exito';

            header('Location: estudiantes.php');
            exit;
        } catch (DomainException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $errores[] = $e->getMessage();
        } catch (RuntimeException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $errores[] = $e->getMessage();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log('Error al crear estudiante: ' . $e->getMessage());
            $errores[] = 'Ocurrió un error al registrar el estudiante. Verifique los datos e inténtelo nuevamente.';
        }
    }
}

// Función para proteger los valores mostrados en HTML.
function escapar_html($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar estudiante | RedVital Colvatenza</title>

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
                    <h1>Registrar estudiante</h1>
                    <p>Complete la información del estudiante y sus acudientes.</p>
                </div>

                <a href="estudiantes.php" class="boton-secundario">
                    Volver al listado
                </a>
            </header>

            <div class="contenido-formulario">

                <?php if (!empty($errores)): ?>
                    <div class="mensaje-formulario mensaje-error" role="alert">
                        <strong>No fue posible registrar el estudiante:</strong>
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

                <form action="crear_estudiante.php" method="POST" id="formulario-estudiante">

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
                                        <option value="<?= (int) $tipo_documento['id_tipo_documento'] ?>"
                                            <?= ((string) $datos_estudiante['id_tipo_documento'] === (string) $tipo_documento['id_tipo_documento'] || 
                                                ($datos_estudiante['id_tipo_documento'] === '' && $tipo_documento['abreviatura'] === 'TI' )) ? 'selected' : '' ?>>
                                            <?= escapar_html($tipo_documento['nombre']) ?>
                                            (<?= escapar_html($tipo_documento['abreviatura']) ?>)
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
                                <p>Puede agregar uno o varios acudientes para este estudiante.</p>
                            </div>
                        </div>

                        <div id="contenedor-acudientes">

                            <?php foreach ($acudientes as $indice => $acudiente): ?>
                                <fieldset class="contenedor-acudiente" data-acudiente>
                                    <legend>Acudiente <?= (int) $indice + 1 ?></legend>

                                    <div class="campos-formulario">

                                        <div class="campo-formulario">
                                            <label for="acudiente_documento_<?= (int) $indice ?>">
                                                Número de documento
                                            </label>

                                            <input
                                                type="text"
                                                id="acudiente_documento_<?= (int) $indice ?>"
                                                name="acudientes[<?= (int) $indice ?>][numero_documento]"
                                                data-campo="numero_documento"
                                                maxlength="30"
                                                value="<?= escapar_html($acudiente['numero_documento']) ?>"
                                            >
                                        </div>

                                        <div class="campo-formulario">
                                            <label for="acudiente_parentesco_<?= (int) $indice ?>">
                                                Parentesco <span class="campo-obligatorio">*</span>
                                            </label>

                                            <select
                                                id="acudiente_parentesco_<?= (int) $indice ?>"
                                                name="acudientes[<?= (int) $indice ?>][parentesco]"
                                                data-campo="parentesco"
                                                required
                                            >
                                                <option value="">Seleccione una opción</option>

                                                <?php
                                                $parentescos = [
                                                    'Madre', 'Padre', 'Abuelo/a',
                                                    'Hermano/a', 'Tío/a', 'Tutor/a',
                                                    'Otro'
                                                ];
                                                ?>

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
                                            <label for="acudiente_nombres_<?= (int) $indice ?>">
                                                Nombres <span class="campo-obligatorio">*</span>
                                            </label>

                                            <input
                                                type="text"
                                                id="acudiente_nombres_<?= (int) $indice ?>"
                                                name="acudientes[<?= (int) $indice ?>][nombres]"
                                                data-campo="nombres"
                                                maxlength="100"
                                                value="<?= escapar_html($acudiente['nombres']) ?>"
                                                required
                                            >
                                        </div>

                                        <div class="campo-formulario">
                                            <label for="acudiente_apellidos_<?= (int) $indice ?>">
                                                Apellidos <span class="campo-obligatorio">*</span>
                                            </label>

                                            <input
                                                type="text"
                                                id="acudiente_apellidos_<?= (int) $indice ?>"
                                                name="acudientes[<?= (int) $indice ?>][apellidos]"
                                                data-campo="apellidos"
                                                maxlength="100"
                                                value="<?= escapar_html($acudiente['apellidos']) ?>"
                                                required
                                            >
                                        </div>

                                        <div class="campo-formulario">
                                            <label for="acudiente_telefono_<?= (int) $indice ?>">
                                                Teléfono <span class="campo-obligatorio">*</span>
                                            </label>

                                            <input
                                                type="tel"
                                                id="acudiente_telefono_<?= (int) $indice ?>"
                                                name="acudientes[<?= (int) $indice ?>][telefono_contacto]"
                                                data-campo="telefono_contacto"
                                                maxlength="20"
                                                value="<?= escapar_html($acudiente['telefono_contacto']) ?>"
                                                required
                                            >
                                        </div>

                                        <div class="campo-formulario">
                                            <label for="acudiente_correo_<?= (int) $indice ?>">
                                                Correo electrónico
                                            </label>

                                            <input
                                                type="email"
                                                id="acudiente_correo_<?= (int) $indice ?>"
                                                name="acudientes[<?= (int) $indice ?>][correo_electronico]"
                                                data-campo="correo_electronico"
                                                maxlength="150"
                                                value="<?= escapar_html($acudiente['correo_electronico']) ?>"
                                            >
                                        </div>

                                        <div class="campo-formulario campo-completo">
                                            <label for="acudiente_direccion_<?= (int) $indice ?>">
                                                Dirección de residencia
                                            </label>

                                            <input
                                                type="text"
                                                id="acudiente_direccion_<?= (int) $indice ?>"
                                                name="acudientes[<?= (int) $indice ?>][direccion_residencia]"
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
                                        <?= count($acudientes) === 1 ? 'hidden' : '' ?>
                                    >
                                        Quitar acudiente
                                    </button>
                                </fieldset>
                            <?php endforeach; ?>

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
                            Registrar estudiante
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
                    <option value="Madre">Madre</option>
                    <option value="Padre">Padre</option>
                    <option value="Abuelo/a">Abuelo/a</option>
                    <option value="Hermano/a">Hermano/a</option>
                    <option value="Tío/a">Tío/a</option>
                    <option value="Tutor/a">Tutor/a</option>
                    <option value="Otro">Otro</option>
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
