
<?php

session_start();

$pagina_activa = 'emergencias';
$rol_requerido = 'administrador';

require_once __DIR__ . '/verificar_rol.php';
require_once __DIR__ . '/../config/database.php';

// Inicializar las variables del formulario.
$nombre = '';
$telefono = '';
$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario.
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

            // Registrar el contacto de emergencia.
            $sql = "
                INSERT INTO emergencia (
                    nombre,
                    telefono,
                    activo,
                    creado_por
                )
                VALUES (
                    :nombre,
                    :telefono,
                    1,
                    :creado_por
                )
            ";

            $registro = $pdo->prepare($sql);

            $registro->execute([
                ':nombre' => $nombre,
                ':telefono' => $telefono,
                ':creado_por' => (int) $_SESSION['id_usuario']
            ]);

            // Guardar el mensaje de éxito en la sesión.
            $_SESSION['mensaje_emergencias'] = [
                'tipo' => 'exito',
                'texto' => 'El contacto de emergencia se registró correctamente.'
            ];

            // Redirigir al listado de emergencias.
            header('Location: emergencias.php');
            exit;

        } catch (PDOException $e) {

            // Registrar el error técnico.
            error_log(
                'Error al registrar emergencia en RedVital Colvatenza: '
                . $e->getMessage()
            );

            // Mostrar un mensaje comprensible.
            $mensaje = 'No fue posible registrar el contacto de emergencia. '
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

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/crear_emergencia.css">

    <title>Registrar emergencia | RedVital Colvatenza</title>
</head>

<body>

    <div class="estructura-administrativa">

        <?php require_once __DIR__ . '/includes/menu.php'; ?>

        <main class="contenido-administrativo">

            <div class="crear-emergencia">

                <header class="encabezado-formulario">
                    <div>
                        <h1>Registrar emergencia</h1>

                        <p>
                            Registra un nuevo contacto telefónico para
                            la atención de emergencias de RedVital Colvatenza.
                        </p>
                    </div>
                </header>

                <section class="contenido-formulario">

                    <div class="introduccion-formulario">
                        <h2>Datos de la emergencia</h2>

                        <p>
                            Completa los campos para registrar un nuevo
                            contacto en el directorio de emergencias.
                        </p>
                    </div>

                    <?php if ($mensaje !== ''): ?>

                        <div
                            class="mensaje-formulario <?= $tipo_mensaje === 'exito'
                                ? 'mensaje-exito'
                                : 'mensaje-error' ?>"
                            role="alert"
                        >
                            <?= htmlspecialchars(
                                $mensaje,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    <?php endif; ?>

                    <form action="crear_emergencia.php" method="POST">

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
                                    value="<?= htmlspecialchars(
                                        $nombre,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
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
                                    value="<?= htmlspecialchars(
                                        $telefono,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
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
                                Registrar emergencia
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
