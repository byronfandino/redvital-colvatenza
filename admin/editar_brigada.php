
<?php

$pagina_activa = 'brigadas';
$rol_requerido = 'administrador';

require_once __DIR__ . '/verificar_rol.php';
require_once __DIR__ . '/../config/database.php';

$id_brigada = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_brigada || $id_brigada < 1) {
    $_SESSION['mensaje_estado'] = 'El identificador de la brigada no es válido.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: brigadas.php');
    exit;
}

$ruta_imagenes = __DIR__ . '/../assets/images/';
$url_imagenes = '../assets/images/';

$mensaje = '';
$tipo_mensaje = '';
$errores = [];

// Consultar la brigada que se va a editar.
$consulta = $pdo->prepare(
    'SELECT id_brigada, nombre, descripcion, color, icono
     FROM brigadas
     WHERE id_brigada = :id_brigada
     LIMIT 1'
);

$consulta->execute([
    'id_brigada' => $id_brigada
]);

$brigada = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$brigada) {
    $_SESSION['mensaje_estado'] = 'La brigada que intentas editar no existe.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: brigadas.php');
    exit;
}

// Precargar los datos actuales.
$nombre = $brigada['nombre'];
$descripcion = $brigada['descripcion'];
$color = strtoupper($brigada['color']);
$icono_actual = $brigada['icono'];
$icono = $icono_actual;

