
document.addEventListener('DOMContentLoaded', function () {
    const archivoImagen = document.getElementById('imagen_nueva');
    const vistaPrevia = document.getElementById('vista-previa-icono');
    const mensajeVistaPrevia = document.getElementById('mensaje-vista-previa');
    const selectorColor = document.getElementById('color');
    const valorColor = document.getElementById('valor-color');

    let urlTemporalActual = null;

    // Gestionar la selección de una imagen nueva.
    if (archivoImagen && vistaPrevia && mensajeVistaPrevia) {
        archivoImagen.addEventListener('change', function () {
            if (urlTemporalActual !== null) {
                URL.revokeObjectURL(urlTemporalActual);
                urlTemporalActual = null;
            }

            const archivo = this.files[0];

            if (!archivo) {
                // En edición, conservar la imagen actual si no se selecciona otra.
                if (!vistaPrevia.getAttribute('src')) {
                    vistaPrevia.hidden = true;
                    mensajeVistaPrevia.textContent =
                        'Selecciona una imagen para visualizarla.';
                } else {
                    mensajeVistaPrevia.textContent =
                        'Imagen actual de la brigada.';
                }

                return;
            }

            // Validar el tamaño máximo de 5 MB.
            if (archivo.size > 5 * 1024 * 1024) {
                this.value = '';
                mensajeVistaPrevia.textContent =
                    'La imagen supera el tamaño máximo de 5 MB.';
                return;
            }

            // Validar la extensión y el tipo MIME.
            const formatosPermitidos = [
                'image/png',
                'image/jpeg',
                'image/webp',
                'image/svg+xml'
            ];

            const extensionesPermitidas = [
                'png',
                'jpg',
                'jpeg',
                'webp',
                'svg'
            ];

            const extension = archivo.name.split('.').pop().toLowerCase();

            if (
                !extensionesPermitidas.includes(extension) ||
                (
                    archivo.type !== '' &&
                    !formatosPermitidos.includes(archivo.type)
                )
            ) {
                this.value = '';
                mensajeVistaPrevia.textContent =
                    'Selecciona una imagen PNG, JPG, JPEG, WEBP o SVG válida.';
                return;
            }

            // Crear una URL temporal para mostrar la vista previa.
            urlTemporalActual = URL.createObjectURL(archivo);

            vistaPrevia.onload = function () {
                vistaPrevia.hidden = false;
                mensajeVistaPrevia.textContent =
                    'Vista previa de la nueva imagen.';
            };

            vistaPrevia.onerror = function () {
                vistaPrevia.hidden = true;
                mensajeVistaPrevia.textContent =
                    'No fue posible mostrar la imagen seleccionada.';
            };

            vistaPrevia.src = urlTemporalActual;
            vistaPrevia.hidden = false;
        });
    }

    // Actualizar el código hexadecimal del color.
    if (selectorColor && valorColor) {
        selectorColor.addEventListener('input', function () {
            valorColor.textContent = this.value.toUpperCase();
        });
    }
});
