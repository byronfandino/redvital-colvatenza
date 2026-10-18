<?php

session_start();

require_once __DIR__ . '/proteger.php';
require_once __DIR__ . '/../config/database.php';

$pagina_activa = 'estudiantes';

$rol_usuario = $_SESSION['rol'] ?? '';
$es_administrador = $rol_usuario === 'administrador';

function escapar_html($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// Consultar los estudiantes junto con el curso y los usuarios de auditoría.
$consulta = "
    SELECT
        e.id_estudiante,
        e.id_tipo_documento,
        td.nombre AS tipo_documento,
        td.abreviatura AS abreviatura_documento,
        e.numero_documento,
        e.nombres,
        e.apellidos,
        e.fecha_nacimiento,
        e.id_curso,
        c.grado,
        c.grupo,
        e.tipo_sangre,
        e.direccion_residencia,
        e.telefono_contacto,
        e.condiciones_especiales,
        e.activo,
        e.creado_en,
        e.actualizado_en,
        e.creado_por,
        e.actualizado_por,
        CONCAT_WS(' ', uc.nombres, uc.apellidos) AS nombre_creador,
        CONCAT_WS(' ', ua.nombres, ua.apellidos) AS nombre_actualizador
    FROM estudiantes AS e
    INNER JOIN tipos_documento AS td
        ON e.id_tipo_documento = td.id_tipo_documento
    INNER JOIN cursos AS c
        ON e.id_curso = c.id_curso
    LEFT JOIN usuarios AS uc
        ON e.creado_por = uc.id_usuario
    LEFT JOIN usuarios AS ua
        ON e.actualizado_por = ua.id_usuario
    ORDER BY
        CAST(c.grado AS UNSIGNED) ASC,
        CAST(c.grupo AS UNSIGNED) ASC,
        e.apellidos ASC,
        e.nombres ASC
";

$resultado = $pdo->query($consulta);
$estudiantes = $resultado->fetchAll(PDO::FETCH_ASSOC);

// Consultar todos los acudientes y organizarlos por estudiante.
$consulta_acudientes = "
    SELECT
        ea.id_estudiante,
        ea.parentesco,
        a.id_acudiente,
        a.numero_documento,
        a.nombres,
        a.apellidos,
        a.telefono_contacto,
        a.correo_electronico,
        a.direccion_residencia
    FROM estudiante_acudiente AS ea
    INNER JOIN acudientes AS a
        ON ea.id_acudiente = a.id_acudiente
    ORDER BY ea.id_estudiante ASC, ea.id_estudiante_acudiente ASC
";

$resultado_acudientes = $pdo->query($consulta_acudientes);
$acudientes_por_estudiante = [];

while ($acudiente = $resultado_acudientes->fetch(PDO::FETCH_ASSOC)) {
    $id_estudiante = (int) $acudiente['id_estudiante'];

    if (!isset($acudientes_por_estudiante[$id_estudiante])) {
        $acudientes_por_estudiante[$id_estudiante] = [];
    }

    $acudientes_por_estudiante[$id_estudiante][] = $acudiente;
}

// Añadir el primer acudiente a cada registro para mostrarlo en la tabla.
foreach ($estudiantes as &$estudiante) {
    $id_estudiante = (int) $estudiante['id_estudiante'];
    $lista_acudientes = $acudientes_por_estudiante[$id_estudiante] ?? [];

    $estudiante['acudientes'] = $lista_acudientes;
    $estudiante['primer_acudiente'] = $lista_acudientes[0] ?? null;
}
unset($estudiante);

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

    <title>Gestión de estudiantes | RedVital Colvatenza</title>

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/estudiantes.css">
</head>

<body>

<div class="estructura-administrativa">

    <?php require_once __DIR__ . '/includes/menu.php'; ?>

    <main class="contenido-administrativo">

        <section class="gestion-estudiantes">

            <header class="encabezado-estudiantes">
                <div>
                    <h1>Gestión de estudiantes</h1>
                    <p>Consulta y administra la información de los estudiantes de la sede campestre.</p>
                </div>

                <?php if ($es_administrador): ?>
                    <a href="crear_estudiante.php" class="boton-principal">
                        Crear estudiante
                    </a>
                <?php endif; ?>
            </header>

            <?php if (!empty($mensaje_estado)): ?>
                <div class="mensaje-formulario mensaje-<?= escapar_html($tipo_mensaje_estado) ?>">
                    <?= escapar_html($mensaje_estado) ?>
                </div>
            <?php endif; ?>

            <div class="contenido-estudiantes">

                <div class="resumen-estudiantes">
                    <div>
                        <h2>Estudiantes registrados</h2>
                        <p>Busca por documento, nombre, apellido o grado.</p>
                    </div>

                    <strong>
                        Total: <span id="total-estudiantes"><?= count($estudiantes) ?></span>
                    </strong>
                </div>

                <div class="filtros-estudiantes">

                    <div class="campo-busqueda">
                        <label for="buscar-estudiante">Buscar estudiante</label>
                        <input
                            type="search"
                            id="buscar-estudiante"
                            placeholder="Documento, nombres o apellidos..."
                            autocomplete="off"
                        >
                    </div>

                    <div class="campo-filtro-grado">
                        <label for="filtrar-grado">Filtrar por curso</label>

                        <select id="filtrar-grado">
                            <option value="">Todos los cursos</option>

                            <?php
                            $cursos_filtro = [];

                            foreach ($estudiantes as $estudiante) {
                                $grado = (string) $estudiante['grado'];
                                $grupo = (string) $estudiante['grupo'];

                                $curso = $grado . '-' . $grupo;
                                $cursos_filtro[$curso] = $curso;
                            }

                            uksort($cursos_filtro, function ($a, $b) {
                                [$grado_a, $grupo_a] = array_map('intval', explode('-', $a));
                                [$grado_b, $grupo_b] = array_map('intval', explode('-', $b));

                                return ($grado_a <=> $grado_b) ?: ($grupo_a <=> $grupo_b);
                            });
                            ?>

                            <?php foreach ($cursos_filtro as $curso): ?>
                                <option value="<?= escapar_html($curso) ?>">
                                    <?= escapar_html($curso) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="button" id="limpiar-filtros" class="boton-limpiar">
                        Limpiar filtros
                    </button>

                </div>

                <div class="resultado-busqueda" id="resultado-busqueda" aria-live="polite"></div>

                <?php if (count($estudiantes) > 0): ?>

                    <div class="tabla-contenedor">
                        <table class="tabla-estudiantes">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Curso</th>
                                    <th>Teléfono estudiante</th>
                                    <th>Primer acudiente</th>
                                    <th>Teléfono acudiente</th>
                                    <th>Activo</th>
                                    <th>Fecha de creación</th>
                                    <th>Creado por</th>
                                    <th>Fecha de actualización</th>
                                    <th>Actualizado por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="cuerpo-tabla-estudiantes">

                                <?php foreach ($estudiantes as $estudiante): ?>

                                    <?php
                                    $primer_acudiente = $estudiante['primer_acudiente'];

                                    $nombre_primer_acudiente = $primer_acudiente
                                        ? trim($primer_acudiente['nombres'] . ' ' . $primer_acudiente['apellidos'])
                                        : '';

                                    $detalles_estudiante = [
                                        'id_estudiante' => $estudiante['id_estudiante'],
                                        'tipo_documento' => $estudiante['tipo_documento'],
                                        'abreviatura_documento' => $estudiante['abreviatura_documento'],
                                        'numero_documento' => $estudiante['numero_documento'],
                                        'nombres' => $estudiante['nombres'],
                                        'apellidos' => $estudiante['apellidos'],
                                        'fecha_nacimiento' => $estudiante['fecha_nacimiento'],
                                        'grado' => $estudiante['grado'],
                                        'grupo' => $estudiante['grupo'],
                                        'tipo_sangre' => $estudiante['tipo_sangre'],
                                        'direccion_residencia' => $estudiante['direccion_residencia'],
                                        'telefono_contacto' => $estudiante['telefono_contacto'],
                                        'condiciones_especiales' => $estudiante['condiciones_especiales'],
                                        'activo' => (int) $estudiante['activo'],
                                        'creado_en' => $estudiante['creado_en'],
                                        'nombre_creador' => $estudiante['nombre_creador'],
                                        'actualizado_en' => $estudiante['actualizado_en'],
                                        'nombre_actualizador' => $estudiante['nombre_actualizador'],
                                        'acudientes' => array_map(function ($acudiente) {
                                            return [
                                                'numero_documento' => $acudiente['numero_documento'],
                                                'nombres' => $acudiente['nombres'],
                                                'apellidos' => $acudiente['apellidos'],
                                                'telefono_contacto' => $acudiente['telefono_contacto'],
                                                'correo_electronico' => $acudiente['correo_electronico'],
                                                'direccion_residencia' => $acudiente['direccion_residencia'],
                                                'parentesco' => $acudiente['parentesco']
                                            ];
                                        }, $estudiante['acudientes'])
                                    ];

                                    $json_detalles = json_encode(
                                        $detalles_estudiante,
                                        JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP
                                    );
                                    ?>

                                    <tr
                                        data-estudiante
                                        data-documento="<?= escapar_html($estudiante['numero_documento']) ?>"
                                        data-nombres="<?= escapar_html($estudiante['nombres']) ?>"
                                        data-apellidos="<?= escapar_html($estudiante['apellidos']) ?>"
                                        data-grado="<?= escapar_html($estudiante['grado']) ?>"
                                        data-grupo="<?= escapar_html($estudiante['grupo']) ?>"
                                    >

                                        <td>
                                            <?= escapar_html($estudiante['numero_documento']) ?>
                                        </td>

                                        <td><?= escapar_html($estudiante['nombres']) ?></td>

                                        <td><?= escapar_html($estudiante['apellidos']) ?></td>

                                        <td>
                                            <?= escapar_html($estudiante['grado'] . ' - ' . $estudiante['grupo']) ?>
                                        </td>

                                        <td>
                                            <?= !empty($estudiante['telefono_contacto'])
                                                ? escapar_html($estudiante['telefono_contacto'])
                                                : '<span class="dato-sin-registro">Sin registrar</span>' ?>
                                        </td>

                                        <td>
                                            <?= $nombre_primer_acudiente !== ''
                                                ? escapar_html($nombre_primer_acudiente)
                                                : '<span class="dato-sin-registro">Sin acudiente</span>' ?>
                                        </td>

                                        <td>
                                            <?= !empty($primer_acudiente['telefono_contacto'])
                                                ? escapar_html($primer_acudiente['telefono_contacto'])
                                                : '<span class="dato-sin-registro">Sin registrar</span>' ?>
                                        </td>

                                        <td>
                                            <?php if ((int) $estudiante['activo'] === 1): ?>
                                                <span class="etiqueta-estado estado-activo">Activo</span>
                                            <?php else: ?>
                                                <span class="etiqueta-estado estado-inactivo">Inactivo</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= !empty($estudiante['creado_en'])
                                                ? escapar_html(date('d/m/Y H:i', strtotime($estudiante['creado_en'])))
                                                : '<span class="dato-sin-registro">Sin registrar</span>' ?>
                                        </td>

                                        <td>
                                            <?= !empty($estudiante['nombre_creador'])
                                                ? escapar_html($estudiante['nombre_creador'])
                                                : '<span class="dato-sin-registro">Usuario no disponible</span>' ?>
                                        </td>

                                        <td>
                                            <?= !empty($estudiante['actualizado_en'])
                                                ? escapar_html(date('d/m/Y H:i', strtotime($estudiante['actualizado_en'])))
                                                : '<span class="dato-sin-registro">Sin actualizar</span>' ?>
                                        </td>

                                        <td>
                                            <?= !empty($estudiante['actualizado_en']) && !empty($estudiante['nombre_actualizador'])
                                                ? escapar_html($estudiante['nombre_actualizador'])
                                                : '<span class="dato-sin-registro">Sin actualizar</span>' ?>
                                        </td>

                                        <td>
                                            <div class="acciones-estudiante">

                                                <button
                                                    type="button"
                                                    class="boton-accion boton-ver"
                                                    data-detalles="<?= escapar_html($json_detalles ?: '{}') ?>"
                                                >
                                                    Ver
                                                </button>

                                                <?php if ($es_administrador): ?>

                                                    <a
                                                        href="editar_estudiante.php?id=<?= (int) $estudiante['id_estudiante'] ?>"
                                                        class="boton-accion boton-editar"
                                                    >
                                                        Editar
                                                    </a>

                                                    <form
                                                        action="cambiar_estado_estudiante.php"
                                                        method="POST"
                                                        class="formulario-accion"
                                                    >
                                                        <input
                                                            type="hidden"
                                                            name="id_estudiante"
                                                            value="<?= (int) $estudiante['id_estudiante'] ?>"
                                                        >

                                                        <?php if ((int) $estudiante['activo'] === 1): ?>
                                                            <input type="hidden" name="activo" value="0">
                                                            <button type="submit" class="boton-accion boton-desactivar">
                                                                Desactivar
                                                            </button>
                                                        <?php else: ?>
                                                            <input type="hidden" name="activo" value="1">
                                                            <button type="submit" class="boton-accion boton-activar">
                                                                Activar
                                                            </button>
                                                        <?php endif; ?>
                                                    </form>

                                                <?php endif; ?>

                                            </div>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>

                    <p class="mensaje-vacio" id="sin-resultados" hidden>
                        No se encontraron estudiantes con los filtros seleccionados.
                    </p>

                <?php else: ?>

                    <p class="mensaje-vacio">
                        No hay estudiantes registrados.
                    </p>

                <?php endif; ?>

            </div>
        </section>

    </main>
</div>

<!-- Ventana emergente para consultar los datos completos -->
<div class="modal-estudiante" id="modal-estudiante" hidden>
    <div class="fondo-modal" data-cerrar-modal></div>

    <section
        class="contenido-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-estudiante"
    >
        <header class="encabezado-modal">
            <div>
                <h2 id="titulo-modal-estudiante">Información del estudiante</h2>
                <p>Consulta completa del registro seleccionado.</p>
            </div>

            <button type="button" class="boton-cerrar-modal" data-cerrar-modal aria-label="Cerrar">
                &times;
            </button>
        </header>

        <div class="cuerpo-modal" id="detalles-modal-estudiante"></div>

        <footer class="pie-modal">
            <button type="button" class="boton-principal" data-cerrar-modal>
                Cerrar
            </button>
        </footer>
    </section>
</div>

<script src="../assets/js/admin/menu.js"></script>
<script src="../assets/js/admin/estudiantes.js"></script>

</body>
</html>
