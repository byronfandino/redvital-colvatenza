
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Información institucional y datos de contacto de RedVital Colvatenza.">
    <title>Información institucional | RedVital Colvatenza</title>
    <link rel="stylesheet" href="assets/css/public/menu.css">
    <link rel="stylesheet" href="assets/css/public/pie-pagina.css">
    <link rel="stylesheet" href="assets/css/public/informacion.css">
</head>

<body>

    <?php include 'includes/public/navbar.php'; ?>

    <main class="pagina-institucional">

        <!-- Hero institucional -->
        <section class="hero-institucional">
            <div class="contenedor">
                <h1>Información institucional y contacto</h1>
                <p>
                    Conoce nuestra institución y encuentra la información
                    necesaria para comunicarte con nosotros.
                </p>
            </div>
        </section>

        <!-- Información general -->
        <section class="seccion-informacion">
            <div class="contenedor">
                <h2>Información general</h2>
                <p class="descripcion-seccion">
                    Conoce los datos principales de nuestra institución educativa.
                </p>

                <div class="tarjeta-informacion">

                    <article class="dato-institucional">
                        <div class="contenedor-icono icono-verde">
                            <img src="assets/images/escuela.svg"
                                 alt="" aria-hidden="true" class="icono">
                        </div>
                        <h3>Institución educativa</h3>
                        <p>Institución Educativa Técnica Valle de Tenza</p>
                    </article>

                    <article class="dato-institucional">
                        <div class="contenedor-icono icono-rojo">
                            <img src="assets/images/casa.svg"
                                 alt="" aria-hidden="true" class="icono">
                        </div>
                        <h3>Sede principal</h3>
                        <p>Campestre</p>
                    </article>

                    <article class="dato-institucional">
                        <div class="contenedor-icono icono-azul">
                            <img src="assets/images/ubicacion.svg"
                                 alt="" aria-hidden="true" class="icono">
                        </div>
                        <h3>Dirección</h3>
                        <p>Km 1 vía Sutatenza</p>
                    </article>

                    <article class="dato-institucional">
                        <div class="contenedor-icono icono-morado">
                            <img src="assets/images/mapa.svg"
                                 alt="" aria-hidden="true" class="icono">
                        </div>
                        <h3>Ubicación</h3>
                        <p>Guateque, Boyacá, Colombia</p>
                    </article>

                </div>
            </div>
        </section>

        <!-- Contacto y horario -->
        <section class="seccion-contacto">
            <div class="contenedor">
                <h2>Contacto y atención</h2>
                <p class="descripcion-seccion">
                    Aquí encontrarás nuestros canales de comunicación y el horario de atención.
                </p>

                <div class="contenedor-contacto">

                    <!-- Datos de contacto -->
                    <article class="tarjeta-contacto">
                        <h3>Información de contacto</h3>

                        <div class="dato-contacto">
                            <div class="contenedor-icono icono-verde">
                                <img src="assets/images/telefono.svg"
                                     alt="" aria-hidden="true" class="icono">
                            </div>
                            <div class="texto-contacto">
                                <h4>Teléfono</h4>
                                <a href="tel:3202481822">320 248 1822</a>
                            </div>
                        </div>

                        <div class="dato-contacto">
                            <div class="contenedor-icono icono-rojo">
                                <img src="assets/images/correo.svg"
                                     alt="" aria-hidden="true" class="icono">
                            </div>
                            <div class="texto-contacto">
                                <h4>Correo electrónico</h4>
                                <a href="mailto:guatequevalledetenza@sedboyaca.gov.co">
                                    guatequevalledetenza@sedboyaca.gov.co
                                </a>
                            </div>
                        </div>

                        <div class="dato-contacto">
                            <div class="contenedor-icono icono-azul">
                                <img src="assets/images/direccion.svg"
                                     alt="" aria-hidden="true" class="icono">
                            </div>
                            <div class="texto-contacto">
                                <h4>Dirección</h4>
                                <p>Km 1 vía Sutatenza, Guateque, Boyacá</p>
                            </div>
                        </div>
                    </article>

                    <!-- Horario de atención -->
                    <article class="tarjeta-horario">
                        <h3>Horario de atención</h3>

                        <div class="contenido-horario">
                            <div class="contenedor-icono icono-verde icono-reloj">
                                <img src="assets/images/reloj.svg"
                                     alt="" aria-hidden="true" class="icono">
                            </div>
                            <div>
                                <h4>Lunes a viernes</h4>
                                <p>7:00 a. m. - 3:00 p. m.</p>
                            </div>
                        </div>

                        <img
                            src="assets/images/colegio-valle-tenza.jpeg"
                            alt="Instalaciones de la institución educativa"
                            class="imagen-institucion"
                            loading="lazy"
                        >
                    </article>

                </div>
            </div>
        </section>

    </main>

    <?php include 'includes/public/footer.php'; ?>

</body>
</html>
