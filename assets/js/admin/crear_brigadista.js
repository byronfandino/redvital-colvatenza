
document.addEventListener('DOMContentLoaded', () => {
    const selectorBrigada = document.getElementById('id_brigada');
    const muestraColor = document.getElementById('muestra-color-brigada');
    const textoColor = document.getElementById('texto-color-brigada');
    const campoDocumento = document.getElementById('numero_documento');

    // Comprobar que existan los elementos necesarios.
    if (!selectorBrigada || !muestraColor || !textoColor) {
        console.error('No se encontraron los elementos para mostrar el color de la brigada.');
        return;
    }

    // Actualizar la vista previa del color.
    function actualizarColorBrigada() {
        const opcionSeleccionada =
            selectorBrigada.options[selectorBrigada.selectedIndex];

        const color = opcionSeleccionada?.dataset.color || '';

        if (/^#[0-9a-fA-F]{6}$/.test(color)) {
            muestraColor.style.backgroundColor = color;
            textoColor.textContent = `Color institucional: ${color.toUpperCase()}`;
        } else {
            muestraColor.style.backgroundColor = '#ffffff';
            textoColor.textContent = 'Seleccione una brigada para ver su color.';
        }
    }

    // Escuchar los cambios en la brigada seleccionada.
    selectorBrigada.addEventListener('change', actualizarColorBrigada);

    // Inicializar la vista previa.
    actualizarColorBrigada();

    // Enfocar el campo de documento.
    if (campoDocumento) {
        campoDocumento.focus();
    }
});
