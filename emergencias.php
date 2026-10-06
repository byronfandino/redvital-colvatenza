<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergencias | RedVital Colvatenza</title>
    <link rel="stylesheet" href="assets/css/public/menu.css">
    <link rel="stylesheet" href="assets/css/public/pie-pagina.css">
    <link rel="stylesheet" href="assets/css/public/emergencias.css">
</head>

<body>

    <!-- Encabezado -->
    <?php include 'includes/public/navbar.php'; ?>

    <!-- Presentación -->
    <main>

        <!-- Hero -->
        <section class="hero-emergencias">
            <div class="hero-contenido contenedor">
                <h1>¿Qué hacer en una<br>emergencia?</h1>
                <p>Conoce los pasos a seguir según el tipo de situación.</p>
            </div>
        </section>

        <!-- Opciones de emergencia -->
        <section id="inicio" class="emergencias">
            <div class="contenedor">

                <div class="tarjetas-emergencia">

                    <!-- Incendio -->
                    <a href="#incendio" class="tarjeta-emergencia tarjeta-incendio">
                        <div class="icono-emergencia">
                            <img src="assets/images/incendio.png" alt="Incendio">
                        </div>

                        <div class="contenido-tarjeta">
                            <h2>Incendio</h2>
                            <p>Cómo actuar, paso a paso</p>
                        </div>

                        <span class="flecha-emergencia">+</span>
                    </a>


                    <!-- Sismo -->
                    <a href="#sismo" class="tarjeta-emergencia tarjeta-sismo">
                        <div class="icono-emergencia">
                            <img src="assets/images/sismo.png" alt="Sismo">
                        </div>

                        <div class="contenido-tarjeta">
                            <h2>Sismo</h2>
                            <p>Recomendaciones y zonas seguras</p>
                        </div>

                        <span class="flecha-emergencia">+</span>
                    </a>


                    <!-- Accidente -->
                    <a href="#primeros-auxilios" class="tarjeta-emergencia tarjeta-accidente">
                        <div class="icono-emergencia">
                            <img src="assets/images/primeros-auxilios.png" alt="Primeros auxilios">
                        </div>

                        <div class="contenido-tarjeta">
                            <h2>Accidente / Primeros auxilios</h2>
                            <p>Qué hacer ante lesiones o accidentes</p>
                        </div>

                        <span class="flecha-emergencia">+</span>
                    </a>


                    <!-- Evacuación -->
                    <a href="#evacuacion" class="tarjeta-emergencia tarjeta-evacuacion">
                        <div class="icono-emergencia">
                            <img src="assets/images/evacuacion.png" alt="Evacuación">
                        </div>

                        <div class="contenido-tarjeta">
                            <h2>Evacuación</h2>
                            <p>Procedimiento de evacuación de la institución</p>
                        </div>

                        <span class="flecha-emergencia">+</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Emergencias -->

        <section class="seccion-emergencias">

            <!-- Incendio -->

            <section id="incendio" class="emergencia emergencia-incendio">

                <div class="contenedor">

                    <!-- Encabezado -->
                    <div class="encabezado-emergencia">

                        <div class="icono-emergencia-grande">
                            <img src="assets/images/incendio.png" alt="Incendio">
                        </div>

                        <div>
                            <span class="etiqueta-emergencia">EMERGENCIA 01</span>
                            <h2>Incendio</h2>
                            <p>Conoce cómo actuar de manera segura ante un incendio.</p>
                        </div>

                    </div>


                    <!-- Qué hacer -->
                    <div class="bloque-emergencia">

                        <div class="titulo-bloque">
                            <span class="numero-bloque">01</span>

                            <div>
                                <h3>¿Qué hacer?</h3>
                                <p>Sigue estos pasos y mantén la calma.</p>
                            </div>
                        </div>


                        <div class="pasos-emergencia">

                            <article class="paso-emergencia">
                                <span class="numero-paso">1</span>

                                <div>
                                    <h4>Mantén la calma</h4>
                                    <p>Conserva la calma y evalúa rápidamente la situación.</p>
                                </div>
                            </article>


                            <article class="paso-emergencia">
                                <span class="numero-paso">2</span>

                                <div>
                                    <h4>Da aviso</h4>
                                    <p>Informa inmediatamente a las personas responsables de la institución.</p>
                                </div>
                            </article>


                            <article class="paso-emergencia">
                                <span class="numero-paso">3</span>

                                <div>
                                    <h4>Aléjate del fuego</h4>
                                    <p>Evita acercarte al fuego y mantente alejado del humo.</p>
                                </div>
                            </article>


                            <article class="paso-emergencia">
                                <span class="numero-paso">4</span>

                                <div>
                                    <h4>Evacúa</h4>
                                    <p>Sigue las rutas de evacuación establecidas hasta llegar al punto de encuentro.</p>
                                </div>
                            </article>

                        </div>

                    </div>


                    <!-- Qué no hacer -->
                    <div class="alerta-emergencia">

                        <div class="icono-alerta">
                            <span>!</span>
                        </div>

                        <div>
                            <h3>¿Qué no hacer?</h3>

                            <ul>
                                <li>No regreses al lugar del incendio.</li>
                                <li>No utilices ascensores durante la evacuación.</li>
                                <li>No te expongas innecesariamente al humo.</li>
                                <li>No pongas en riesgo tu vida para recuperar objetos personales.</li>
                            </ul>
                        </div>

                    </div>

                </div>

            </section>



            <!-- Sismo -->

            <section id="sismo" class="emergencia emergencia-sismo">

                <div class="contenedor">

                    <!-- Encabezado -->
                    <div class="encabezado-emergencia">

                        <div class="icono-emergencia-grande">
                            <img src="assets/images/sismo.png" alt="Sismo">
                        </div>

                        <div>
                            <span class="etiqueta-emergencia">EMERGENCIA 02</span>
                            <h2>Sismo</h2>
                            <p>Aprende qué hacer antes, durante y después de un sismo.</p>
                        </div>

                    </div>


                    <!-- Antes -->
                    <div class="bloque-emergencia">

                        <div class="titulo-bloque">
                            <span class="numero-bloque">01</span>

                            <div>
                                <h3>Antes del sismo</h3>
                                <p>Prepárate y conoce los lugares seguros.</p>
                            </div>
                        </div>


                        <div class="recomendaciones-emergencia">

                            <article class="recomendacion-emergencia">
                                <span>1</span>
                                <p>Identifica las zonas seguras y las rutas de evacuación.</p>
                            </article>

                            <article class="recomendacion-emergencia">
                                <span>2</span>
                                <p>Participa activamente en los simulacros de emergencia.</p>
                            </article>

                            <article class="recomendacion-emergencia">
                                <span>3</span>
                                <p>Mantén despejadas las rutas de evacuación.</p>
                            </article>

                        </div>

                    </div>


                    <!-- Durante -->
                    <div class="bloque-emergencia">

                        <div class="titulo-bloque">
                            <span class="numero-bloque">02</span>

                            <div>
                                <h3>Durante el sismo</h3>
                                <p>Protégete hasta que el movimiento termine.</p>
                            </div>
                        </div>


                        <div class="recomendaciones-emergencia">

                            <article class="recomendacion-emergencia">
                                <span>1</span>
                                <p>Mantén la calma y evita correr.</p>
                            </article>

                            <article class="recomendacion-emergencia">
                                <span>2</span>
                                <p>Aléjate de ventanas, vidrios y objetos que puedan caer.</p>
                            </article>

                            <article class="recomendacion-emergencia">
                                <span>3</span>
                                <p>Protégete debajo de una estructura resistente si es posible.</p>
                            </article>

                        </div>

                    </div>


                    <!-- Después -->
                    <div class="alerta-emergencia alerta-sismo">

                        <div class="icono-alerta">
                            <span>!</span>
                        </div>

                        <div>
                            <h3>Después del sismo</h3>

                            <ul>
                                <li>Verifica tu estado y el de las personas que están cerca.</li>
                                <li>Evacúa únicamente cuando sea seguro hacerlo.</li>
                                <li>Sigue las instrucciones de los responsables de la emergencia.</li>
                                <li>No regreses a las instalaciones hasta recibir autorización.</li>
                            </ul>
                        </div>

                    </div>

                </div>

            </section>



            <!-- Primeros auxilios -->

            <section id="primeros-auxilios" class="emergencia emergencia-primeros-auxilios">

                <div class="contenedor">

                    <!-- Encabezado -->
                    <div class="encabezado-emergencia">

                        <div class="icono-emergencia-grande">
                            <img src="assets/images/primeros-auxilios.png" alt="Primeros auxilios">
                        </div>

                        <div>
                            <span class="etiqueta-emergencia">EMERGENCIA 03</span>
                            <h2>Accidente / Primeros auxilios</h2>
                            <p>Actúa de manera adecuada ante una lesión o accidente.</p>
                        </div>

                    </div>


                    <!-- Qué hacer -->
                    <div class="bloque-emergencia">

                        <div class="titulo-bloque">
                            <span class="numero-bloque">01</span>

                            <div>
                                <h3>¿Qué hacer?</h3>
                                <p>Actúa con calma y solicita ayuda cuando sea necesario.</p>
                            </div>
                        </div>


                        <div class="pasos-emergencia">

                            <article class="paso-emergencia">
                                <span class="numero-paso">1</span>

                                <div>
                                    <h4>Evalúa la situación</h4>
                                    <p>Verifica que el lugar sea seguro antes de acercarte a la persona.</p>
                                </div>
                            </article>


                            <article class="paso-emergencia">
                                <span class="numero-paso">2</span>

                                <div>
                                    <h4>Solicita ayuda</h4>
                                    <p>Informa a un responsable y solicita asistencia si la situación lo requiere.</p>
                                </div>
                            </article>


                            <article class="paso-emergencia">
                                <span class="numero-paso">3</span>

                                <div>
                                    <h4>Conserva la calma</h4>
                                    <p>Evita realizar procedimientos que puedan empeorar la situación.</p>
                                </div>
                            </article>


                            <article class="paso-emergencia">
                                <span class="numero-paso">4</span>

                                <div>
                                    <h4>Acompaña a la persona</h4>
                                    <p>Permanece junto a la persona afectada mientras llega la ayuda.</p>
                                </div>
                            </article>

                        </div>

                    </div>


                    <!-- Recomendaciones -->
                    <div class="alerta-emergencia alerta-primeros-auxilios">

                        <div class="icono-alerta">
                            <span>+</span>
                        </div>

                        <div>
                            <h3>Recuerda</h3>

                            <ul>
                                <li>No muevas a una persona lesionada si no es necesario.</li>
                                <li>No suministres medicamentos sin autorización.</li>
                                <li>Solicita ayuda profesional cuando la situación lo requiera.</li>
                                <li>Informa lo ocurrido a los responsables de la institución.</li>
                            </ul>
                        </div>

                    </div>

                </div>

            </section>



            <!-- Evacuación -->

            <section id="evacuacion" class="emergencia emergencia-evacuacion">

                <div class="contenedor">

                    <!-- Encabezado -->
                    <div class="encabezado-emergencia">

                        <div class="icono-emergencia-grande">
                            <img src="assets/images/evacuacion.png" alt="Evacuación">
                        </div>

                        <div>
                            <span class="etiqueta-emergencia">EMERGENCIA 04</span>
                            <h2>Evacuación</h2>
                            <p>Conoce cómo evacuar de manera segura y ordenada.</p>
                        </div>

                    </div>


                    <!-- Cuándo evacuar -->
                    <div class="bloque-emergencia">

                        <div class="titulo-bloque">
                            <span class="numero-bloque">01</span>

                            <div>
                                <h3>¿Cuándo evacuar?</h3>
                                <p>Evacúa cuando recibas la indicación de hacerlo.</p>
                            </div>
                        </div>


                        <div class="recomendaciones-emergencia">

                            <article class="recomendacion-emergencia">
                                <span>1</span>
                                <p>Mantén la calma y sigue las instrucciones de los responsables.</p>
                            </article>

                            <article class="recomendacion-emergencia">
                                <span>2</span>
                                <p>Utiliza las rutas de evacuación señalizadas.</p>
                            </article>

                            <article class="recomendacion-emergencia">
                                <span>3</span>
                                <p>Dirígete al punto de encuentro establecido.</p>
                            </article>

                        </div>

                    </div>


                    <!-- Rutas de evacuación -->
                    <div class="bloque-rutas">

                        <div class="contenido-rutas">

                            <h3>Conoce las rutas de evacuación</h3>

                            <p>
                                Identifica las rutas señalizadas dentro de la institución
                                y conoce el punto de encuentro antes de que ocurra una emergencia.
                            </p>

                        </div>


                        <div class="imagen-rutas">
                            <img src="assets/images/ruta-evacuacion.jpeg" alt="Ruta de evacuación">
                        </div>

                    </div>


                    <!-- Recomendaciones -->
                    <div class="alerta-emergencia alerta-evacuacion">

                        <div class="icono-alerta">
                            <span>!</span>
                        </div>

                        <div>
                            <h3>Durante la evacuación</h3>

                            <ul>
                                <li>Camina, no corras.</li>
                                <li>No regreses por objetos personales.</li>
                                <li>No bloquees las rutas de evacuación.</li>
                                <li>Permanece en el punto de encuentro hasta recibir instrucciones.</li>
                            </ul>
                        </div>

                    </div>

                </div>

            </section>

        </section>
    </main>

    <?php include 'includes/public/footer.php'; ?>

</body>

</html>