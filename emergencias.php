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
    <?php include 'includes/public/menu.php'; ?>

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

                    <!-- Caídas y resbalones -->
                    <a href="#caidas-resbalones" class="tarjeta-emergencia tarjeta-incidente">
                        <div class="icono-emergencia"><img src="assets/images/caida.png" alt="Caídas y resbalones"></div>
                        <div class="contenido-tarjeta"><h2>Caídas y resbalones</h2><p>Golpes, esguinces y posibles fracturas</p></div>
                        <span class="flecha-emergencia">+</span>
                    </a>

                    <!-- Cortes y heridas -->
                    <a href="#cortes-heridas" class="tarjeta-emergencia tarjeta-incidente">
                        <div class="icono-emergencia"><img src="assets/images/cortes.png" alt="Cortes y heridas"></div>
                        <div class="contenido-tarjeta"><h2>Cortes y heridas</h2><p>Cómo controlar el sangrado y proteger la lesión</p></div>
                        <span class="flecha-emergencia">+</span>
                    </a>

                    <!-- Golpes en la cabeza -->
                    <a href="#golpes-cabeza" class="tarjeta-emergencia tarjeta-incidente">
                        <div class="icono-emergencia"><img src="assets/images/golpe-cabeza.png" alt="Golpes en la cabeza"></div>
                        <div class="contenido-tarjeta"><h2>Golpes en la cabeza</h2><p>Cuidados iniciales y señales de alarma</p></div>
                        <span class="flecha-emergencia">+</span>
                    </a>

                    <!-- Atragantamiento -->
                    <a href="#atragantamiento" class="tarjeta-emergencia tarjeta-incidente">
                        <div class="icono-emergencia"><img src="assets/images/atragantamiento.png" alt="Atragantamiento"></div>
                        <div class="contenido-tarjeta"><h2>Atragantamiento</h2><p>Actuación ante una obstrucción respiratoria</p></div>
                        <span class="flecha-emergencia">+</span>
                    </a>

                    <!-- Quemaduras -->
                    <a href="#quemaduras" class="tarjeta-emergencia tarjeta-incidente">
                        <div class="icono-emergencia"><img src="assets/images/quemadura.png" alt="Quemaduras"></div>
                        <div class="contenido-tarjeta"><h2>Quemaduras</h2><p>Qué hacer ante calor o sustancias químicas</p></div>
                        <span class="flecha-emergencia">+</span>
                    </a>

                    <!-- Desmayos -->
                    <a href="#desmayos" class="tarjeta-emergencia tarjeta-incidente">
                        <div class="icono-emergencia"><img src="assets/images/desmayo.png" alt="Desmayos"></div>
                        <div class="contenido-tarjeta"><h2>Desmayos</h2><p>Atención inicial y cuándo pedir ayuda</p></div>
                        <span class="flecha-emergencia">+</span>
                    </a>

                    <!-- Lesiones oculares -->
                    <a href="#lesiones-oculares" class="tarjeta-emergencia tarjeta-incidente">
                        <div class="icono-emergencia"><img src="assets/images/golpe-ojo.png" alt="Lesiones oculares"></div>
                        <div class="contenido-tarjeta"><h2>Lesiones oculares</h2><p>Polvo, partículas, salpicaduras y golpes</p></div>
                        <span class="flecha-emergencia">+</span>
                    </a>

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
                    <!-- <a href="#primeros-auxilios" class="tarjeta-emergencia tarjeta-accidente">
                        <div class="icono-emergencia">
                            <img src="assets/images/primeros-auxilios.png" alt="Primeros auxilios">
                        </div>

                        <div class="contenido-tarjeta">
                            <h2>Accidente / Primeros auxilios</h2>
                            <p>Qué hacer ante lesiones o accidentes</p>
                        </div>

                        <span class="flecha-emergencia">+</span>
                    </a> -->

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

        <!-- Incidentes escolares frecuentes -->
        <section class="seccion-incidentes" id="incidentes-escolares">
            <div class="contenedor">
                <div class="introduccion-incidentes">
                    <span class="etiqueta-seccion-incidentes">ATENCIÓN INICIAL</span>
                    <h2>Incidentes frecuentes en el colegio</h2>
                    <p>Conoce cómo actuar ante situaciones que pueden presentarse en salones, pasillos, escaleras, patio, comedor o laboratorios. Avisa siempre al personal responsable de la institución.</p>
                </div>

                <div class="lista-incidentes">
                    <article id="caidas-resbalones" class="tarjeta-incidente-detalle color-incidente-verde">
                        <div class="cabecera-incidente"><span class="numero-incidente">01</span><div><h3>Caídas y resbalones</h3><p>Pasillos, escaleras o patio</p></div></div>
                        <div class="cuerpo-incidente"><div><h4>¿Qué puede pasar?</h4><p>Golpes, raspones, contusiones, esguinces o posibles fracturas en muñecas, brazos o piernas.</p></div><div><h4>¿Qué hacer?</h4><ul><li>Comprueba que el lugar sea seguro y avisa a un adulto responsable.</li><li>No muevas bruscamente al estudiante si sospechas una fractura grave o lesión en cuello o columna; solicita ayuda médica.</li><li>En un golpe leve, aplica frío envuelto en una tela. Si hay un raspón superficial, lávalo con agua limpia y jabón.</li><li>Informa al servicio médico escolar y a los padres o acudientes. Solicita valoración médica si hay dolor intenso, deformidad o dificultad para apoyar una extremidad.</li></ul></div></div>
                    </article>

                    <article id="cortes-heridas" class="tarjeta-incidente-detalle color-incidente-rojo">
                        <div class="cabecera-incidente"><span class="numero-incidente">02</span><div><h3>Cortes y heridas</h3><p>Tijeras, laboratorio o cristales</p></div></div>
                        <div class="cuerpo-incidente"><div><h4>¿Qué puede pasar?</h4><p>Heridas superficiales o profundas y sangrado.</p></div><div><h4>¿Qué hacer?</h4><ul><li>Lávate las manos y usa guantes si están disponibles.</li><li>Presiona la herida con una gasa o paño limpio para controlar el sangrado. Si el sangrado es abundante, pide ayuda urgente y mantén la presión.</li><li>Cuando el sangrado esté controlado y la herida sea superficial, enjuaga con agua limpia y cúbrela con un apósito estéril.</li><li>No retires objetos incrustados ni explores heridas profundas. Busca atención urgente si el sangrado no cede, la herida es profunda o fue causada por un objeto contaminado.</li></ul></div></div>
                    </article>

                    <article id="golpes-cabeza" class="tarjeta-incidente-detalle color-incidente-azul">
                        <div class="cabecera-incidente"><span class="numero-incidente">03</span><div><h3>Golpes en la cabeza</h3><p>Caídas o impactos fuertes con balones</p></div></div>
                        <div class="cuerpo-incidente"><div><h4>¿Qué puede pasar?</h4><p>Dolor, chichón, mareo o náuseas. Algunos síntomas pueden aparecer más tarde.</p></div><div><h4>¿Qué hacer?</h4><ul><li>Suspende la actividad, mantén al estudiante acompañado y avisa al personal responsable y a su acudiente.</li><li>Aplica frío envuelto en una tela, sin presionar. No lo dejes solo ni permitas que vuelva a jugar.</li><li>No muevas el cuello si sospechas una lesión cervical.</li><li><strong>Busca atención médica urgente</strong> ante pérdida de conciencia, vómitos repetidos, confusión, convulsiones, somnolencia inusual, dolor que empeora, visión alterada o sangre/líquido por nariz u oídos.</li></ul></div></div>
                    </article>

                    <article id="atragantamiento" class="tarjeta-incidente-detalle color-incidente-naranja">
                        <div class="cabecera-incidente"><span class="numero-incidente">04</span><div><h3>Atragantamiento</h3><p>Comedor o recreo</p></div></div>
                        <div class="cuerpo-incidente"><div><h4>¿Qué puede pasar?</h4><p>Un alimento u objeto puede obstruir parcial o totalmente las vías respiratorias.</p></div><div><h4>¿Qué hacer?</h4><ul><li>Si puede hablar, respirar y toser con fuerza, anímalo a seguir tosiendo y vigílalo.</li><li>Si no puede respirar, hablar ni toser eficazmente, pide a alguien que active emergencias de inmediato y aplica primeros auxilios para atragantamiento adecuados a su edad, únicamente si sabes realizarlos.</li><li>Las maniobras cambian según la edad; no se realizan compresiones abdominales en bebés menores de un año.</li><li>Si pierde el conocimiento, colócalo con cuidado en el suelo, activa emergencias e inicia RCP siguiendo las instrucciones del operador y tu nivel de formación.</li></ul></div></div>
                    </article>

                    <article id="quemaduras" class="tarjeta-incidente-detalle color-incidente-naranja">
                        <div class="cabecera-incidente"><span class="numero-incidente">05</span><div><h3>Quemaduras</h3><p>Laboratorio de química o comedor</p></div></div>
                        <div class="cuerpo-incidente"><div><h4>¿Qué puede pasar?</h4><p>Lesiones por superficies calientes, líquidos hirviendo o sustancias químicas.</p></div><div><h4>¿Qué hacer?</h4><ul><li>En quemaduras térmicas, enfría la zona con agua corriente fresca durante 20 minutos si es posible. No uses hielo, pasta dental, pomadas ni remedios caseros.</li><li>No retires ropa pegada a la piel. Quita con cuidado anillos u objetos cercanos si no están adheridos ni causan más lesión.</li><li>En quemaduras químicas, evita el contacto con la sustancia, avisa al responsable del laboratorio y sigue la ficha de seguridad. Retira ropa contaminada si puede hacerse sin exponerte y enjuaga con abundante agua, salvo indicación específica del producto.</li><li>Solicita atención médica urgente en quemaduras extensas, profundas, químicas o que afecten cara, manos, genitales o vías respiratorias.</li></ul></div></div>
                    </article>

                    <article id="desmayos" class="tarjeta-incidente-detalle color-incidente-verde">
                        <div class="cabecera-incidente"><span class="numero-incidente">06</span><div><h3>Desmayos o lipotimias</h3><p>Calor, ayuno o esfuerzo físico</p></div></div>
                        <div class="cuerpo-incidente"><div><h4>¿Qué puede pasar?</h4><p>Mareo, debilidad o pérdida breve de la conciencia.</p></div><div><h4>¿Qué hacer?</h4><ul><li>Acuesta a la persona boca arriba en un lugar seguro y ventilado. Si no hay lesión y respira con normalidad, puedes elevarle ligeramente las piernas.</li><li>Afloja prendas ajustadas y vigila su respiración. No la dejes sola.</li><li>No le des agua, alimentos ni medicamentos hasta que esté completamente despierta y pueda tragar con normalidad.</li><li>Si no recupera pronto la conciencia, tiene dificultad para respirar, dolor en el pecho, convulsiones o se lesionó al caer, activa emergencias. Si no respira normalmente, inicia RCP si sabes hacerlo.</li></ul></div></div>
                    </article>

                    <article id="lesiones-oculares" class="tarjeta-incidente-detalle color-incidente-azul">
                        <div class="cabecera-incidente"><span class="numero-incidente">07</span><div><h3>Lesiones oculares</h3><p>Balones, polvo, partículas o salpicaduras</p></div></div>
                        <div class="cuerpo-incidente"><div><h4>¿Qué puede pasar?</h4><p>Irritación, cuerpo extraño, salpicadura química o golpe directo en el ojo.</p></div><div><h4>¿Qué hacer?</h4><ul><li>No permitas que la persona se frote el ojo.</li><li>Si hay polvo suelto, enjuaga suavemente con agua limpia o suero fisiológico. Ante una salpicadura química, inicia inmediatamente un lavado abundante y continuo y solicita asistencia urgente.</li><li>No intentes retirar objetos incrustados ni presiones el ojo lesionado.</li><li>Si hubo un golpe fuerte, dolor intenso, cambios en la visión o un objeto incrustado, protege el ojo sin presión y acude de inmediato a urgencias.</li></ul></div></div>
                    </article>
                </div>

                <div class="aviso-incidentes"><strong>Importante:</strong> esta información es orientativa y no sustituye la capacitación en primeros auxilios ni la valoración de profesionales de salud. Ante una situación grave, activa el servicio de emergencias y sigue sus instrucciones.</div>
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

            <!-- <section id="primeros-auxilios" class="emergencia emergencia-primeros-auxilios">

                <div class="contenedor">

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
            </section> -->

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
                            <img src="assets/images/ruta-evacuacion.png" alt="Ruta de evacuación">
                        </div>
                    </div>
                    
                    <!-- Punto de encuentro -->
                    <div class="punto-encuentro">

                        <div class="contenido-punto-encuentro">
                            <span class="etiqueta-emergencia">PUNTO DE ENCUENTRO</span>
                            <h3>¿Dónde reunirse después de evacuar?</h3>
                            <p>
                                Una vez evacuada la institución, dirígete al punto de encuentro
                                y permanece allí hasta recibir nuevas instrucciones.
                            </p>

                        </div>

                        <div class="imagen-punto-encuentro">
                            <img src="assets/images/punto-encuentro.jpeg" alt="Punto de encuentro de la institución">
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

            <!-- Instructivo de emergencias -->
            <section class="seccion-instructivo">
                <div class="contenedor">

                    <div class="contenido-instructivo">

                        <div class="icono-instructivo">
                            <img src="assets/images/icono-instructivo.svg" alt="Documento institucional">
                        </div>

                        <div class="texto-instructivo">
                            <h2>Instructivo de emergencias</h2>
                            <p>Consulta y descarga el documento institucional con las orientaciones para actuar ante una emergencia.</p>
                        </div>

                        <a href="assets/documentos/instructivo.pdf"
                        class="boton-instructivo"
                        download>
                            <span>Descargar instructivo PDF</span>
                        </a>

                    </div>
                </div>
            </section>
        </section>
    </main>

    <?php include 'includes/public/pie_pagina.php'; ?>

</body>

</html>