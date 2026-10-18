 
<?php

$pagina_activa = 'usuarios';
$rol_requerido = 'administrador';

require_once __DIR__ . '/verificar_rol.php';
require_once __DIR__ . '/../config/database.php';

// Identificar el usuario seleccionado para editar.
$id_usuario_editar = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

$mensaje = '';
$tipo_mensaje = '';
$usuario = null;

// Validar el identificador.
if (!$id_usuario_editar || $id_usuario_editar < 1) {
    $_SESSION['mensaje_estado'] = 'El identificador del usuario no es válido.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: usuarios.php');
    exit;
}

// Consultar el usuario seleccionado.
try {
    $consulta = $pdo->prepare(
        'SELECT id_usuario, nombres, apellidos, correo_electronico, rol
         FROM usuarios
         WHERE id_usuario = :id_usuario
         LIMIT 1'
    );

    $consulta->execute([
        ':id_usuario' => $id_usuario_editar
    ]);

    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Error al consultar usuario: ' . $e->getMessage());

    $_SESSION['mensaje_estado'] = 'No fue posible consultar los datos del usuario.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: usuarios.php');
    exit;
}

// Verificar que el usuario exista.
if (!$usuario) {
    $_SESSION['mensaje_estado'] = 'El usuario seleccionado no existe.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: usuarios.php');
    exit;
}

// Cargar los datos actuales.
$nombres = $usuario['nombres'];
$apellidos = $usuario['apellidos'];
$correo = $usuario['correo_electronico'];
$rol = $usuario['rol'];

