<?php

require_once __DIR__ . '/proteger.php';

// Obtener los datos del usuario autenticado.
$nombres = $_SESSION['nombres'] ?? '';
$apellidos = $_SESSION['apellidos'] ?? '';
$rol = $_SESSION['rol'] ?? '';

// Definir el nombre del rol para mostrarlo.
$nombre_rol = match ($rol) {
    'administrador' => 'Administrador',
    'lector' => 'Lector',
    default => 'Usuario'
};

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin/index.css">
    <title>Panel administrativo | RedVital Colvatenza</title>
</head>

<body>
    <main class="panel-administrativo">
        <header class="encabezado-panel">
            <h1>RedVital Colvatenza</h1>
            <p>Panel administrativo</p>
        </header>

        <section class="informacion-usuario">
            <div>
                <h2>
                    Hola,
                    <?= htmlspecialchars(
                        $nombres,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h2>

                <p>Gestiona la información del sistema.</p>

                <span class="rol-usuario">
                    <?= htmlspecialchars(
                        $nombre_rol,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>
            </div>
        </section>

        <section class="seccion-opciones">

            <h2>Opciones disponibles</h2>

            <div class="opciones-panel">
                <?php if ($rol === 'administrador'): ?>
                    <article class="tarjeta-opcion">
                        <img
                            src="../assets/images/usuarios.svg"
                            alt="Gestión de usuarios"
                            class="imagen-opcion"
                        >
                        <h3>Gestión de usuarios</h3>
                        <p>
                            Administra las cuentas y los permisos
                            de acceso al sistema.
                        </p>
                        <a href="usuarios.php" class="enlace-opcion">
                            Administrar usuarios
                        </a>
                    </article>

                <?php endif; ?>

                <article class="tarjeta-opcion">
                    <img
                        src="../assets/images/modulos.svg"
                        alt="Consulta de información"
                        class="imagen-opcion"
                    >

                    <h3>Consulta de información</h3>
                    <p>
                        Consulta los módulos disponibles
                        en el sistema.
                    </p>
                    <a href="#" class="enlace-opcion">
                        Consultar información
                    </a>

                </article>
            </div>
        </section>

        <div class="acciones-panel">
            <form action="logout.php" method="POST">
                <button type="submit" class="boton-cerrar-sesion">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </main>
</body>

</html>
