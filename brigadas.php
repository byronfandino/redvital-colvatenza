
<?php

require_once __DIR__ . '/config/database.php';

// Cargar las brigadas activas.
$brigadas = [];
$brigadistasPorBrigada = [];
$error_carga = false;

try {
    $consulta_brigadas = $pdo->query("
        SELECT
            id_brigada,
            nombre,
            descripcion,
            color,
            icono
        FROM brigadas
        WHERE activo = 1
        ORDER BY nombre ASC
    ");

    $brigadas = $consulta_brigadas->fetchAll(PDO::FETCH_ASSOC);

    // Cargar los brigadistas activos de las brigadas activas.
    $consulta_brigadistas = $pdo->query("
        SELECT
            b.id_brigadista,
            b.id_brigada,
            b.numero_documento,
            b.nombres,
            b.apellidos,
            b.dependencia,
            b.celular
        FROM brigadistas b
        INNER JOIN brigadas br
            ON br.id_brigada = b.id_brigada
        WHERE b.activo = 1
          AND br.activo = 1
        ORDER BY b.apellidos ASC, b.nombres ASC
    ");

    $registros_brigadistas = $consulta_brigadistas->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar los brigadistas según su brigada.
    foreach ($registros_brigadistas as $brigadista) {
        $id_brigada = (int) $brigadista['id_brigada'];

        $brigadistasPorBrigada[$id_brigada][] = $brigadista;
    }
} catch (Throwable $e) {
    error_log('Error al cargar brigadas y brigadistas públicos: ' . $e->getMessage());

    $brigadas = [];
    $brigadistasPorBrigada = [];
    $error_carga = true;
}

// Datos de teléfonos de emergencia.
$telefonosEmergencia = [
    [
        'nombre' => 'Alcaldía',
        'telefono' => '310 441 9567',
    ],
    [
        'nombre' => 'Urgencias',
        'telefono' => '316 742 8505',
    ],
    [
        'nombre' => 'Ambulancia',
        'telefono' => '316 742 8515',
    ],
    [
        'nombre' => 'Bomberos',
        'telefono' => '314 219 4981',
    ],
    [
        'nombre' => 'Defensa Civil',
        'telefono' => '310 623 2796',
    ],
    [
        'nombre' => 'Estación de Policía',
        'telefono' => '310 442 5020',
    ],
    [
        'nombre' => 'Cuadrante',
        'telefono' => '302 351 2353',
    ],
    [
        'nombre' => 'EBSA Guateque',
        'telefono' => '312 497 1303',
    ],
    [
        'nombre' => 'Servicios Públicos',
        'telefono' => '310 443 2457',
    ],
    [
        'nombre' => 'Concesión Sisga',
        'telefono' => '316 549 7841',
    ],
    [
        'nombre' => 'ENERCER',
        'telefono' => '321 349 9349',
    ],
    [
        'nombre' => 'Comisaría de Familia',
        'telefono' => '310 442 5041',
    ],
    [
        'nombre' => 'Personería',
        'telefono' => '310 888 0328',
    ],
    [
        'nombre' => 'Dirección Local de Salud',
        'telefono' => '310 442 6051',
    ],
];

// Proteger los valores mostrados en HTML.
function escapar_html($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// Validar el color antes de utilizarlo en estilos CSS.
function obtener_color_brigada($color): string
{
    return preg_match('/^#[0-9a-fA-F]{6}$/', (string) $color)
        ? $color
        : '#075447';
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Brigadistas | RedVital Colvatenza</title>

    <link rel="stylesheet" href="assets/css/public/menu.css">
    <link rel="stylesheet" href="assets/css/public/pie-pagina.css">
    <link rel="stylesheet" href="assets/css/public/brigadistas.css">
</head>

<body>

    <?php include_once __DIR__ . '/includes/public/menu.php'; ?>

    <main>

        <!-- Hero -->
        <section class="hero-brigadistas">
            <div class="hero-contenido">
                <h1>Nuestras brigadas</h1>

                <p>
                    Personal capacitado para prevenir, atender
                    y responder ante emergencias.
                </p>
            </div>
        </section>

        <!-- Tarjetas -->
        <section class="seccion-brigadas">
            <div class="contenedor">

                <div class="encabezado-seccion">
                    <h2>Conoce nuestras brigadas</h2>

                    <p>
                        Conoce al personal encargado de apoyar la atención
                        y respuesta ante diferentes situaciones de emergencia.
                    </p>
                </div>

                <?php if ($error_carga): ?>

                    <p class="mensaje-error" role="alert">
                        No fue posible cargar la información de las brigadas.
                        Intenta nuevamente más tarde.
                    </p>

                <?php elseif (empty($brigadas)): ?>

                    <p>
                        Actualmente no hay brigadas activas registradas.
                    </p>

                <?php else: ?>

                    <div class="tarjetas-brigadas">

                        <?php foreach ($brigadas as $brigada): ?>
                            <?php
                                $id_brigada = (int) $brigada['id_brigada'];
                                $color_brigada = obtener_color_brigada($brigada['color']);
                                $icono_brigada = 'assets/images/' . basename($brigada['icono']);
                            ?>

                            <article
                                class="tarjeta-brigada"
                                style="--color-brigada: <?= escapar_html($color_brigada) ?>;"
                            >
                                <div class="tarjeta-icono">
                                    <img
                                        src="<?= escapar_html($icono_brigada) ?>"
                                        alt="<?= escapar_html($brigada['nombre']) ?>"
                                    >
                                </div>

                                <div class="tarjeta-contenido">
                                    <h3><?= escapar_html($brigada['nombre']) ?></h3>

                                    <p><?= escapar_html($brigada['descripcion']) ?></p>

                                    <a
                                        href="#brigada-<?= $id_brigada ?>"
                                        class="boton-brigada"
                                    >
                                        Ver brigadistas
                                    </a>
                                </div>
                            </article>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>
        </section>

        <!-- Brigadistas -->
        <section id="contactos" class="seccion-informacion-brigadistas">
            <div class="contenedor">

                <div class="encabezado-seccion">
                    <h2>Personal de nuestras brigadas</h2>

                    <p>
                        Conoce las personas encargadas de apoyar
                        la atención de emergencias.
                    </p>
                </div>

                <?php if (!$error_carga && !empty($brigadas)): ?>

                    <?php foreach ($brigadas as $brigada): ?>
                        <?php
                            $id_brigada = (int) $brigada['id_brigada'];
                            $color_brigada = obtener_color_brigada($brigada['color']);
                            $icono_brigada = 'assets/images/' . basename($brigada['icono']);
                            $integrantes = $brigadistasPorBrigada[$id_brigada] ?? [];
                        ?>

                        <article
                            id="brigada-<?= $id_brigada ?>"
                            class="detalle-brigada"
                            style="--color-brigada: <?= escapar_html($color_brigada) ?>;"
                        >

                            <div class="detalle-encabezado">

                                <div class="detalle-icono">
                                    <img
                                        src="<?= escapar_html($icono_brigada) ?>"
                                        alt="<?= escapar_html($brigada['nombre']) ?>"
                                    >
                                </div>

                                <div>
                                    <h2><?= escapar_html($brigada['nombre']) ?></h2>

                                    <p><?= escapar_html($brigada['descripcion']) ?></p>
                                </div>

                            </div>

                            <div class="tabla-contenedor">

                                <table class="tabla-brigadistas">

                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Dependencia</th>
                                            <th>Celular</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php if (!empty($integrantes)): ?>

                                            <?php foreach ($integrantes as $brigadista): ?>
                                                <tr>
                                                    <td>
                                                        <?= escapar_html(
                                                            $brigadista['nombres'] . ' ' .
                                                            $brigadista['apellidos']
                                                        ) ?>
                                                    </td>

                                                    <td>
                                                        <?= escapar_html($brigadista['dependencia']) ?>
                                                    </td>

                                                    <td>
                                                        <?= escapar_html($brigadista['celular']) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>

                                        <?php else: ?>

                                            <tr>
                                                <td colspan="3">
                                                    Actualmente no hay brigadistas activos
                                                    registrados en esta brigada.
                                                </td>
                                            </tr>

                                        <?php endif; ?>

                                    </tbody>

                                </table>

                            </div>

                        </article>

                    <?php endforeach; ?>

                <?php elseif (!$error_carga): ?>

                    <p>
                        No hay brigadas disponibles para mostrar sus integrantes.
                    </p>

                <?php endif; ?>

            </div>
        </section>

        <!-- Directorio de emergencias -->
        <section class="seccion-directorio-emergencias">
            <div class="contenedor">

                <div class="encabezado-seccion">
                    <h2>Teléfonos de emergencia</h2>

                    <p>
                        Ten a la mano los principales contactos para solicitar
                        ayuda ante una situación de emergencia.
                    </p>
                </div>

                <div class="directorio-emergencias">

                    <?php foreach ($telefonosEmergencia as $contacto): ?>

                        <article class="tarjeta-contacto-emergencia">

                            <div class="contacto-informacion">
                                <h3>
                                    <?= escapar_html($contacto['nombre']) ?>
                                </h3>

                                <p>
                                    <?= escapar_html($contacto['telefono']) ?>
                                </p>
                            </div>

                            <a
                                href="tel:<?= preg_replace('/\s+/', '', $contacto['telefono']) ?>"
                                class="boton-llamar"
                            >
                                Llamar
                            </a>

                        </article>

                    <?php endforeach; ?>

                </div>

            </div>
        </section>

    </main>

    <?php include_once __DIR__ . '/includes/public/pie_pagina.php'; ?>

</body>
</html>
