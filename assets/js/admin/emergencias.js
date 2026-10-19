
document.addEventListener('DOMContentLoaded', () => {
    const campoNombre = document.getElementById('buscar-nombre-emergencia');
    const botonLimpiar = document.getElementById('limpiar-filtros-emergencias');
    const cuerpoTabla = document.getElementById('cuerpo-tabla-emergencias');
    const mensajeSinResultados = document.getElementById('mensaje-sin-resultados');
    const resultadoBusqueda = document.getElementById('resultado-busqueda-emergencias');

    if (!campoNombre || !botonLimpiar || !cuerpoTabla || !resultadoBusqueda) {
        console.error('No se encontraron los elementos necesarios para filtrar las emergencias.');
        return;
    }

    const filasEmergencias = cuerpoTabla.querySelectorAll('tr[data-nombre]');
    const totalEmergencias = filasEmergencias.length;

    // Filtrar los contactos por nombre.
    const filtrarEmergencias = () => {
        const nombreBuscado = campoNombre.value
            .trim()
            .toLocaleLowerCase('es');

        let coincidencias = 0;

        filasEmergencias.forEach((fila) => {
            const nombreEmergencia = fila.dataset.nombre
                .toLocaleLowerCase('es');

            const coincide = nombreEmergencia.includes(nombreBuscado);

            fila.hidden = !coincide;

            if (coincide) {
                coincidencias++;
            }
        });

        // Mostrar u ocultar el mensaje cuando no hay coincidencias.
        if (mensajeSinResultados) {
            mensajeSinResultados.hidden = coincidencias > 0;
        }

        // Actualizar el número de resultados mostrados.
        resultadoBusqueda.textContent = coincidencias === totalEmergencias
            ? `Se muestran ${coincidencias} contactos.`
            : `Se encontraron ${coincidencias} de ${totalEmergencias} contactos.`;
    };

    campoNombre.addEventListener('input', filtrarEmergencias);

    // Limpiar el campo y mostrar nuevamente todos los contactos.
    botonLimpiar.addEventListener('click', () => {
        campoNombre.value = '';
        filtrarEmergencias();
        campoNombre.focus();
    });
});
