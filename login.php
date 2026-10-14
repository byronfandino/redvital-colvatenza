
<?php

// Configurar las sesiones antes de iniciarlas.
ini_set('session.use_strict_mode', '1');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require_once __DIR__ . '/config/database.php';

// Redirigir si ya existe una sesión autenticada.
if (isset($_SESSION['id_usuario'])) {
    header('Location: admin/index.php');
    exit;
}

$mensaje = '';
$correo = '';

// Generar el token de seguridad del formulario.
if (empty($_SESSION['token_login'])) {
    $_SESSION['token_login'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['correo_electronico'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $token = $_POST['token_login'] ?? '';

    // Validar el token para proteger el formulario.
    if (
        !is_string($token) ||
        !hash_equals($_SESSION['token_login'], $token)
    ) {
        $mensaje = 'La solicitud no es válida. Recarga la página e inténtalo nuevamente.';

    } elseif (
        !is_string($correo) ||
        !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
        !is_string($contrasena) ||
        $contrasena === ''
    ) {
        $mensaje = 'Ingresa un correo electrónico y una contraseña válidos.';

    } else {

        try {

            // Buscar la cuenta por correo electrónico.
            $sql = 'SELECT
                        id_usuario,
                        nombres,
                        apellidos,
                        correo_electronico,
                        contrasena,
                        rol,
                        activo
                    FROM usuarios
                    WHERE correo_electronico = :correo
                    LIMIT 1';

            $consulta = $pdo->prepare($sql);

            $consulta->execute([
                ':correo' => $correo
            ]);

            $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

            // Verificar las credenciales y el estado de la cuenta.
            if (
                $usuario &&
                (int) $usuario['activo'] === 1 &&
                password_verify($contrasena, $usuario['contrasena'])
            ) {

                // Regenerar el identificador de sesión.
                session_regenerate_id(true);

                // Guardar los datos necesarios para la sesión.
                $_SESSION['id_usuario'] = (int) $usuario['id_usuario'];
                $_SESSION['nombres'] = $usuario['nombres'];
                $_SESSION['apellidos'] = $usuario['apellidos'];
                $_SESSION['correo_electronico'] = $usuario['correo_electronico'];
                $_SESSION['rol'] = $usuario['rol'];

                // Actualizar la fecha del último acceso.
                $actualizar = $pdo->prepare(
                    'UPDATE usuarios
                     SET ultimo_acceso = NOW()
                     WHERE id_usuario = :id_usuario'
                );

                $actualizar->execute([
                    ':id_usuario' => $usuario['id_usuario']
                ]);

                // Renovar el token para la siguiente sesión.
                unset($_SESSION['token_login']);

                // Redirigir al área privada.
                header('Location: admin/index.php');
                exit;

            } else {

                $mensaje = 'Correo electrónico o contraseña incorrectos.';

            }

        } catch (PDOException $e) {

            // Registrar el error en los registros del servidor.
            error_log('Error de autenticación en RedVital: ' . $e->getMessage());

            $mensaje = 'No fue posible iniciar sesión. Inténtalo nuevamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | RedVital Colvatenza</title>

    <link rel="stylesheet" href="assets/css/public/menu.css">
    <link rel="stylesheet" href="assets/css/public/pie-pagina.css">
    <link rel="stylesheet" href="assets/css/public/login.css">
</head>

<body>
    <?php include 'includes/public/navbar.php'; ?>

    <main class="contenedor-login">

        <section class="tarjeta-login">

            <header class="encabezado-login">
                <h1>RedVital Colvatenza</h1>
                <p>Ingresa a tu cuenta para continuar.</p>
            </header>

            <?php if ($mensaje !== ''): ?>
                <div class="mensaje-error" role="alert">
                    <?= htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">

                <input
                    type="hidden"
                    name="token_login"
                    value="<?= htmlspecialchars(
                        $_SESSION['token_login'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                <div class="campo-formulario">
                    <label for="correo_electronico">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        id="correo_electronico"
                        name="correo_electronico"
                        value="<?= htmlspecialchars(
                            $correo,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        maxlength="150"
                        autocomplete="username"
                        required
                    >
                </div>

                <div class="campo-formulario">
                    <label for="contrasena">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        id="contrasena"
                        name="contrasena"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="boton-login">
                    Iniciar sesión
                </button>

            </form>

        </section>

    </main>
    <?php include 'includes/public/footer.php'; ?>
</body>

</html>
