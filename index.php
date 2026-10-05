<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="RedVital Colvatenza - Sistema de prevención, preparación y respuesta ante emergencias.">
    <title>Inicio | RedVital Colvatenza</title>
    <link rel="stylesheet" href="assets/css/public/index.css">
</head>

<body>

    <?php include 'includes/public/navbar.php'; ?>

    <main>

        <section class="seccion-hero">
            <div class="fondo-hero"></div>
            <div class="contenido-hero">

                <!-- Logo y presentación institucional -->
                <div class="presentacion-hero">
                    <img
                        src="assets/images/logo.png"
                        alt="Logo RedVital Colvatenza"
                        class="logo-hero"
                    >

                    <p class="subtitulo-hero">
                        Sistema de prevención, preparación y respuesta ante emergencias
                    </p>

                    <p class="institucion-hero">
                        Institución Educativa Técnica Valle de Tenza
                    </p>
                </div>


                <!-- Mensaje principal -->
                <div class="mensaje-hero">
                    <h1>
                        Comunidad segura,<br>
                        una institución más fuerte
                    </h1>
                    <p class="eslogan-hero">
                        Prevenir <span>•</span> Preparar <span>•</span> Responder
                    </p>
                </div>
            </div>
        </section>

        <!-- ACCESOS RÁPIDOS -->
        <section class="seccion-accesos">
            <div class="contenedor">
                <div class="encabezado-seccion">
                    <span class="etiqueta-seccion">
                        Información de emergencia
                    </span>
                    <h2 class="titulo-seccion">
                        ¿Qué necesitas consultar?
                    </h2>
                    <p class="descripcion-seccion">
                        Encuentra rápidamente la información necesaria
                        para prevenir, prepararte y actuar ante una
                        situación de emergencia.
                    </p>
                </div>

                <div class="tarjetas-accesos">

                    <!-- ¿Qué hacer en una emergencia? -->
                    <article class="tarjeta-acceso tarjeta-emergencia">
                        <div class="icono-acceso">
                            <img src="assets/images/icono-mas.svg" alt="Icono Plus">
                        </div>

                        <h3>
                            ¿Qué hacer en<br>
                            una emergencia?
                        </h3>

                        <p> Guía y recomendaciones </p>

                        <a href="emergencias.php" class="enlace-tarjeta">
                            Ver información
                        </a>
                    </article>

                    <!-- Brigadas -->

                    <article class="tarjeta-acceso tarjeta-brigadas">

                        <div class="icono-acceso">
                            <img src="assets/images/icono-brigadas.svg" alt="Icono Plus">
                        </div>

                        <h3> Brigadas </h3>
                        <p>Conoce las brigadas</p>
                        <a href="brigadas.php" class="enlace-tarjeta">
                            Conocer brigadas
                        </a>
                    </article>


                    <!-- Ruta de evacuación -->

                    <article class="tarjeta-acceso tarjeta-evacuacion">
                        <div class="icono-acceso">
                           <img src="assets/images/icono-map.svg" alt="Icono Mapa">
                        </div>

                        <h3>Ruta de<br> evacuación</h3>

                        <p>Mapa y puntos de encuentro</p>

                        <a href="evacuacion.php" class="enlace-tarjeta">
                            Ver ruta
                        </a>
                    </article>

                    <!-- Contactos -->

                    <article class="tarjeta-acceso tarjeta-contactos">

                        <div class="icono-acceso">
                            <img src="assets/images/icono-phone.svg" alt="Icono Mapa">
                        </div>

                        <h3>Contactos de<br>
                            emergencia
                        </h3>

                        <p>Números importantes</p>
                        <a href="contactos.php" class="enlace-tarjeta">
                            Ver contactos
                        </a>
                    </article>
                </div>
            </div>
        </section>

        <!-- =====================================================
            AVISO IMPORTANTE
            ===================================================== -->

        <section class="seccion-informacion">
            <div class="contenedor">
                <div class="aviso-importante">
                    <div class="icono-aviso">
                        !
                    </div>

                    <div class="contenido-aviso">
                        <h3>La prevención comienza con la información</h3>
                        <p>
                            Conocer las rutas de evacuación, los puntos
                            de encuentro, las brigadas y las acciones
                            adecuadas ante una emergencia permite
                            responder de manera más organizada y segura.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
            SOBRE REDVITAL
            ===================================================== -->
        <section class="seccion-presentacion">

            <div class="contenedor contenido-presentacion">

                <div class="texto-presentacion">

                    <span class="etiqueta-seccion">
                        RedVital Colvatenza
                    </span>

                    <h2 class="titulo-presentacion">
                        Prevenir • Preparar • Responder
                    </h2>

                    <p class="descripcion-presentacion">
                        Este proyecto busca facilitar el acceso a información
                        relacionada con la prevención, preparación y respuesta
                        ante emergencias en la comunidad educativa.
                    </p>

                    <p class="descripcion-presentacion">
                        A través de RedVital Colvatenza se reúnen recursos,
                        recomendaciones y elementos del plan de prevención,
                        preparación y respuesta ante emergencias de la
                        institución.
                    </p>


                    <ul class="lista-presentacion">

                        <li>
                            <span class="icono-lista">✓</span>
                            Prevención de situaciones de emergencia
                        </li>

                        <li>
                            <span class="icono-lista">✓</span>
                            Preparación de la comunidad educativa
                        </li>

                        <li>
                            <span class="icono-lista">✓</span>
                            Respuesta organizada ante emergencias
                        </li>

                    </ul>

                </div>


                <div class="imagen-presentacion">

                    <figure>

                        <img
                            src="assets/images/colegio2.png"
                            alt="Institución Educativa Técnica Valle de Tenza"
                        >

                        <figcaption>
                            Institución Educativa Técnica Valle de Tenza
                        </figcaption>

                    </figure>

                </div>

            </div>

        </section>
    </main>

    <?php include 'includes/public/footer.php'; ?>
    <script src="assets/js/public/menu.js"></script>
</body>
</html>