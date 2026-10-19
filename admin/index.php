
<?php

session_start();

require_once __DIR__ . '/proteger.php';

// Identificar la página activa.
$pagina_activa = 'inicio';

// Obtener el rol del usuario autenticado.
$rol_usuario = strtolower(trim($_SESSION['rol'] ?? ''));

// Definir los módulos disponibles según el rol.
$modulos = [
    [
        'id' => 'usuarios',
        'nombre' => 'Gestión de usuarios',
        'descripcion' => 'Administra las cuentas de usuario del sistema.',
        'ruta' => 'usuarios.php',
        'imagen' => 'usuarios.svg',
        'administrador' => true,
        'lector' => false
    ],
    [
        'id' => 'cursos',
        'nombre' => 'Cursos',
        'descripcion' => 'Administra los cursos registrados en la institución.',
        'ruta' => 'cursos.php',
        'imagen' => 'cursos-verde.svg',
        'administrador' => true,
        'lector' => false
    ],
    [
        'id' => 'estudiantes',
        'nombre' => 'Estudiantes',
        'descripcion' => 'Consulta la información de los estudiantes registrados.',
        'ruta' => 'estudiantes.php',
        'imagen' => 'estudiantes-verde.svg',
        'administrador' => true,
        'lector' => true
    ],
    [
        'id' => 'brigadas',
        'nombre' => 'Brigadas',
        'descripcion' => 'Administra las brigadas de atención de emergencias.',
        'ruta' => 'brigadas.php',
        'imagen' => 'brigadas-verde.svg',
        'administrador' => true,
        'lector' => false
    ],
    [
        'id' => 'brigadistas',
        'nombre' => 'Brigadistas',
        'descripcion' => 'Consulta la información del personal de las brigadas.',
        'ruta' => 'brigadistas.php',
        'imagen' => 'brigadistas-verde.svg',
        'administrador' => true,
        'lector' => true
    ],
    [
        'id' => 'emergencias',
        'nombre' => 'Emergencias',
        'descripcion' => 'Consulta los contactos telefónicos de emergencia.',
        'ruta' => 'emergencias.php',
        'imagen' => 'emergencias-verde.svg',
        'administrador' => true,
        'lector' => true
    ]
];

// Filtrar los módulos que puede consultar el usuario.
$modulos_disponibles = [];

foreach ($modulos as $modulo) {

    if (
        ($rol_usuario === 'administrador' && $modulo['administrador']) ||
        ($rol_usuario === 'lector' && $modulo['lector'])
    ) {
        $modulos_disponibles[] = $modulo;
    }
}

// Escapar los valores que se muestran en HTML.
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

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/index.css">

    <title>Panel administrativo | RedVital Colvatenza</title>
</head>

<body>

    <div class="estructura-administrativa">

        <?php require_once __DIR__ . '/includes/menu.php'; ?>

        <main class="contenido-administrativo">

            <section class="seccion-opciones">

                <h2>Opciones disponibles</h2>

                <div class="opciones-panel">

                    <?php foreach ($modulos_disponibles as $modulo): ?>

                        <article class="tarjeta-opcion">

                            <img
                                src="../assets/images/<?= escapar_html($modulo['imagen']) ?>"
                                alt="<?= escapar_html($modulo['nombre']) ?>"
                                class="imagen-opcion"
                            >

                            <h3>
                                <?= escapar_html($modulo['nombre']) ?>
                            </h3>

                            <p>
                                <?= escapar_html($modulo['descripcion']) ?>
                            </p>

                            <a
                                href="<?= escapar_html($modulo['ruta']) ?>"
                                class="enlace-opcion"
                            >
                                <?= $modulo['id'] === 'usuarios'
                                    ? 'Administrar usuarios'
                                    : 'Ingresar al módulo' ?>
                            </a>

                        </article>

                    <?php endforeach; ?>

                </div>

            </section>
        </main>

    </div>

    <script src="../assets/js/admin/menu.js" defer></script>

</body>

</html>
