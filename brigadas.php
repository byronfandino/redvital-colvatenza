<?php
// Datos de las brigadas
$brigadas = [

    'contra_incendios' => [
        'titulo' => 'Brigada de Contra incendios',
        'descripcion' => 'Prevención y control de incendios.',
        'color' => '#E53935',
        'icono' => 'assets/images/incendio.png',
    ],

    'primeros_auxilios' => [
        'titulo' => 'Brigada de Primeros Auxilios',
        'descripcion' => 'Atención inicial en caso de accidentes o lesiones.',
        'color' => '#16A34A',
        'icono' => 'assets/images/primeros-auxilios.png',
    ],

    'evacuacion_rescate' => [
        'titulo' => 'Brigada de Evacuación y Rescate',
        'descripcion' => 'Apoyo en evacuación y traslado seguro.',
        'color' => '#2563EB',
        'icono' => 'assets/images/brigadistas.svg',
    ],
];

// Datos de los brigadistas
$brigadistas = [

    'contra_incendios' => [
        [
            'nombre' => 'Carlos Andrés Pérez',
            'cargo' => 'Brigadista contra incendios',
            'celular' => '300 123 4567',
        ],
        [
            'nombre' => 'María Fernanda Gómez',
            'cargo' => 'Brigadista contra incendios',
            'celular' => '310 456 7890',
        ],
        [
            'nombre' => 'Jorge Luis Rodríguez',
            'cargo' => 'Coordinador de brigada',
            'celular' => '315 789 1234',
        ],
    ],

    'primeros_auxilios' => [
        [
            'nombre' => 'Laura Marcela Torres',
            'cargo' => 'Brigadista de primeros auxilios',
            'celular' => '300 987 6543',
        ],
        [
            'nombre' => 'Andrés Felipe Martínez',
            'cargo' => 'Brigadista de primeros auxilios',
            'celular' => '312 345 6789',
        ],
        [
            'nombre' => 'Diana Carolina López',
            'cargo' => 'Coordinadora de brigada',
            'celular' => '318 654 3210',
        ],
    ],

    'evacuacion_rescate' => [
        [
            'nombre' => 'Juan David Ramírez',
            'cargo' => 'Brigadista de evacuación',
            'celular' => '301 234 5678',
        ],
        [
            'nombre' => 'Natalia Andrea Castro',
            'cargo' => 'Brigadista de evacuación',
            'celular' => '313 456 7890',
        ],
        [
            'nombre' => 'Sebastián Moreno',
            'cargo' => 'Coordinador de brigada',
            'celular' => '316 789 0123',
        ],
    ],

];

// Datos de teléfonos de emergencia
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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brigadistas | RedVital</title>
    <link rel="stylesheet" href="assets/css/public/menu.css" >
    <link rel="stylesheet" href="assets/css/public/pie-pagina.css" >
    <link rel="stylesheet" href="assets/css/public/brigadistas.css" >
</head>
<body>

    <?php include_once 'includes/public/navbar.php' ?>

    <main>
        <!-- Hero -->
        <section class="hero-brigadistas">
            <div class="hero-contenido">
                <h1> Nuestras brigadas </h1>
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
                    <h2> Conoce nuestras brigadas </h2>
                    <p>
                        Conoce al personal encargado de apoyar la atención
                        y respuesta ante diferentes situaciones de emergencia.
                    </p>
                </div>

                <div class="tarjetas-brigadas">

                    <?php foreach ($brigadas as $tipo => $brigada): ?>
                        <article class="tarjeta-brigada" style="--color-brigada: <?= $brigada['color'] ?>;" >
                            <div class="tarjeta-icono">
                                <img src="<?= htmlspecialchars($brigada['icono']) ?>" alt="<?= htmlspecialchars($brigada['titulo']) ?>">
                            </div>

                            <div class="tarjeta-contenido">
                                <h3> <?= htmlspecialchars($brigada['titulo']) ?> </h3>
                                <p> <?= htmlspecialchars($brigada['descripcion']) ?> </p>
                                <a href="#brigada-<?= $tipo ?>" class="boton-brigada"> Ver brigadistas </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Brigadistas -->

        <section class="seccion-informacion-brigadistas">
            <div class="contenedor">
                <div class="encabezado-seccion">
                    <h2>Personal de nuestras brigadas</h2>
                    <p>
                        Conoce las personas encargadas de apoyar
                        la atención de emergencias.
                    </p>
                </div>

                <?php foreach ($brigadas as $tipo => $brigada): ?>
                    <article id="brigada-<?= $tipo ?>" class="detalle-brigada" style="--color-brigada: <?= $brigada['color'] ?>;" >
                        <div class="detalle-encabezado">
                            <div class="detalle-icono">
                                <img src="<?= htmlspecialchars($brigada['icono']) ?>" alt="<?= htmlspecialchars($brigada['titulo']) ?>">
                            </div>

                            <div>
                                <h2> <?= htmlspecialchars($brigada['titulo']) ?> </h2>
                                <p> <?= htmlspecialchars($brigada['descripcion']) ?> </p>
                            </div>
                        </div>

                        <div class="tabla-contenedor">
                            <table class="tabla-brigadistas">
                                <thead>
                                    <tr>
                                        <th> Nombre </th>
                                        <th> Cargo </th>
                                        <th> Celular </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($brigadistas[$tipo] as $brigadista): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($brigadista['nombre']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($brigadista['cargo']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($brigadista['celular']) ?>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </article>

                <?php endforeach; ?>

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
                                    <?= htmlspecialchars($contacto['nombre']) ?>
                                </h3>

                                <p>
                                    <?= htmlspecialchars($contacto['telefono']) ?>
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
        <!--  -->

    </main>
   <?php include_once 'includes/public/footer.php' ?>
</body>
</html>