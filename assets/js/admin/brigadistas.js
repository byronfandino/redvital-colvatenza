
document.addEventListener('DOMContentLoaded', function () {
    const buscarNombre = document.getElementById('buscar-nombre');
    const buscarDocumento = document.getElementById('buscar-documento');
    const filtrarBrigada = document.getElementById('filtrar-brigada');
    const botonLimpiar = document.getElementById('limpiar-filtros');
    const filas = document.querySelectorAll('[data-brigadista]');
    const totalBrigadistas = document.getElementById('total-brigadistas');
    const resultadoBusqueda = document.getElementById('resultado-busqueda');
    const mensajeSinResultados = document.getElementById('sin-resultados');

    const totalInicial = filas.length;

    // Normalizar el texto para facilitar las búsquedas.
    function normalizarTexto(texto) {
        return texto
            .toLocaleLowerCase('es')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    // Aplicar los filtros seleccionados.
    function filtrarBrigadistas() {
        const nombreBuscado = normalizarTexto(buscarNombre.value);
        const documentoBuscado = normalizarTexto(buscarDocumento.value);
        const brigadaSeleccionada = filtrarBrigada.value;

        let cantidadVisible = 0;

        filas.forEach(function (fila) {
            const nombres = normalizarTexto(fila.dataset.nombres || '');
            const apellidos = normalizarTexto(fila.dataset.apellidos || '');
            const documento = normalizarTexto(fila.dataset.documento || '');
            const brigada = fila.dataset.brigada || '';

            const coincideNombre =
                nombreBuscado === '' ||
                nombres.includes(nombreBuscado) ||
                apellidos.includes(nombreBuscado) ||
                (nombres + ' ' + apellidos).includes(nombreBuscado);

            const coincideDocumento =
                documentoBuscado === '' ||
                documento.includes(documentoBuscado);

            const coincideBrigada =
                brigadaSeleccionada === '' ||
                brigada === brigadaSeleccionada;

            const mostrar =
                coincideNombre &&
                coincideDocumento &&
                coincideBrigada;

            fila.hidden = !mostrar;

            if (mostrar) {
                cantidadVisible++;
            }
        });

        // Actualizar el contador.
        totalBrigadistas.textContent = cantidadVisible;

        // Mostrar el resultado de la búsqueda.
        if (resultadoBusqueda) {
            if (
                nombreBuscado !== '' ||
                documentoBuscado !== '' ||
                brigadaSeleccionada !== ''
            ) {
                resultadoBusqueda.textContent =
                    'Se encontraron ' + cantidadVisible +
                    ' de ' + totalInicial + ' brigadistas.';
            } else {
                resultadoBusqueda.textContent = '';
            }
        }

        // Mostrar el mensaje cuando no hay coincidencias.
        if (mensajeSinResultados) {
            mensajeSinResultados.hidden = cantidadVisible !== 0;
        }
    }

    // Escuchar los cambios en los filtros.
    buscarNombre.addEventListener('input', filtrarBrigadistas);
    buscarDocumento.addEventListener('input', filtrarBrigadistas);
    filtrarBrigada.addEventListener('change', filtrarBrigadistas);

    // Limpiar todos los filtros.
    botonLimpiar.addEventListener('click', function () {
        buscarNombre.value = '';
        buscarDocumento.value = '';
        filtrarBrigada.value = '';

        filtrarBrigadistas();
    });

    // Mostrar el total inicial.
    filtrarBrigadistas();
});
