<?php

// Obtener los datos del usuario autenticado.
$id_usuario = $_SESSION['id_usuario'] ?? '';
$nombre_usuario = $_SESSION['nombres'] ?? 'Usuario';
$apellido_usuario = $_SESSION['apellidos'] ?? '';
$rol_usuario = $_SESSION['rol'] ?? 'lector';

// Obtener el nombre completo y el rol para mostrarlos.
$nombre_completo = trim($nombre_usuario . ' ' . $apellido_usuario);

$nombre_rol = match ($rol_usuario) {
    'administrador' => 'Administrador',
    'lector' => 'Lector',
    default => 'Usuario'
};

// Identificar la página activa.
$pagina_activa = $pagina_activa ?? 'inicio';

// Definir las opciones de navegación.
$opciones_menu = [
    [
        'id' => 'inicio',
        'nombre' => 'Inicio',
        'ruta' => 'index.php',
        'imagen' => 'inicio.svg',
        'administrador' => true,
        'lector' => true
    ],
    [
        'id' => 'usuarios',
        'nombre' => 'Usuarios',
        'ruta' => 'usuarios.php',
        'imagen' => 'usuarios-blanco.svg',
        'administrador' => true,
        'lector' => false
    ],
    [
        'id' => 'cursos',
        'nombre' => 'cursos',
        'ruta' => 'cursos.php',
        'imagen' => 'cursos.svg',
        'administrador' => true,
        'lector' => false
    ],
    [
        'id' => 'estudiantes',
        'nombre' => 'Estudiantes',
        'ruta' => 'estudiantes.php',
        'imagen' => 'estudiantes.svg',
        'administrador' => true,
        'lector' => true
    ],
    [
        'id' => 'brigadas',
        'nombre' => 'Brigadas',
        'ruta' => 'brigadas.php',
        'imagen' => 'brigadas.svg',
        'administrador' => true,
        'lector' => false
    ],
    [
        'id' => 'brigadistas',
        'nombre' => 'Brigadistas',
        'ruta' => 'brigadistas.php',
        'imagen' => 'brigadistas-blanco.svg',
        'administrador' => true,
        'lector' => true
    ],
    [
        'id' => 'emergencias',
        'nombre' => 'Emergencias',
        'ruta' => 'emergencias.php',
        'imagen' => 'emergencias.svg',
        'administrador' => true,
        'lector' => true
    ]
];

// Construir la ruta hacia las imágenes desde la raíz del proyecto.
$ruta_imagenes_menu = '../assets/images/';
?>


<!-- Botón para abrir el menú en tabletas y teléfonos -->
<button
    type="button"
    class="boton-menu-movil"
    id="boton-menu-movil"
    aria-label="Abrir menú de navegación"
    aria-controls="menu-administrativo"
    aria-expanded="false"
>
    <span></span>
    <span></span>
    <span></span>
</button>

<!-- Fondo que se muestra detrás del menú -->
<div
    class="fondo-menu-movil"
    id="fondo-menu-movil"
    hidden
></div>

<aside
    class="menu-administrativo"
    id="menu-administrativo"
    aria-label="Menú administrativo"
>
    <div class="encabezado-menu-movil">
        <span>Navegación</span>

        <button
            type="button"
            class="boton-cerrar-menu"
            id="boton-cerrar-menu"
            aria-label="Cerrar menú de navegación"
        >
            &times;
        </button>
    </div>

    <div class="marca-menu">
        <div class="logo-menu">
            <img
                src="<?= htmlspecialchars(
                    $ruta_imagenes_menu . 'logo-blanco.png',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                alt=""
            >
        </div>
    </div>

    <div class="perfil-menu">
        <div class="datos-perfil">
            <p class="nombre-perfil">
                <?= htmlspecialchars(
                    $nombre_completo,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <span class="rol-perfil">
                <?= htmlspecialchars(
                    $nombre_rol,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
        </div>
    </div>

    <nav class="navegacion-menu" aria-label="Menú principal">
        <p class="titulo-navegacion">MENÚ PRINCIPAL</p>

        <ul>
            <?php foreach ($opciones_menu as $opcion): ?>
                <?php
                if (
                    $rol_usuario === 'administrador' &&
                    !$opcion['administrador']
                ) {
                    continue;
                }

                if (
                    $rol_usuario === 'lector' &&
                    !$opcion['lector']
                ) {
                    continue;
                }

                $es_activa = $pagina_activa === $opcion['id'];
                ?>

                <li>
                    <a
                        href="<?= htmlspecialchars(
                            $opcion['ruta'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="enlace-menu <?= $es_activa
                            ? 'enlace-activo'
                            : '' ?>"
                        <?= $es_activa ? 'aria-current="page"' : '' ?>
                    >
                        <img
                            src="<?= htmlspecialchars(
                                $ruta_imagenes_menu . $opcion['imagen'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            alt=""
                            class="icono-menu"
                        >

                        <span>
                            <?= htmlspecialchars(
                                $opcion['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="acciones-menu">
        <a href="cerrar_sesion.php" class="enlace-cerrar-sesion">
            <img
                src="<?= htmlspecialchars(
                    $ruta_imagenes_menu . 'cerrar-sesion.svg',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                alt=""
                class="icono-menu"
            >
            <span>Cerrar sesión</span>
        </a>
    </div>
</aside>
