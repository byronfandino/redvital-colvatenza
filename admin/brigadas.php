<?php

$pagina_activa = 'brigadas';
$rol_requerido = 'administrador';

require_once __DIR__ . '/verificar_rol.php';
require_once __DIR__ . '/../config/database.php';

// Consultar las brigadas registradas.
try {
    $sql = "
        SELECT
            b.id_brigada,
            b.nombre,
            b.descripcion,
            b.color,
            b.icono,
            b.activo,
            b.creado_en,
            b.actualizado_en,
            uc.nombres AS creador_nombres,
            uc.apellidos AS creador_apellidos,
            ua.nombres AS actualizador_nombres,
            ua.apellidos AS actualizador_apellidos
        FROM brigadas b
        INNER JOIN usuarios uc
            ON b.creado_por = uc.id_usuario
        LEFT JOIN usuarios ua
            ON b.actualizado_por = ua.id_usuario
        ORDER BY b.id_brigada ASC
    ";

    $consulta = $pdo->query($sql);
    $brigadas = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $error_consulta = false;

} catch (PDOException $e) {
    error_log(
        'Error al consultar brigadas en RedVital Colvatenza: ' .
        $e->getMessage()
    );

    $brigadas = [];
    $error_consulta = true;
}

// Recuperar el mensaje de estado.
$mensaje_estado = $_SESSION['mensaje_estado'] ?? '';
$tipo_mensaje_estado = $_SESSION['tipo_mensaje_estado'] ?? 'exito';

unset($_SESSION['mensaje_estado']);
unset($_SESSION['tipo_mensaje_estado']);

// Escapar los valores mostrados en HTML.
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

    <title>Gestión de brigadas | RedVital Colvatenza</title>

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/brigadas.css">
</head>

<body>

    <div class="estructura-administrativa">

        <?php require_once __DIR__ . '/includes/menu.php'; ?>

        <main class="contenido-administrativo">

            <section class="gestion-brigadas">

                <header class="encabezado-brigadas">
                    <div>
                        <h1>Gestión de brigadas</h1>
                        <p>
                            Consulta y administra las brigadas de emergencia
                            de RedVital Colvatenza.
                        </p>
                    </div>

                    <a href="crear_brigada.php" class="boton-principal">
                        Crear brigada
                    </a>
                </header>

                <div class="contenido-brigadas">

                    <div class="resumen-brigadas">
                        <div>
                            <h2>Brigadas registradas</h2>
                            <p>
                                Total registros:
                                <strong><?= count($brigadas) ?></strong>
                            </p>
                        </div>
                    </div>

                    <?php if ($mensaje_estado !== ''): ?>

                        <div
                            class="mensaje-formulario <?= $tipo_mensaje_estado === 'exito'
                                ? 'mensaje-exito'
                                : 'mensaje-error' ?>"
                            role="alert"
                        >
                            <?= escapar_html($mensaje_estado) ?>
                        </div>

                    <?php endif; ?>

                    <?php if ($error_consulta): ?>

                        <div class="mensaje-formulario mensaje-error" role="alert">
                            No fue posible consultar las brigadas.
                            Inténtalo nuevamente.
                        </div>

                    <?php elseif (empty($brigadas)): ?>

                        <div class="mensaje-vacio">
                            <h3>No hay brigadas registradas</h3>
                            <p>
                                Todavía no existen brigadas para mostrar.
                                Puedes registrar la primera mediante el botón superior.
                            </p>
                        </div>

                    <?php else: ?>

                        <div class="tabla-contenedor">

                            <table class="tabla-brigadas">

                                <thead>
                                    <tr>
                                        <!-- <th>ID</th> -->
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Color</th>
                                        <th>Icono</th>
                                        <th>Estado</th>
                                        <th>Creado por</th>
                                        <th>Actualizado por</th>
                                        <th>Fecha de creación</th>
                                        <th>Fecha de actualización</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php foreach ($brigadas as $brigada): ?>

                                        <tr>

                                            <td>
                                                <?= escapar_html($brigada['nombre']) ?>
                                            </td>

                                            <td class="descripcion-brigada">
                                                <?= escapar_html($brigada['descripcion']) ?>
                                            </td>

                                            <td>
                                                <div class="muestra-color">
                                                    <span
                                                        class="muestra-color-circulo"
                                                        style="background-color: <?= escapar_html($brigada['color']) ?>;"
                                                    ></span>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="icono-brigada">
                                                    <img
                                                        src="../assets/images/<?= rawurlencode(basename($brigada['icono'])) ?>"
                                                        alt="<?= escapar_html($brigada['nombre']) ?>"
                                                        loading="lazy"
                                                    >
                                                </div>
                                            </td>

                                            <td>
                                                <?php if ((int) $brigada['activo'] === 1): ?>

                                                    <span class="etiqueta-estado estado-activo">
                                                        Activa
                                                    </span>

                                                <?php else: ?>

                                                    <span class="etiqueta-estado estado-inactivo">
                                                        Inactiva
                                                    </span>

                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?= escapar_html(
                                                    $brigada['creador_nombres'] . ' ' .
                                                    $brigada['creador_apellidos']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($brigada['actualizador_nombres'])): ?>

                                                    <?= escapar_html(
                                                        $brigada['actualizador_nombres'] . ' ' .
                                                        $brigada['actualizador_apellidos']
                                                    ) ?>

                                                <?php else: ?>

                                                    <span class="dato-sin-registro">
                                                        Sin actualizar
                                                    </span>

                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($brigada['creado_en'])): ?>

                                                    <?= escapar_html(
                                                        date(
                                                            'd/m/Y H:i',
                                                            strtotime($brigada['creado_en'])
                                                        )
                                                    ) ?>

                                                <?php else: ?>

                                                    <span class="dato-sin-registro">
                                                        Sin registro
                                                    </span>

                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($brigada['actualizado_en'])): ?>

                                                    <?= escapar_html(
                                                        date(
                                                            'd/m/Y H:i',
                                                            strtotime($brigada['actualizado_en'])
                                                        )
                                                    ) ?>

                                                <?php else: ?>

                                                    <span class="dato-sin-registro">
                                                        Sin actualizar
                                                    </span>

                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <div class="acciones-brigada">

                                                    <a
                                                        href="editar_brigada.php?id=<?= (int) $brigada['id_brigada'] ?>"
                                                        class="boton-accion boton-editar"
                                                    >
                                                        Editar
                                                    </a>

                                                    <form
                                                        action="cambiar_estado_brigada.php"
                                                        method="POST"
                                                        class="formulario-accion"
                                                    >
                                                        <input
                                                            type="hidden"
                                                            name="id_brigada"
                                                            value="<?= (int) $brigada['id_brigada'] ?>"
                                                        >

                                                        <?php if ((int) $brigada['activo'] === 1): ?>

                                                            <input
                                                                type="hidden"
                                                                name="activo"
                                                                value="0"
                                                            >

                                                            <button
                                                                type="submit"
                                                                class="boton-accion boton-desactivar"
                                                            >
                                                                Desactivar
                                                            </button>

                                                        <?php else: ?>

                                                            <input
                                                                type="hidden"
                                                                name="activo"
                                                                value="1"
                                                            >

                                                            <button
                                                                type="submit"
                                                                class="boton-accion boton-activar"
                                                            >
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

                    <?php endif; ?>

                </div>

            </section>

        </main>

    </div>

    <script src="../assets/js/admin/menu.js" defer></script>

</body>

</html>
