<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/index.css">
    <title>Panel administrativo | RedVital Colvatenza</title>
</head>

<body>

    <?php
    require_once __DIR__ . '/proteger.php';

    $pagina_activa = 'inicio';
    ?>

    <div class="estructura-administrativa">
        <?php require_once __DIR__ . '/includes/menu.php'; ?>

        <main class="contenido-administrativo">
            <section class="seccion-opciones">

                <h2>Opciones disponibles</h2>

                <div class="opciones-panel">
                    <?php if ($nombre_rol === 'Administrador'): ?>
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
                <form action="cerrar_sesion.php" method="POST">
                    <button type="submit" class="boton-cerrar-sesion">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </main>
    </div>
    <script src="../assets/js/admin/menu.js" defer></script>
</body>
</html>
