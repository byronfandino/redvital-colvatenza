
document.addEventListener('DOMContentLoaded', function () {
    const archivoImagen = document.getElementById('imagen_nueva');
    const vistaPrevia = document.getElementById('vista-previa-icono');
    const mensajeVistaPrevia = document.getElementById('mensaje-vista-previa');
    const selectorColor = document.getElementById('color');
    const valorColor = document.getElementById('valor-color');

    let urlTemporalActual = null;

    // Mostrar la imagen seleccionada.
    archivoImagen.addEventListener('change', function () {
        if (urlTemporalActual !== null) {
            URL.revokeObjectURL(urlTemporalActual);
            urlTemporalActual = null;
        }

        const archivo = this.files[0];

        if (!archivo) {
            vistaPrevia.removeAttribute('src');
            vistaPrevia.hidden = true;
            mensajeVistaPrevia.textContent = 'Selecciona una imagen para visualizarla.';
            return;
        }

        // Validar el tamaño máximo de 5 MB.
        if (archivo.size > 5 * 1024 * 1024) {
            this.value = '';
            vistaPrevia.removeAttribute('src');
            vistaPrevia.hidden = true;
            mensajeVistaPrevia.textContent = 'La imagen supera el tamaño máximo de 5 MB.';
            return;
        }

        // Definir los formatos permitidos.
        const formatosPermitidos = [
            'image/png',
            'image/jpeg',
            'image/webp',
            'image/svg+xml'
        ];

        const extension = archivo.name.split('.').pop().toLowerCase();
        const extensionesPermitidas = ['png', 'jpg', 'jpeg', 'webp', 'svg'];

        // Validar la extensión y el tipo MIME.
        if (
            !extensionesPermitidas.includes(extension) ||
            (archivo.type !== '' && !formatosPermitidos.includes(archivo.type))
        ) {
            this.value = '';
            vistaPrevia.removeAttribute('src');
            vistaPrevia.hidden = true;
            mensajeVistaPrevia.textContent =
                'Selecciona una imagen PNG, JPG, JPEG, WEBP o SVG válida.';
            return;
        }

        // Crear una URL temporal para mostrar la vista previa.
        urlTemporalActual = URL.createObjectURL(archivo);

        vistaPrevia.onload = function () {
            mensajeVistaPrevia.textContent = 'Vista previa de la imagen seleccionada.';
        };

        vistaPrevia.onerror = function () {
            vistaPrevia.hidden = true;
            mensajeVistaPrevia.textContent =
                'No fue posible mostrar la imagen. Verifica que el archivo sea válido.';
        };

        vistaPrevia.src = urlTemporalActual;
        vistaPrevia.hidden = false;
    });

    // Actualizar el código hexadecimal del color.
    selectorColor.addEventListener('input', function () {
        valorColor.textContent = this.value.toUpperCase();
    });
});
