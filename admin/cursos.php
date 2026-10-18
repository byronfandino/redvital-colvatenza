<?php

$pagina_activa = 'cursos';
$rol_requerido = 'administrador';

require_once __DIR__ . '/verificar_rol.php';
require_once __DIR__ . '/../config/database.php';

// Consultar los cursos junto con los usuarios que los crearon y actualizaron
$consulta = "
    SELECT
        c.id_curso,
        c.grado,
        c.grupo,
        c.activo,
        c.creado_en,
        c.actualizado_en,
        CONCAT_WS(' ', uc.nombres, uc.apellidos) AS nombre_creador,
        CONCAT_WS(' ', ua.nombres, ua.apellidos) AS nombre_actualizador
    FROM cursos AS c
    LEFT JOIN usuarios AS uc
        ON c.creado_por = uc.id_usuario
    LEFT JOIN usuarios AS ua
        ON c.actualizado_por = ua.id_usuario
    ORDER BY
        CAST(c.grado AS UNSIGNED) ASC,
        CAST(c.grupo AS UNSIGNED) ASC
";

$resultado = $pdo->query($consulta);
$cursos = $resultado->fetchAll(PDO::FETCH_ASSOC);

// Recuperar los mensajes de estado
$mensaje_estado = $_SESSION['mensaje_estado'] ?? '';
$tipo_mensaje_estado = $_SESSION['tipo_mensaje_estado'] ?? 'exito';

unset($_SESSION['mensaje_estado']);
unset($_SESSION['tipo_mensaje_estado']);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de cursos | RedVital Colvatenza</title>

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/cursos.css">
</head>

<body>

    <div class="estructura-administrativa">

        <?php require_once __DIR__ . '/includes/menu.php'; ?>

        <main class="contenido-administrativo">

            <section class="gestion-cursos">

                <header class="encabezado-cursos">
                    <div>
                        <h1>Gestión de cursos</h1>
                        <p>Administra los grados y grupos de la sede campestre.</p>
                    </div>

                    <a href="crear_curso.php" class="boton-principal">
                        Crear curso
                    </a>
                </header>

                <?php if (!empty($mensaje_estado)): ?>
                    <div class="mensaje-formulario mensaje-<?php echo htmlspecialchars($tipo_mensaje_estado, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($mensaje_estado, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <div class="contenido-cursos">

                    <?php if (count($cursos) > 0): ?>

                        <div class="tabla-contenedor">

                            <table class="tabla-cursos">

                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Grado</th>
                                        <th>Grupo</th>
                                        <th>Estado</th>
                                        <th>Fecha de creación</th>
                                        <th>Creado por</th>
                                        <th>Fecha de actualización</th>
                                        <th>Actualizado por</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php foreach ($cursos as $curso): ?>

                                        <tr>
                                            <td>
                                                <?php echo (int) $curso['id_curso']; ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($curso['grado'], ENT_QUOTES, 'UTF-8'); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($curso['grupo'], ENT_QUOTES, 'UTF-8'); ?>
                                            </td>

                                            <td>
                                                <?php if ((int) $curso['activo'] === 1): ?>
                                                    <span class="etiqueta-estado estado-activo">
                                                        Activo
                                                    </span>
                                                <?php else: ?>
                                                    <span class="etiqueta-estado estado-inactivo">
                                                        Inactivo
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo htmlspecialchars(
                                                    date('d/m/Y H:i', strtotime($curso['creado_en'])),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                $nombre_creador = trim($curso['nombre_creador'] ?? '');

                                                if ($nombre_creador !== '') {
                                                    echo htmlspecialchars($nombre_creador, ENT_QUOTES, 'UTF-8');
                                                } else {
                                                    echo '<span class="dato-sin-registro">Usuario no disponible</span>';
                                                }
                                                ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($curso['actualizado_en'])): ?>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        date('d/m/Y H:i', strtotime($curso['actualizado_en'])),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                    ?>
                                                <?php else: ?>
                                                    <span class="dato-sin-registro">
                                                        Sin actualizar
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php
                                                $nombre_actualizador = trim($curso['nombre_actualizador'] ?? '');

                                                if (empty($curso['actualizado_en'])) {
                                                    echo '<span class="dato-sin-registro">Sin actualizar</span>';
                                                } elseif ($nombre_actualizador !== '') {
                                                    echo htmlspecialchars($nombre_actualizador, ENT_QUOTES, 'UTF-8');
                                                } else {
                                                    echo '<span class="dato-sin-registro">Usuario no disponible</span>';
                                                }
                                                ?>
                                            </td>

                                            <td>
                                                <div class="acciones-curso">

                                                    <a
                                                        href="editar_curso.php?id=<?php echo (int) $curso['id_curso']; ?>"
                                                        class="boton-accion boton-editar">
                                                        Editar
                                                    </a>

                                                    <form
                                                        action="cambiar_estado_curso.php"
                                                        method="POST"
                                                        class="formulario-accion">

                                                        <input
                                                            type="hidden"
                                                            name="id_curso"
                                                            value="<?php echo (int) $curso['id_curso']; ?>">

                                                        <?php if ((int) $curso['activo'] === 1): ?>

                                                            <input
                                                                type="hidden"
                                                                name="activo"
                                                                value="0">

                                                            <button
                                                                type="submit"
                                                                class="boton-accion boton-desactivar">
                                                                Desactivar
                                                            </button>

                                                        <?php else: ?>

                                                            <input
                                                                type="hidden"
                                                                name="activo"
                                                                value="1">

                                                            <button
                                                                type="submit"
                                                                class="boton-accion boton-activar">
                                                                Activar
                                                            </button>

                                                        <?php endif; ?>

                                                    </form>

                                                </div>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php else: ?>

                        <p class="mensaje-vacio">
                            No hay cursos registrados.
                        </p>

                    <?php endif; ?>

                </div>

            </section>

        </main>

    </div>

    <script src="../assets/js/admin/menu.js"></script>

</body>

</html>