// Procesar el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $color = strtoupper(trim($_POST['color'] ?? '#075447'));

    $archivo_subido = $_FILES['imagen_nueva'] ?? null;
    $hay_archivo_nuevo = (
        $archivo_subido !== null &&
        isset($archivo_subido['error']) &&
        $archivo_subido['error'] !== UPLOAD_ERR_NO_FILE
    );

    $errores = [];

    // Validar el nombre.
    if ($nombre === '') {
        $errores[] = 'El nombre de la brigada es obligatorio.';
    } elseif (mb_strlen($nombre) > 100) {
        $errores[] = 'El nombre no puede superar los 100 caracteres.';
    }

    // Validar la descripción.
    if ($descripcion === '') {
        $errores[] = 'La descripción es obligatoria.';
    } elseif (mb_strlen($descripcion) > 255) {
        $errores[] = 'La descripción no puede superar los 255 caracteres.';
    }

    // Validar el color hexadecimal.
    if (!preg_match('/^#[0-9A-F]{6}$/i', $color)) {
        $errores[] = 'Selecciona un color hexadecimal válido.';
    }

    // Validar el estado de la subida, si se seleccionó un archivo.
    if (
        $hay_archivo_nuevo &&
        $archivo_subido['error'] !== UPLOAD_ERR_OK
    ) {
        $errores[] = 'No fue posible recibir la imagen. Intenta nuevamente.';
    }

    $extension_nueva = '';
    $tipo_mime = '';

    // Validar el formato, contenido y tamaño de la imagen nueva.
    if (empty($errores) && $hay_archivo_nuevo) {
        $extension_nueva = strtolower(
            pathinfo($archivo_subido['name'], PATHINFO_EXTENSION)
        );

        $formatos_permitidos = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
        ];

        if (!array_key_exists($extension_nueva, $formatos_permitidos)) {
            $errores[] = 'La imagen debe estar en formato PNG, JPG, JPEG, WEBP o SVG.';
        } elseif (!is_uploaded_file($archivo_subido['tmp_name'])) {
            $errores[] = 'El archivo recibido no es una subida válida.';
        } elseif ($archivo_subido['size'] > 5 * 1024 * 1024) {
            $errores[] = 'La imagen no puede superar los 5 MB.';
        } else {
            // Comprobar el tipo MIME real del archivo.
            $informacion_archivo = finfo_open(FILEINFO_MIME_TYPE);
            $tipo_mime = false;

            if ($informacion_archivo !== false) {
                $tipo_mime = finfo_file(
                    $informacion_archivo,
                    $archivo_subido['tmp_name']
                );

                finfo_close($informacion_archivo);
            }

            if ($extension_nueva === 'svg') {
                // Validar el contenido XML del archivo SVG.
                $contenido_svg = file_get_contents(
                    $archivo_subido['tmp_name']
                );

                $svg_valido = false;

                if ($contenido_svg !== false) {
                    // Rechazar contenido activo y declaraciones de entidades.
                    $contiene_contenido_peligroso = preg_match(
                        '/<!DOCTYPE|<!ENTITY|<script\b|\bon\w+\s*=|javascript\s*:|<foreignObject\b/i',
                        $contenido_svg
                    );

                    // Verificar que exista un elemento SVG.
                    $tiene_elemento_svg = preg_match(
                        '/<svg\b/i',
                        $contenido_svg
                    );

                    if (
                        $contiene_contenido_peligroso === 0 &&
                        $tiene_elemento_svg === 1
                    ) {
                        // Comprobar que el documento XML sea válido.
                        $errores_xml_previos = libxml_use_internal_errors(true);

                        try {
                            $documento_xml = new DOMDocument();

                            $documento_xml->resolveExternals = false;
                            $documento_xml->substituteEntities = false;

                            $xml_cargado = $documento_xml->loadXML(
                                $contenido_svg,
                                LIBXML_NONET
                            );

                            $elemento_raiz = $documento_xml->documentElement;

                            if (
                                $xml_cargado &&
                                $elemento_raiz !== null &&
                                $elemento_raiz->localName === 'svg' &&
                                $elemento_raiz->namespaceURI === 'http://www.w3.org/2000/svg'
                            ) {
                                $svg_valido = true;
                            }
                        } finally {
                            libxml_clear_errors();
                            libxml_use_internal_errors($errores_xml_previos);
                        }
                    }
                }

                if (!$svg_valido) {
                    $errores[] = 'El archivo SVG no es válido o contiene elementos no permitidos.';
                }
            } else {
                // Validar imágenes PNG, JPG, JPEG y WEBP.
                if (
                    $tipo_mime === false ||
                    $tipo_mime !== $formatos_permitidos[$extension_nueva] ||
                    @getimagesize($archivo_subido['tmp_name']) === false
                ) {
                    $errores[] = 'El archivo seleccionado no es una imagen válida.';
                }
            }
        }
    }

    // Comprobar que el nombre no pertenezca a otra brigada.
    if (empty($errores)) {
        $consulta = $pdo->prepare(
            'SELECT COUNT(*)
             FROM brigadas
             WHERE LOWER(nombre) = LOWER(:nombre)
               AND id_brigada <> :id_brigada'
        );

        $consulta->execute([
            'nombre' => $nombre,
            'id_brigada' => $id_brigada
        ]);

        if ((int) $consulta->fetchColumn() > 0) {
            $errores[] = 'Ya existe otra brigada con ese nombre.';
        }
    }

    // Verificar la carpeta de imágenes si se subió una imagen nueva.
    if (empty($errores) && $hay_archivo_nuevo) {
        if (!is_dir($ruta_imagenes) || !is_writable($ruta_imagenes)) {
            $errores[] = 'La carpeta de imágenes no existe o no permite guardar archivos.';
        }
    }

    // Conservar la imagen actual si no se seleccionó otra.
    $icono = $icono_actual;
    $ruta_imagen_guardada = null;

    // Guardar la imagen nueva con un nombre único.
    if (empty($errores) && $hay_archivo_nuevo) {
        try {
            do {
                $nombre_generado = 'brigada_'
                    . bin2hex(random_bytes(16))
                    . '.'
                    . $extension_nueva;

                $destino = $ruta_imagenes . $nombre_generado;
            } while (file_exists($destino));

            if (move_uploaded_file($archivo_subido['tmp_name'], $destino)) {
                $icono = $nombre_generado;
                $ruta_imagen_guardada = $destino;
            } else {
                $errores[] = 'No fue posible guardar la imagen en el servidor.';
            }
        } catch (Throwable $e) {
            error_log(
                'Error al guardar la imagen de la brigada: '
                . $e->getMessage()
            );

            $errores[] = 'Ocurrió un error al guardar la imagen.';
        }
    }

    // Actualizar los datos de la brigada.
    if (empty($errores)) {
        try {
            $consulta = $pdo->prepare(
                'UPDATE brigadas
                 SET nombre = :nombre,
                     descripcion = :descripcion,
                     color = :color,
                     icono = :icono,
                     actualizado_por = :actualizado_por
                 WHERE id_brigada = :id_brigada'
            );

            $consulta->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'color' => $color,
                'icono' => $icono,
                'actualizado_por' => $_SESSION['id_usuario'],
                'id_brigada' => $id_brigada
            ]);

            $_SESSION['mensaje_estado'] = 'La brigada se actualizó correctamente.';
            $_SESSION['tipo_mensaje_estado'] = 'exito';

            header('Location: brigadas.php');
            exit;
        } catch (PDOException $e) {
            error_log('Error al actualizar brigada: ' . $e->getMessage());

            $mensaje = 'No fue posible actualizar la brigada. Inténtalo nuevamente.';
            $tipo_mensaje = 'error';
        }
    }

    // Mostrar los errores de validación.
    if (!empty($errores)) {
        $mensaje = implode(' ', $errores);
        $tipo_mensaje = 'error';
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar brigada | RedVital Colvatenza</title>

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/crear_usuario.css">
    <link rel="stylesheet" href="../assets/css/admin/crear_brigada.css">
</head>
<body>

<div class="estructura-administrativa">

    <?php require_once __DIR__ . '/includes/menu.php'; ?>

    <main class="contenido-administrativo">

        <section class="crear-usuario">

            <div class="encabezado-formulario">
                <div>
                    <h1>Editar brigada</h1>
                    <p>Actualiza la información y la identificación visual de la brigada.</p>
                </div>
            </div>

            <div class="contenido-formulario">

                <div class="introduccion-formulario">
                    <h2>Información de la brigada</h2>
                    <p>Modifica los campos que necesites y guarda los cambios.</p>
                </div>

                <?php if ($mensaje !== ''): ?>
                    <div class="mensaje-formulario mensaje-<?= htmlspecialchars($tipo_mensaje, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form
                    action="editar_brigada.php?id=<?= (int) $id_brigada ?>"
                    method="POST"
                    enctype="multipart/form-data"
                    class="formulario-administrativo"
                >

                    <div class="campos-formulario">

                        <div class="campo-formulario campo-completo">
                            <label for="nombre">Nombre de la brigada</label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                maxlength="100"
                                value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="campo-formulario campo-completo">
                            <label for="descripcion">Descripción</label>

                            <textarea
                                id="descripcion"
                                name="descripcion"
                                maxlength="255"
                                rows="4"
                                required
                            ><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div class="campo-formulario">
                            <label for="color">Color de identificación</label>

                            <div class="selector-color">
                                <input
                                    type="color"
                                    id="color"
                                    name="color"
                                    value="<?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >

                                <span id="valor-color">
                                    <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                        </div>

                        <div class="campo-formulario campo-completo">
                            <label>Imagen actual</label>

                            <div class="vista-previa-brigada">

                                <p>Imagen registrada en la brigada</p>

                                <img
                                    src="<?= htmlspecialchars(
                                        $url_imagenes . rawurlencode(basename($icono_actual)),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    alt="Imagen actual de la brigada"
                                    id="vista-previa-icono"
                                >

                                <span id="mensaje-vista-previa">
                                    Imagen actual de la brigada.
                                </span>

                            </div>
                        </div>

                        <div class="campo-formulario campo-completo">
                            <label for="imagen_nueva">Cambiar imagen (opcional)</label>

                            <input
                                type="file"
                                id="imagen_nueva"
                                name="imagen_nueva"
                                accept=".png,.jpg,.jpeg,.webp,.svg,image/png,image/jpeg,image/webp,image/svg+xml"
                            >

                            <small>
                                Si no seleccionas otra imagen, se conservará la actual.
                                Formatos permitidos: PNG, JPG, JPEG, WEBP y SVG.
                                Tamaño máximo: 5 MB.
                                La nueva imagen se guardará con un nombre único.
                            </small>
                        </div>

                    </div>

                    <div class="acciones-formulario">

                        <a href="brigadas.php" class="boton-cancelar">
                            Cancelar
                        </a>

                        <button type="submit" class="boton-guardar">
                            Guardar cambios
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>

<script src="../assets/js/admin/menu.js" defer></script>
<script src="../assets/js/admin/editar_brigada.js" defer></script>

</body>
</html>