// Procesar el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombres = trim($_POST['nombres'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $correo = trim($_POST['correo_electronico'] ?? '');
    $rol = $_POST['rol'] ?? '';

    $contrasena = $_POST['contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

    // Validar los campos obligatorios.
    if (
        $nombres === '' ||
        $apellidos === '' ||
        $correo === '' ||
        $rol === ''
    ) {
        $mensaje = 'Completa todos los campos obligatorios.';
        $tipo_mensaje = 'error';

    } elseif (
        strlen($nombres) > 100 ||
        strlen($apellidos) > 100
    ) {
        $mensaje = 'Los nombres y apellidos no deben superar los 100 caracteres.';
        $tipo_mensaje = 'error';

    } elseif (
        strlen($correo) > 150 ||
        filter_var($correo, FILTER_VALIDATE_EMAIL) === false
    ) {
        $mensaje = 'Ingresa un correo electrónico válido.';
        $tipo_mensaje = 'error';

    } elseif (!in_array($rol, ['administrador', 'lector'], true)) {
        $mensaje = 'El rol seleccionado no es válido.';
        $tipo_mensaje = 'error';

    } elseif (
        $contrasena !== '' &&
        strlen($contrasena) < 8
    ) {
        $mensaje = 'La nueva contraseña debe tener al menos 8 caracteres.';
        $tipo_mensaje = 'error';

    } elseif (
        $contrasena !== '' &&
        $contrasena !== $confirmar_contrasena
    ) {
        $mensaje = 'Las contraseñas no coinciden.';
        $tipo_mensaje = 'error';

    } elseif (
        $contrasena === '' &&
        $confirmar_contrasena !== ''
    ) {
        $mensaje = 'Ingresa una nueva contraseña para poder confirmarla.';
        $tipo_mensaje = 'error';

    } else {

        try {
            // Comprobar si otro usuario utiliza el mismo correo.
            $consulta = $pdo->prepare(
                'SELECT id_usuario
                 FROM usuarios
                 WHERE correo_electronico = :correo
                   AND id_usuario <> :id_usuario
                 LIMIT 1'
            );

            $consulta->execute([
                ':correo' => $correo,
                ':id_usuario' => $id_usuario_editar
            ]);

            if ($consulta->fetch()) {
                $mensaje = 'Otro usuario ya tiene registrado ese correo electrónico.';
                $tipo_mensaje = 'error';

            } else {

                // Preparar los parámetros de actualización.
                $parametros = [
                    ':nombres' => $nombres,
                    ':apellidos' => $apellidos,
                    ':correo' => $correo,
                    ':rol' => $rol,
                    ':id_usuario' => $id_usuario_editar
                ];

                // Actualizar la contraseña únicamente si se proporcionó una nueva.
                if ($contrasena !== '') {

                    $hash = password_hash(
                        $contrasena,
                        PASSWORD_DEFAULT
                    );

                    $sql = 'UPDATE usuarios
                            SET nombres = :nombres,
                                apellidos = :apellidos,
                                correo_electronico = :correo,
                                rol = :rol,
                                contrasena = :contrasena,
                                actualizado_en = CURRENT_TIMESTAMP
                            WHERE id_usuario = :id_usuario';

                    $parametros[':contrasena'] = $hash;

                } else {

                    $sql = 'UPDATE usuarios
                            SET nombres = :nombres,
                                apellidos = :apellidos,
                                correo_electronico = :correo,
                                rol = :rol,
                                actualizado_en = CURRENT_TIMESTAMP
                            WHERE id_usuario = :id_usuario';
                }

                $registro = $pdo->prepare($sql);
                $registro->execute($parametros);

                // Guardar el mensaje de éxito en la sesión.
                $_SESSION['mensaje_estado'] = 'Los datos del usuario se actualizaron correctamente.';
                $_SESSION['tipo_mensaje_estado'] = 'exito';

                // Volver al listado.
                header('Location: usuarios.php');
                exit;
            }

        } catch (PDOException $e) {
            error_log(
                'Error al editar usuario en RedVital: ' . $e->getMessage()
            );

            $mensaje = 'No fue posible actualizar el usuario. Verifica los datos e inténtalo nuevamente.';
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
    <link rel="stylesheet" href="../assets/css/admin/editar_usuario.css" >
    <title>Editar usuario | RedVital Colvatenza</title>
</head>

<body>

    <div class="estructura-administrativa">
            
        <?php require_once __DIR__ . '/includes/menu.php'; ?>

        <main class="contenido-administrativo">
            <div class="editar-usuario">
    
                <header class="encabezado-formulario">
                    <div>
                        <h1>Editar usuario</h1>
                        <p>Actualiza los datos de la cuenta seleccionada.</p>
                    </div>
                </header>
    
                <section class="contenido-formulario">
    
                    <div class="introduccion-formulario">
                        <h2>Datos de la cuenta</h2>
                        <p>
                            Modifica la información del usuario.
                            Los campos de contraseña son opcionales.
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
    
                    <form
                        action="editar_usuario.php?id=<?= (int) $id_usuario_editar ?>"
                        method="POST"
                    >
    
                        <div class="campos-formulario">
    
                            <div class="campo-formulario">
                                <label for="nombres">Nombres</label>
                                <input
                                    type="text"
                                    id="nombres"
                                    name="nombres"
                                    maxlength="100"
                                    value="<?= htmlspecialchars(
                                        $nombres,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    autocomplete="given-name"
                                    required
                                >
                            </div>
    
                            <div class="campo-formulario">
                                <label for="apellidos">Apellidos</label>
                                <input
                                    type="text"
                                    id="apellidos"
                                    name="apellidos"
                                    maxlength="100"
                                    value="<?= htmlspecialchars(
                                        $apellidos,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    autocomplete="family-name"
                                    required
                                >
                            </div>
    
                            <div class="campo-formulario campo-completo">
                                <label for="correo_electronico">
                                    Correo electrónico
                                </label>
                                <input
                                    type="email"
                                    id="correo_electronico"
                                    name="correo_electronico"
                                    maxlength="150"
                                    value="<?= htmlspecialchars(
                                        $correo,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    autocomplete="email"
                                    required
                                >
                            </div>
    
                            <div class="campo-formulario">
                                <label for="contrasena">Nueva contraseña</label>
                                <input
                                    type="password"
                                    id="contrasena"
                                    name="contrasena"
                                    minlength="8"
                                    autocomplete="new-password"
                                >
                                <small>
                                    Déjala vacía si deseas conservar la contraseña actual.<br>
                                    Si la cambias, debe tener al menos 8 caracteres.
                                </small>
                            </div>
    
                            <div class="campo-formulario">
                                <label for="confirmar_contrasena">
                                    Confirmar nueva contraseña
                                </label>
                                <input
                                    type="password"
                                    id="confirmar_contrasena"
                                    name="confirmar_contrasena"
                                    minlength="8"
                                    autocomplete="new-password"
                                >
                            </div>
    
                            <div class="campo-formulario campo-completo">
                                <label for="rol">Rol del usuario</label>
    
                                <select id="rol" name="rol" required>
                                    <option
                                        value="lector"
                                        <?= $rol === 'lector' ? 'selected' : '' ?>
                                    >
                                        Lector
                                    </option>
    
                                    <option
                                        value="administrador"
                                        <?= $rol === 'administrador' ? 'selected' : '' ?>
                                    >
                                        Administrador
                                    </option>
                                </select>
    
                                <small>
                                    El administrador puede gestionar las cuentas.
                                    El lector tendrá acceso de consulta.
                                </small>
                            </div>
    
                        </div>
    
                        <div class="acciones-formulario">
                            <a href="usuarios.php" class="boton-cancelar">
                                Cancelar
                            </a>
    
                            <button type="submit" class="boton-guardar">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
