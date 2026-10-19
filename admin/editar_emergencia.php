<?php

session_start();

$pagina_activa = 'emergencias';
$rol_requerido = 'administrador';

require_once __DIR__ . '/verificar_rol.php';
require_once __DIR__ . '/../config/database.php';

// Preparar la función para escapar contenido HTML.
function escapar_html($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// Obtener el identificador del contacto.
$id_emergencia = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_emergencia || $id_emergencia < 1) {
    $_SESSION['mensaje_emergencias'] = [
        'tipo' => 'error',
        'texto' => 'El identificador del contacto de emergencia no es válido.'
    ];

    header('Location: emergencias.php');
    exit;
}

// Inicializar las variables del formulario.
$nombre = '';
$telefono = '';
$mensaje = '';
$tipo_mensaje = '';
$emergencia = null;

// Consultar el contacto que se va a editar.
try {
    $consulta = "
        SELECT id_emergencia, nombre, telefono
        FROM emergencia
        WHERE id_emergencia = :id_emergencia
        LIMIT 1
    ";

    $consulta_preparada = $pdo->prepare($consulta);

    $consulta_preparada->execute([
        ':id_emergencia' => $id_emergencia
    ]);

    $emergencia = $consulta_preparada->fetch(PDO::FETCH_ASSOC);

    if (!$emergencia) {
        $_SESSION['mensaje_emergencias'] = [
            'tipo' => 'error',
            'texto' => 'El contacto de emergencia solicitado no existe.'
        ];

        header('Location: emergencias.php');
        exit;
    }

    // Cargar los datos actuales del contacto.
    $nombre = $emergencia['nombre'];
    $telefono = $emergencia['telefono'];

} catch (PDOException $e) {
    error_log(
        'Error al consultar emergencia para editar: '
        . $e->getMessage()
    );

    $_SESSION['mensaje_emergencias'] = [
        'tipo' => 'error',
        'texto' => 'No fue posible consultar el contacto de emergencia.'
    ];

    header('Location: emergencias.php');
    exit;
}

// Procesar el formulario de edición.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    // Validar los campos obligatorios.
    if ($nombre === '' || $telefono === '') {

        $mensaje = 'Todos los campos son obligatorios.';
        $tipo_mensaje = 'error';

    } elseif (mb_strlen($nombre, 'UTF-8') > 100) {

        $mensaje = 'El nombre no debe superar los 100 caracteres.';
        $tipo_mensaje = 'error';

    } elseif (strlen($telefono) > 20) {

        $mensaje = 'El teléfono no debe superar los 20 caracteres.';
        $tipo_mensaje = 'error';

    } else {

        try {

            // Actualizar los datos del contacto de emergencia.
            $sql = "
                UPDATE emergencia
                SET
                    nombre = :nombre,
                    telefono = :telefono,
                    actualizado_por = :actualizado_por
                WHERE id_emergencia = :id_emergencia
            ";

            $actualizacion = $pdo->prepare($sql);

            $actualizacion->execute([
                ':nombre' => $nombre,
                ':telefono' => $telefono,
                ':actualizado_por' => (int) $_SESSION['id_usuario'],
                ':id_emergencia' => $id_emergencia
            ]);

            // Guardar el mensaje de éxito en la sesión.
            $_SESSION['mensaje_emergencias'] = [
                'tipo' => 'exito',
                'texto' => 'El contacto de emergencia se actualizó correctamente.'
            ];

            // Redirigir al listado de emergencias.
            header('Location: emergencias.php');
            exit;

        } catch (PDOException $e) {

            // Registrar el error técnico.
            error_log(
                'Error al actualizar emergencia en RedVital Colvatenza: '
                . $e->getMessage()
            );

            // Mostrar un mensaje comprensible.
            $mensaje = 'No fue posible actualizar el contacto de emergencia. '
                . 'Inténtalo nuevamente.';

            $tipo_mensaje = 'error';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar emergencia | RedVital Colvatenza</title>

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/crear_emergencia.css">
</head>

<body>

    <div class="estructura-administrativa">

        <?php require_once __DIR__ . '/includes/menu.php'; ?>

        <main class="contenido-administrativo">

            <div class="crear-emergencia">

                <header class="encabezado-formulario">
                    <div>
                        <h1>Editar emergencia</h1>

                        <p>
                            Actualiza la información del contacto telefónico
                            del directorio de emergencias de RedVital Colvatenza.
                        </p>
                    </div>
                </header>

                <section class="contenido-formulario">

                    <div class="introduccion-formulario">
                        <h2>Datos de la emergencia</h2>

                        <p>
                            Modifica los campos que necesites y guarda
                            los cambios realizados.
                        </p>
                    </div>

                    <?php if ($mensaje !== ''): ?>

                        <div
                            class="mensaje-formulario <?= $tipo_mensaje === 'exito'
                                ? 'mensaje-exito'
                                : 'mensaje-error' ?>"
                            role="alert"
                        >
                            <?= escapar_html($mensaje) ?>
                        </div>

                    <?php endif; ?>

                    <form
                        action="editar_emergencia.php?id=<?= (int) $id_emergencia ?>"
                        method="POST"
                    >

                        <div class="campos-formulario">

                            <div class="campo-formulario campo-completo">

                                <label for="nombre">
                                    Nombre del contacto
                                </label>

                                <input
                                    type="text"
                                    id="nombre"
                                    name="nombre"
                                    maxlength="100"
                                    value="<?= escapar_html($nombre) ?>"
                                    placeholder="Ej. Bomberos"
                                    autocomplete="organization"
                                    required
                                >

                                <small>
                                    Escribe el nombre de la entidad o del
                                    servicio de emergencia.
                                </small>

                            </div>

                            <div class="campo-formulario campo-completo">

                                <label for="telefono">
                                    Número telefónico
                                </label>

                                <input
                                    type="tel"
                                    id="telefono"
                                    name="telefono"
                                    maxlength="20"
                                    value="<?= escapar_html($telefono) ?>"
                                    placeholder="Ej. 310 123 4567"
                                    autocomplete="tel"
                                    required
                                >

                                <small>
                                    Puedes incluir espacios, el signo +
                                    y el indicativo telefónico.
                                </small>

                            </div>

                        </div>

                        <div class="acciones-formulario">

                            <a
                                href="emergencias.php"
                                class="boton-cancelar"
                            >
                                Cancelar
                            </a>

                            <button
                                type="submit"
                                class="boton-guardar"
                            >
                                Guardar cambios
                            </button>

                        </div>

                    </form>

                </section>

            </div>

        </main>

    </div>

    <script src="../assets/js/admin/menu.js" defer></script>

</body>

</html>
