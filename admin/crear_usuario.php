<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/crear_usuario.css">
    <title>Registrar usuario | RedVital Colvatenza</title>
</head>

<body>

    <?php
        $pagina_activa = 'usuarios';
        
        // Restringir el acceso a los administradores.
        $rol_requerido = 'administrador';
        require_once __DIR__ . '/verificar_rol.php';

        // Conectar con la base de datos.
        require_once __DIR__ . '/../config/database.php';

        // Inicializar las variables del formulario.
        $nombres = '';
        $apellidos = '';
        $correo = '';
        $rol = 'lector';
        $mensaje = '';
        $tipo_mensaje = '';

        // Procesar el formulario.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombres = trim($_POST['nombres'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $correo = trim($_POST['correo_electronico'] ?? '');
            $contrasena = $_POST['contrasena'] ?? '';
            $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';
            $rol = $_POST['rol'] ?? 'lector';

            // Validar los campos obligatorios.
            if ( $nombres === '' || $apellidos === '' || $correo === '' || $contrasena === '' || $confirmar_contrasena === ''  ) 
            {
                $mensaje = 'Todos los campos son obligatorios.';
                $tipo_mensaje = 'error';
            } 
            elseif ( strlen($nombres) > 100 || strlen($apellidos) > 100) 
            {
                $mensaje = 'Los nombres y apellidos no deben superar los 100 caracteres.';
                $tipo_mensaje = 'error';

            } 
            elseif ( strlen($correo) > 150 || filter_var($correo, FILTER_VALIDATE_EMAIL) === false ) {
                $mensaje = 'Ingresa un correo electrónico válido.';
                $tipo_mensaje = 'error';
            }
            elseif ( !in_array($rol, ['administrador', 'lector'], true) ) {
                $mensaje = 'El rol seleccionado no es válido.';
                $tipo_mensaje = 'error';

            } 
            elseif (strlen($contrasena) < 8) {
                $mensaje = 'La contraseña debe tener al menos 8 caracteres.';
                $tipo_mensaje = 'error';
            } 
            elseif ($contrasena !== $confirmar_contrasena) {
                $mensaje = 'Las contraseñas no coinciden.';
                $tipo_mensaje = 'error';
            } 
            else {
                try {
                    // Comprobar si el correo ya está registrado.
                    $consulta = $pdo->prepare(
                        'SELECT id_usuario
                        FROM usuarios
                        WHERE correo_electronico = :correo
                        LIMIT 1'
                    );

                    $consulta->execute([
                        ':correo' => $correo
                    ]);

                    if ($consulta->fetch()) {

                        $mensaje = 'Ya existe una cuenta registrada con ese correo electrónico.';
                        $tipo_mensaje = 'error';

                    } else {

                        // Generar el hash de la contraseña.
                        $hash = password_hash(
                            $contrasena,
                            PASSWORD_DEFAULT
                        );

                        // Registrar el nuevo usuario.
                        $sql = 'INSERT INTO usuarios ( nombres, apellidos, correo_electronico, contrasena, rol, activo )
                                    VALUES ( :nombres, :apellidos, :correo, :contrasena, :rol,  1  )';

                        $registro = $pdo->prepare($sql);

                        $registro->execute([
                            ':nombres' => $nombres,
                            ':apellidos' => $apellidos,
                            ':correo' => $correo,
                            ':contrasena' => $hash,
                            ':rol' => $rol
                        ]);

                        // Mostrar confirmación del registro.
                        $mensaje = 'El usuario se registró correctamente.';
                        $tipo_mensaje = 'exito';

                        // Limpiar los campos después del registro.
                        $nombres = '';
                        $apellidos = '';
                        $correo = '';
                        $rol = 'lector';
                    }

                } catch (PDOException $e) {

                    error_log(
                        'Error al registrar usuario en RedVital: ' .
                        $e->getMessage()
                    );

                    $mensaje = 'No fue posible registrar el usuario. Verifica los datos e inténtalo nuevamente.';
                    $tipo_mensaje = 'error';
                }
            }
        }

    ?>

    <div class="estructura-administrativa">
            
        <?php require_once __DIR__ . '/includes/menu.php'; ?>

        <main class="contenido-administrativo">
            <div class="crear-usuario">
                <header class="encabezado-formulario">
                    <div>
                        <h1>Registrar usuario</h1>
                        <p>Crea una nueva cuenta para acceder a RedVital Colvatenza.</p>
                    </div>
                </header>
        
                <section class="contenido-formulario">
        
                    <div class="introduccion-formulario">
                        <h2>Datos de la cuenta</h2>
        
                        <p>
                            Completa los campos para registrar un nuevo usuario.
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
        
                        <?php if ($tipo_mensaje === 'exito'): ?>
        
                            <a href="usuarios.php" class="enlace-regresar" >
                                Volver al listado de usuarios
                            </a>
        
                        <?php endif; ?>
        
                    <?php endif; ?>
        
                    <form action="crear_usuario.php" method="POST">
        
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
                                <label for="contrasena">Contraseña</label>
        
                                <input
                                    type="password"
                                    id="contrasena"
                                    name="contrasena"
                                    minlength="8"
                                    autocomplete="new-password"
                                    required
                                >
        
                                <small>
                                    Debe tener al menos 8 caracteres.
                                </small>
                            </div>
        
                            <div class="campo-formulario">
                                <label for="confirmar_contrasena">
                                    Confirmar contraseña
                                </label>
        
                                <input
                                    type="password"
                                    id="confirmar_contrasena"
                                    name="confirmar_contrasena"
                                    minlength="8"
                                    autocomplete="new-password"
                                    required
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
                                        <?= $rol === 'administrador'
                                            ? 'selected'
                                            : '' ?>
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
                                Registrar usuario
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
