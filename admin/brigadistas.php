
<?php

session_start();

require_once __DIR__ . '/proteger.php';
require_once __DIR__ . '/../config/database.php';

$pagina_activa = 'brigadistas';

$rol_usuario = $_SESSION['rol'] ?? '';
$es_administrador = $rol_usuario === 'administrador';

// Escapar valores antes de mostrarlos en HTML.
function escapar_html($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// Consultar los brigadistas y los datos relacionados.
$consulta = "
    SELECT
        b.id_brigadista,
        b.id_brigada,
        br.nombre AS nombre_brigada,
        br.color AS color_brigada,
        b.id_tipo_documento,
        td.nombre AS tipo_documento,
        td.abreviatura AS abreviatura_documento,
        b.numero_documento,
        b.nombres,
        b.apellidos,
        b.dependencia,
        b.celular,
        b.activo,
        b.creado_en,
        b.actualizado_en,
        CONCAT_WS(' ', uc.nombres, uc.apellidos) AS nombre_creador,
        CONCAT_WS(' ', ua.nombres, ua.apellidos) AS nombre_actualizador
    FROM brigadistas AS b
    INNER JOIN brigadas AS br
        ON b.id_brigada = br.id_brigada
    INNER JOIN tipos_documento AS td
        ON b.id_tipo_documento = td.id_tipo_documento
    LEFT JOIN usuarios AS uc
        ON b.creado_por = uc.id_usuario
    LEFT JOIN usuarios AS ua
        ON b.actualizado_por = ua.id_usuario
    ORDER BY
        b.apellidos ASC,
        b.nombres ASC
";

$resultado = $pdo->query($consulta);
$brigadistas = $resultado->fetchAll(PDO::FETCH_ASSOC);

// Consultar las brigadas para el filtro.
$consulta_brigadas = "
    SELECT id_brigada, nombre, color
    FROM brigadas
    ORDER BY nombre ASC
";

$resultado_brigadas = $pdo->query($consulta_brigadas);
$brigadas_filtro = $resultado_brigadas->fetchAll(PDO::FETCH_ASSOC);

// Recuperar mensajes de estado.
$mensaje_estado = $_SESSION['mensaje_estado'] ?? '';
$tipo_mensaje_estado = $_SESSION['tipo_mensaje_estado'] ?? 'exito';

unset($_SESSION['mensaje_estado'], $_SESSION['tipo_mensaje_estado']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de brigadistas | RedVital Colvatenza</title>

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/brigadistas.css">
</head>

<body>

<div class="estructura-administrativa">

    <?php require_once __DIR__ . '/includes/menu.php'; ?>

    <main class="contenido-administrativo">

        <section class="gestion-brigadistas">

            <header class="encabezado-brigadistas">
                <div>
                    <h1>Gestión de brigadistas</h1>
                    <p>Consulta y administra la información de los integrantes de las brigadas de la sede campestre.</p>
                </div>

                <?php if ($es_administrador): ?>
                    <a href="crear_brigadista.php" class="boton-principal">
                        Crear brigadista
                    </a>
                <?php endif; ?>
            </header>

            <?php if ($mensaje_estado !== ''): ?>
                <div class="mensaje-formulario mensaje-<?= escapar_html($tipo_mensaje_estado) ?>">
                    <?= escapar_html($mensaje_estado) ?>
                </div>
            <?php endif; ?>

            <div class="contenido-brigadistas">

                <div class="resumen-brigadistas">
                    <div>
                        <h2>Brigadistas registrados</h2>
                        <p>Busca por nombre, documento o brigada.</p>
                    </div>

                    <strong>
                        Total: <span id="total-brigadistas"><?= count($brigadistas) ?></span>
                    </strong>
                </div>

                <div class="filtros-brigadistas">

                    <div class="campo-filtro">
                        <label for="buscar-nombre">Buscar por nombre</label>

                        <input
                            type="search"
                            id="buscar-nombre"
                            placeholder="Nombres o apellidos..."
                            autocomplete="off"
                        >
                    </div>

                    <div class="campo-filtro">
                        <label for="buscar-documento">Número de documento</label>

                        <input
                            type="search"
                            id="buscar-documento"
                            placeholder="Escribe el documento..."
                            autocomplete="off"
                        >
                    </div>

                    <div class="campo-filtro">
                        <label for="filtrar-brigada">Filtrar por brigada</label>

                        <select id="filtrar-brigada">
                            <option value="">Todas las brigadas</option>

                            <?php foreach ($brigadas_filtro as $brigada_filtro): ?>
                                <option value="<?= (int) $brigada_filtro['id_brigada'] ?>">
                                    <?= escapar_html($brigada_filtro['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="button" id="limpiar-filtros" class="boton-limpiar">
                        Limpiar filtros
                    </button>

                </div>

                <div class="resultado-busqueda" id="resultado-busqueda" aria-live="polite"></div>

                <?php if (count($brigadistas) > 0): ?>

                    <div class="tabla-contenedor">

                        <table class="tabla-brigadistas">

                            <thead>
                                <tr>
                                    <th>Tipo de documento</th>
                                    <th>Número de documento</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Brigada</th>
                                    <th>Dependencia</th>
                                    <th>Celular</th>
                                    <th>Estado</th>
                                    <th>Fecha de creación</th>
                                    <th>Creado por</th>
                                    <th>Fecha de actualización</th>
                                    <th>Actualizado por</th>

                                    <?php if ($es_administrador): ?>
                                        <th>Acciones</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>

                            <tbody id="cuerpo-tabla-brigadistas">

                                <?php foreach ($brigadistas as $brigadista): ?>

                                    <tr
                                        data-brigadista
                                        data-nombres="<?= escapar_html($brigadista['nombres']) ?>"
                                        data-apellidos="<?= escapar_html($brigadista['apellidos']) ?>"
                                        data-documento="<?= escapar_html($brigadista['numero_documento']) ?>"
                                        data-brigada="<?= (int) $brigadista['id_brigada'] ?>"
                                    >

                                        <td>
                                            <?= escapar_html(
                                                $brigadista['abreviatura_documento'] ?: $brigadista['tipo_documento']
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= escapar_html($brigadista['numero_documento']) ?>
                                        </td>

                                        <td>
                                            <?= escapar_html($brigadista['nombres']) ?>
                                        </td>

                                        <td>
                                            <?= escapar_html($brigadista['apellidos']) ?>
                                        </td>

                                        <td>
                                            <span
                                                class="etiqueta-brigada"
                                                style="background-color: <?= escapar_html($brigadista['color_brigada']) ?>;"
                                            >
                                                <?= escapar_html($brigadista['nombre_brigada']) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= escapar_html($brigadista['dependencia']) ?>
                                        </td>

                                        <td>
                                            <?= escapar_html($brigadista['celular']) ?>
                                        </td>

                                        <td>
                                            <?php if ((int) $brigadista['activo'] === 1): ?>
                                                <span class="etiqueta-estado estado-activo">Activo</span>
                                            <?php else: ?>
                                                <span class="etiqueta-estado estado-inactivo">Inactivo</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= !empty($brigadista['creado_en'])
                                                ? escapar_html(date('d/m/Y H:i', strtotime($brigadista['creado_en'])))
                                                : '<span class="dato-sin-registro">Sin registrar</span>' ?>
                                        </td>

                                        <td>
                                            <?= !empty($brigadista['nombre_creador'])
                                                ? escapar_html($brigadista['nombre_creador'])
                                                : '<span class="dato-sin-registro">Usuario no disponible</span>' ?>
                                        </td>

                                        <td>
                                            <?= !empty($brigadista['actualizado_en'])
                                                ? escapar_html(date('d/m/Y H:i', strtotime($brigadista['actualizado_en'])))
                                                : '<span class="dato-sin-registro">Sin actualizar</span>' ?>
                                        </td>

                                        <td>
                                            <?= !empty($brigadista['actualizado_en']) && !empty($brigadista['nombre_actualizador'])
                                                ? escapar_html($brigadista['nombre_actualizador'])
                                                : '<span class="dato-sin-registro">Sin actualizar</span>' ?>
                                        </td>

                                        <?php if ($es_administrador): ?>
                                            <td>
                                                <div class="acciones-brigadista">

                                                    <a
                                                        href="editar_brigadista.php?id=<?= (int) $brigadista['id_brigadista'] ?>"
                                                        class="boton-accion boton-editar"
                                                    >
                                                        Editar
                                                    </a>

                                                    <form
                                                        action="cambiar_estado_brigadista.php"
                                                        method="POST"
                                                        class="formulario-accion"
                                                    >
                                                        <input
                                                            type="hidden"
                                                            name="id_brigadista"
                                                            value="<?= (int) $brigadista['id_brigadista'] ?>"
                                                        >

                                                        <?php if ((int) $brigadista['activo'] === 1): ?>
                                                            <input type="hidden" name="activo" value="0">

                                                            <button
                                                                type="submit"
                                                                class="boton-accion boton-desactivar"
                                                            >
                                                                Desactivar
                                                            </button>
                                                        <?php else: ?>
                                                            <input type="hidden" name="activo" value="1">

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
                                        <?php endif; ?>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <p class="mensaje-vacio" id="sin-resultados" hidden>
                        No se encontraron brigadistas con los filtros seleccionados.
                    </p>

                <?php else: ?>

                    <p class="mensaje-vacio">
                        No hay brigadistas registrados.
                    </p>

                <?php endif; ?>

            </div>

        </section>

    </main>

</div>

<script src="../assets/js/admin/menu.js"></script>
<script src="../assets/js/admin/brigadistas.js"></script>

</body>
</html>
