
<?php

require_once __DIR__ . '/config/database.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombres = trim($_POST['nombres'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $correo = trim($_POST['correo_electronico'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if (
        $nombres === '' ||
        $apellidos === '' ||
        !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
        strlen($contrasena) < 8
    ) {
        $mensaje = 'Verifica los datos. La contraseña debe tener al menos 12 caracteres.';
    } else {

        try {

            // Verificar que no exista ningún usuario todavía.
            $consulta = $pdo->query(
                'SELECT COUNT(*) FROM usuarios'
            );

            $totalUsuarios = (int) $consulta->fetchColumn();

            if ($totalUsuarios > 0) {

                $mensaje = 'Ya existen usuarios registrados. No se creó ninguna cuenta.';

            } else {

                // Proteger la contraseña mediante un hash.
                $hash = password_hash(
                    $contrasena,
                    PASSWORD_DEFAULT
                );

                // Registrar la primera cuenta administradora.
                $sql = 'INSERT INTO usuarios ( nombres, apellidos, correo_electronico, contrasena, rol, activo ) VALUES
                                             ( :nombres, :apellidos, :correo, :contrasena, :rol, :activo )';

                $sentencia = $pdo->prepare($sql);

                $sentencia->execute([
                    ':nombres' => $nombres,
                    ':apellidos' => $apellidos,
                    ':correo' => $correo,
                    ':contrasena' => $hash,
                    ':rol' => 'administrador',
                    ':activo' => 1
                ]);

                $mensaje = 'Administrador creado correctamente.';
            }

        } catch (PDOException $e) {

            $mensaje = 'No fue posible registrar el administrador. Verifica los datos.';

        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear administrador - RedVital</title>
</head>
<body>

    <h1>Crear administrador inicial</h1>

    <?php if ($mensaje !== ''): ?>
        <p><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="nombres">Nombres</label>
            <input
                type="text"
                id="nombres"
                name="nombres"
                maxlength="100"
                required
            >
        </div>

        <div>
            <label for="apellidos">Apellidos</label>
            <input
                type="text"
                id="apellidos"
                name="apellidos"
                maxlength="100"
                required
            >
        </div>

        <div>
            <label for="correo_electronico">Correo electrónico</label>
            <input
                type="email"
                id="correo_electronico"
                name="correo_electronico"
                maxlength="150"
                required
            >
        </div>

        <div>
            <label for="contrasena">Contraseña</label>
            <input
                type="password"
                id="contrasena"
                name="contrasena"
                minlength="8"
                autocomplete="new-password"
                required
            >
        </div>

        <button type="submit">Crear administrador</button>
    </form>

</body>
</html>
