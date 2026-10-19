<?php

session_start();

require_once __DIR__ . '/proteger.php';
require_once __DIR__ . '/../config/database.php';

// Identificar la página activa y el rol del usuario.
$pagina_activa = 'emergencias';

$rol_usuario = $_SESSION['rol'] ?? '';
$es_administrador = $rol_usuario === 'administrador';

// Preparar la función para escapar contenido HTML.
function escapar_html($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// Recuperar el mensaje de sesión y eliminarlo para mostrarlo una sola vez.
$mensaje_sesion = $_SESSION['mensaje_emergencias'] ?? null;
unset($_SESSION['mensaje_emergencias']);

// Inicializar las variables de la página.
$emergencias = [];
$error_consulta = '';

// Consultar los contactos de emergencia y sus datos de auditoría.
try {

    $consulta = "
        SELECT
            e.id_emergencia,
            e.nombre,
            e.telefono,
            e.activo,
            e.creado_por,
            e.creado_en,
            e.actualizado_por,
            e.actualizado_en,
            CONCAT_WS(' ', uc.nombres, uc.apellidos) AS nombre_creador,
            CONCAT_WS(' ', ua.nombres, ua.apellidos) AS nombre_actualizador
        FROM emergencia AS e
        LEFT JOIN usuarios AS uc
            ON e.creado_por = uc.id_usuario
        LEFT JOIN usuarios AS ua
            ON e.actualizado_por = ua.id_usuario
        ORDER BY
            e.nombre ASC
    ";

    $resultado = $pdo->query($consulta);
    $emergencias = $resultado->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    error_log(
        'Error al consultar contactos de emergencia: '
        . $e->getMessage()
    );

    $error_consulta = 'No fue posible cargar los contactos de emergencia.';

}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de emergencias | RedVital Colvatenza</title>

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/emergencias.css">
</head>

<body>

    <div class="estructura-administrativa">

        <?php require_once __DIR__ . '/includes/menu.php'; ?>

        <main class="contenido-administrativo">

            <section class="gestion-emergencias">

                <header class="encabezado-emergencias">

                    <div>
                        <h1>Gestión de emergencias</h1>

                        <p>
                            Administra los contactos telefónicos para la
                            atención de emergencias de RedVital Colvatenza.
                        </p>
                    </div>

                    <?php if ($es_administrador): ?>

                        <a
                            href="crear_emergencia.php"
                            class="boton-principal"
                        >
                            Registrar contacto
                        </a>

                    <?php endif; ?>

                </header>

                <?php if (is_array($mensaje_sesion) && !empty($mensaje_sesion['texto'])): ?>

                    <?php
                    $tipo_alerta = $mensaje_sesion['tipo'] ?? 'exito';

                    $clase_alerta = $tipo_alerta === 'error'
                        ? 'alerta-error'
                        : 'alerta-exito';
                    ?>

                    <div
                        class="alerta-emergencias <?= escapar_html($clase_alerta) ?>"
                        role="<?= $tipo_alerta === 'error' ? 'alert' : 'status' ?>"
                        aria-live="<?= $tipo_alerta === 'error' ? 'assertive' : 'polite' ?>"
                    >
                        <?= escapar_html($mensaje_sesion['texto']) ?>
                    </div>

                <?php endif; ?>

                <?php if ($error_consulta !== ''): ?>

                    <div class="alerta-emergencias alerta-error" role="alert">
                        <?= escapar_html($error_consulta) ?>
                    </div>

                <?php else: ?>

                    <section class="contenido-emergencias">

                        <div class="encabezado-tabla-emergencias">

                            <div>
                                <h2>Directorio de contactos</h2>

                                <p>
                                    Consulta los números telefónicos y el
                                    estado de cada contacto registrado.
                                </p>
                            </div>

                        </div>

                        <div class="filtros-emergencias">

                            <div class="campo-busqueda">
                                <label for="buscar-nombre-emergencia">
                                    Buscar por nombre
                                </label>

                                <input
                                    type="search"
                                    id="buscar-nombre-emergencia"
                                    placeholder="Ej. Bomberos"
                                    autocomplete="off"
                                >
                            </div>
                            <button
                                type="button"
                                id="limpiar-filtros-emergencias"
                                class="boton-secundario"
                            >
                                Limpiar filtros
                            </button>

                        </div>

                        <p
                            class="resultado-busqueda"
                            id="resultado-busqueda-emergencias"
                            aria-live="polite"
                        >
                            Se muestran <?= count($emergencias) ?>
                            contactos.
                        </p>

                        <div class="tabla-contenedor">

                            <table class="tabla-emergencias">

                                <thead>
                                    <tr>
                                        
                                        <th>Nombre del contacto</th>
                                        <th>Teléfono</th>
                                        <th>Estado</th>
                                        <th>Fecha de registro</th>
                                        <th>Registrado por</th>
                                        <th>Última actualización</th>
                                        <th>Actualizado por</th>

                                        <?php if ($es_administrador): ?>
                                            <th>Acciones</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>

                                <tbody id="cuerpo-tabla-emergencias">

                                    <?php if (empty($emergencias)): ?>

                                        <tr>
                                            <td
                                                colspan="<?= $es_administrador ? 9 : 8 ?>"
                                                class="mensaje-vacio"
                                            >
                                                No hay contactos de emergencia
                                                registrados.
                                            </td>
                                        </tr>

                                    <?php else: ?>

                                        <?php foreach ($emergencias as $emergencia): ?>

                                            <tr
                                                data-nombre="<?= escapar_html(mb_strtolower($emergencia['nombre'], 'UTF-8')) ?>"
                                                data-telefono="<?= escapar_html($emergencia['telefono']) ?>"
                                            >

                                                

                                                <td>
                                                    <?= escapar_html($emergencia['nombre']) ?>
                                                </td>

                                                <td>
                                                    <a
                                                        href="tel:<?= escapar_html(preg_replace('/[^0-9+]/', '', $emergencia['telefono'])) ?>"
                                                        class="enlace-telefono"
                                                    >
                                                        <?= escapar_html($emergencia['telefono']) ?>
                                                    </a>
                                                </td>

                                                <td>
                                                    <?php if ((int) $emergencia['activo'] === 1): ?>

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
                                                    <?= escapar_html(
                                                        date(
                                                            'd/m/Y H:i',
                                                            strtotime($emergencia['creado_en'])
                                                        )
                                                    ) ?>
                                                </td>

                                                <td>
                                                    <?php
                                                    $nombre_creador = trim(
                                                        $emergencia['nombre_creador'] ?? ''
                                                    );
                                                    ?>

                                                    <?php if ($nombre_creador !== ''): ?>

                                                        <?= escapar_html($nombre_creador) ?>

                                                    <?php else: ?>

                                                        <span class="dato-sin-registro">
                                                            Usuario no disponible
                                                        </span>

                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <?php if (!empty($emergencia['actualizado_en'])): ?>

                                                        <?= escapar_html(
                                                            date(
                                                                'd/m/Y H:i',
                                                                strtotime($emergencia['actualizado_en'])
                                                            )
                                                        ) ?>

                                                    <?php else: ?>

                                                        <span class="dato-sin-registro">
                                                            Sin actualizar
                                                        </span>

                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <?php
                                                    $nombre_actualizador = trim(
                                                        $emergencia['nombre_actualizador'] ?? ''
                                                    );
                                                    ?>

                                                    <?php if (empty($emergencia['actualizado_en'])): ?>

                                                        <span class="dato-sin-registro">
                                                            Sin actualizar
                                                        </span>

                                                    <?php elseif ($nombre_actualizador !== ''): ?>

                                                        <?= escapar_html($nombre_actualizador) ?>

                                                    <?php else: ?>

                                                        <span class="dato-sin-registro">
                                                            Usuario no disponible
                                                        </span>

                                                    <?php endif; ?>
                                                </td>

                                                <?php if ($es_administrador): ?>

                                                    <td>
                                                        <div class="acciones-emergencia">

                                                            <a
                                                                href="editar_emergencia.php?id=<?= (int) $emergencia['id_emergencia'] ?>"
                                                                class="boton-accion boton-editar"
                                                            >
                                                                Editar
                                                            </a>

                                                            <form
                                                                action="cambiar_estado_emergencia.php"
                                                                method="POST"
                                                                class="formulario-accion"
                                                            >

                                                                <input
                                                                    type="hidden"
                                                                    name="id_emergencia"
                                                                    value="<?= (int) $emergencia['id_emergencia'] ?>"
                                                                >

                                                                <?php if ((int) $emergencia['activo'] === 1): ?>

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

                                                <?php endif; ?>

                                            </tr>

                                        <?php endforeach; ?>

                                        <tr
                                            id="mensaje-sin-resultados"
                                            hidden
                                        >
                                            <td
                                                colspan="<?= $es_administrador ? 9 : 8 ?>"
                                                class="mensaje-vacio"
                                            >
                                                No se encontraron contactos
                                                que coincidan con la búsqueda.
                                            </td>
                                        </tr>

                                    <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </section>

                <?php endif; ?>

            </section>

        </main>

    </div>

    <script src="../assets/js/admin/menu.js" defer></script>
    <script src="../assets/js/admin/emergencias.js" defer></script>

</body>

</html>
