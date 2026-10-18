
<?php
session_start();

$pagina_activa = 'brigadistas';
$rol_requerido = 'administrador';

require_once __DIR__ . '/verificar_rol.php';
require_once __DIR__ . '/../config/database.php';

$id_usuario_actual = (int) $_SESSION['id_usuario'];

$errores = [];

$datos_brigadista = [
    'id_brigada' => '',
    'id_tipo_documento' => '',
    'numero_documento' => '',
    'nombres' => '',
    'apellidos' => '',
    'dependencia' => '',
    'celular' => ''
];

$brigadas = [];
$tipos_documento = [];

// Cargar brigadas y tipos de documento activos.
try {
    $consulta_brigadas = $pdo->query("
        SELECT id_brigada, nombre, color
        FROM brigadas
        WHERE activo = 1
        ORDER BY nombre ASC
    ");

    $brigadas = $consulta_brigadas->fetchAll(PDO::FETCH_ASSOC);

    $consulta_tipos_documento = $pdo->query("
        SELECT id_tipo_documento, nombre, abreviatura
        FROM tipos_documento
        WHERE activo = 1
        ORDER BY nombre ASC
    ");

    $tipos_documento = $consulta_tipos_documento->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Error al cargar datos del formulario de brigadistas: ' . $e->getMessage());
    $errores[] = 'No fue posible cargar las brigadas o los tipos de documento.';
}

// Procesar el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos_brigadista as $campo => $valor) {
        $datos_brigadista[$campo] = trim((string) ($_POST[$campo] ?? ''));
    }

    // Validar la brigada.
    if (
        $datos_brigadista['id_brigada'] === '' ||
        !ctype_digit($datos_brigadista['id_brigada'])
    ) {
        $errores[] = 'Seleccione una brigada válida.';
    }

    // Validar el tipo de documento.
    if (
        $datos_brigadista['id_tipo_documento'] === '' ||
        !ctype_digit($datos_brigadista['id_tipo_documento'])
    ) {
        $errores[] = 'Seleccione un tipo de documento válido.';
    }

    // Validar el número de documento.
    if ($datos_brigadista['numero_documento'] === '') {
        $errores[] = 'Ingrese el número de documento del brigadista.';
    } elseif (mb_strlen($datos_brigadista['numero_documento']) > 30) {
        $errores[] = 'El número de documento no puede superar los 30 caracteres.';
    }

    // Validar los nombres.
    if ($datos_brigadista['nombres'] === '') {
        $errores[] = 'Ingrese los nombres del brigadista.';
    } elseif (mb_strlen($datos_brigadista['nombres']) > 100) {
        $errores[] = 'Los nombres no pueden superar los 100 caracteres.';
    }

    // Validar los apellidos.
    if ($datos_brigadista['apellidos'] === '') {
        $errores[] = 'Ingrese los apellidos del brigadista.';
    } elseif (mb_strlen($datos_brigadista['apellidos']) > 100) {
        $errores[] = 'Los apellidos no pueden superar los 100 caracteres.';
    }

    // Validar la dependencia.
    if ($datos_brigadista['dependencia'] === '') {
        $errores[] = 'Ingrese la dependencia del brigadista.';
    } elseif (mb_strlen($datos_brigadista['dependencia']) > 100) {
        $errores[] = 'La dependencia no puede superar los 100 caracteres.';
    }

    // Validar el celular.
    if ($datos_brigadista['celular'] === '') {
        $errores[] = 'Ingrese el celular del brigadista.';
    } elseif (mb_strlen($datos_brigadista['celular']) > 20) {
        $errores[] = 'El celular no puede superar los 20 caracteres.';
    }

    // Guardar el brigadista.
    if (empty($errores)) {
        try {
            $pdo->beginTransaction();

            // Comprobar que la brigada esté activa.
            $consulta_brigada = $pdo->prepare("
                SELECT id_brigada
                FROM brigadas
                WHERE id_brigada = :id
                  AND activo = 1
                LIMIT 1
            ");

            $consulta_brigada->execute([
                'id' => (int) $datos_brigadista['id_brigada']
            ]);

            if (!$consulta_brigada->fetchColumn()) {
                throw new RuntimeException('La brigada seleccionada no está disponible.');
            }

            // Comprobar que el tipo de documento esté activo.
            $consulta_tipo = $pdo->prepare("
                SELECT id_tipo_documento
                FROM tipos_documento
                WHERE id_tipo_documento = :id
                  AND activo = 1
                LIMIT 1
            ");

            $consulta_tipo->execute([
                'id' => (int) $datos_brigadista['id_tipo_documento']
            ]);

            if (!$consulta_tipo->fetchColumn()) {
                throw new RuntimeException('El tipo de documento seleccionado no está disponible.');
            }

            // Evitar documentos duplicados para el mismo tipo.
            $consulta_documento = $pdo->prepare("
                SELECT id_brigadista
                FROM brigadistas
                WHERE id_tipo_documento = :tipo
                  AND numero_documento = :documento
                LIMIT 1
            ");

            $consulta_documento->execute([
                'tipo' => (int) $datos_brigadista['id_tipo_documento'],
                'documento' => $datos_brigadista['numero_documento']
            ]);

            if ($consulta_documento->fetchColumn()) {
                throw new DomainException(
                    'Ya existe un brigadista con ese tipo y número de documento.'
                );
            }

            // Insertar el brigadista.
            $insertar_brigadista = $pdo->prepare("
                INSERT INTO brigadistas (
                    id_brigada,
                    id_tipo_documento,
                    numero_documento,
                    nombres,
                    apellidos,
                    dependencia,
                    celular,
                    creado_por
                ) VALUES (
                    :id_brigada,
                    :id_tipo_documento,
                    :numero_documento,
                    :nombres,
                    :apellidos,
                    :dependencia,
                    :celular,
                    :creado_por
                )
            ");

            $insertar_brigadista->execute([
                'id_brigada' => (int) $datos_brigadista['id_brigada'],
                'id_tipo_documento' => (int) $datos_brigadista['id_tipo_documento'],
                'numero_documento' => $datos_brigadista['numero_documento'],
                'nombres' => $datos_brigadista['nombres'],
                'apellidos' => $datos_brigadista['apellidos'],
                'dependencia' => $datos_brigadista['dependencia'],
                'celular' => $datos_brigadista['celular'],
                'creado_por' => $id_usuario_actual
            ]);

            $pdo->commit();

            $_SESSION['mensaje_estado'] = 'Brigadista registrado correctamente.';
            $_SESSION['tipo_mensaje_estado'] = 'exito';

            header('Location: brigadistas.php');
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

            // Manejar también una posible colisión de la restricción UNIQUE.
            if ($e instanceof PDOException && $e->getCode() === '23000') {
                $errores[] = 'No se pudo registrar el brigadista. Verifique que el documento no esté registrado.';
            } else {
                error_log('Error al crear brigadista: ' . $e->getMessage());
                $errores[] = 'Ocurrió un error al registrar el brigadista. Verifique los datos e inténtelo nuevamente.';
            }
        }
    }
}

// Proteger los valores mostrados en HTML.
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
    <title>Registrar brigadista | RedVital Colvatenza</title>

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/crear_brigadista.css">
</head>
<body>

<div class="estructura-administrativa">

    <?php require_once __DIR__ . '/includes/menu.php'; ?>

    <main class="contenido-administrativo">

        <section class="crear-usuario crear-brigadista">

            <header class="encabezado-formulario">
                <div>
                    <h1>Registrar brigadista</h1>
                    <p>Complete la información del integrante de la brigada.</p>
                </div>

                <a href="brigadistas.php" class="boton-secundario">
                    Volver al listado
                </a>
            </header>

            <div class="contenido-formulario">

                <?php if (!empty($errores)): ?>
                    <div class="mensaje-formulario mensaje-error" role="alert">
                        <strong>No fue posible registrar el brigadista:</strong>
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

                <form action="crear_brigadista.php" method="POST" id="formulario-brigadista">

                    <section class="seccion-formulario">
                        <h2>Información del brigadista</h2>

                        <div class="campos-formulario">

                            <div class="campo-formulario">
                                <label for="id_brigada">
                                    Brigada <span class="campo-obligatorio">*</span>
                                </label>

                                <select name="id_brigada" id="id_brigada" required>
                                    <option value="">Seleccione una brigada</option>

                                    <?php foreach ($brigadas as $brigada): ?>
                                        <option
                                            value="<?= (int) $brigada['id_brigada'] ?>"
                                            data-color="<?= escapar_html($brigada['color']) ?>"
                                            <?= (string) $datos_brigadista['id_brigada'] === (string) $brigada['id_brigada'] ? 'selected' : '' ?>
                                        >
                                            <?= escapar_html($brigada['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <div class="vista-color-brigada">
                                    <span id="muestra-color-brigada" aria-hidden="true"></span>
                                    <small id="texto-color-brigada">Color de la brigada seleccionada</small>
                                </div>
                            </div>

                            <div class="campo-formulario">
                                <label for="id_tipo_documento">
                                    Tipo de documento <span class="campo-obligatorio">*</span>
                                </label>

                                <select name="id_tipo_documento" id="id_tipo_documento" required>
                                    <option value="">Seleccione una opción</option>

                                    <?php foreach ($tipos_documento as $tipo_documento): ?>
                                        <option
                                            value="<?= (int) $tipo_documento['id_tipo_documento'] ?>"
                                            <?= (string) $datos_brigadista['id_tipo_documento'] === (string) $tipo_documento['id_tipo_documento'] ? 'selected' : '' ?>
                                        >
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
                                    value="<?= escapar_html($datos_brigadista['numero_documento']) ?>"
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
                                    value="<?= escapar_html($datos_brigadista['nombres']) ?>"
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
                                    value="<?= escapar_html($datos_brigadista['apellidos']) ?>"
                                    required
                                >
                            </div>

                            <div class="campo-formulario">
                                <label for="dependencia">
                                    Dependencia <span class="campo-obligatorio">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="dependencia"
                                    id="dependencia"
                                    maxlength="100"
                                    value="<?= escapar_html($datos_brigadista['dependencia']) ?>"
                                    placeholder="Ej. Coordinación académica"
                                    required
                                >
                            </div>

                            <div class="campo-formulario">
                                <label for="celular">
                                    Celular <span class="campo-obligatorio">*</span>
                                </label>

                                <input
                                    type="tel"
                                    name="celular"
                                    id="celular"
                                    maxlength="20"
                                    value="<?= escapar_html($datos_brigadista['celular']) ?>"
                                    required
                                >
                            </div>

                        </div>
                    </section>

                    <div class="acciones-formulario">
                        <a href="brigadistas.php" class="boton-cancelar">Cancelar</a>

                        <button type="submit" class="boton-guardar">
                            Registrar brigadista
                        </button>
                    </div>

                </form>
            </div>
        </section>

    </main>
</div>

<script src="../assets/js/admin/menu.js"></script>
<script src="../assets/js/admin/crear_brigadista.js"></script>

</body>
</html>
s