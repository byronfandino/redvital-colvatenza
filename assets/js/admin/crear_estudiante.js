
document.addEventListener('DOMContentLoaded', () => {
    const listaAcudientes = document.getElementById('contenedor-acudientes');
    const plantillaAcudiente = document.getElementById('plantilla-acudiente');
    const textNumeroDocumento = document.getElementById('numero_documento');
    const botonAgregar = document.getElementById('agregar-acudiente');

    // Comprobar que existan los elementos necesarios.
    if (!listaAcudientes || !plantillaAcudiente || !botonAgregar) {
        console.error('No se encontraron los elementos necesarios para gestionar los acudientes.');
        return;
    }

    // Actualizar numeración, nombres e identificadores.
    function actualizarAcudientes() {
        const tarjetas = listaAcudientes.querySelectorAll('.contenedor-acudiente');

        tarjetas.forEach((tarjeta, indice) => {
            const leyenda = tarjeta.querySelector('legend');
            const botonQuitar = tarjeta.querySelector('.boton-quitar-acudiente');

            if (leyenda) {
                leyenda.textContent = `Acudiente ${indice + 1}`;
            }

            if (botonQuitar) {
                botonQuitar.hidden = tarjetas.length === 1;
            }

            tarjeta.querySelectorAll('[data-campo]').forEach((campo) => {
                const nombreCampo = campo.dataset.campo;
                const identificador = `acudiente_${nombreCampo}_${indice}`;

                campo.name = `acudientes[${indice}][${nombreCampo}]`;
                campo.id = identificador;

                const etiqueta = tarjeta.querySelector(
                    `[data-etiqueta="${nombreCampo}"]`
                );

                if (etiqueta) {
                    etiqueta.htmlFor = identificador;
                }
            });
        });
    }

    // Agregar un nuevo acudiente.
    botonAgregar.addEventListener('click', () => {
        console.log('Click en el botón agregar acudiente');

        const tarjetas = listaAcudientes.querySelectorAll('.contenedor-acudiente');

        if (tarjetas.length >= 20) {
            alert('Solo puedes registrar un máximo de 20 acudientes.');
            return;
        }

        const fragmento = plantillaAcudiente.content.cloneNode(true);
        listaAcudientes.appendChild(fragmento);

        actualizarAcudientes();

        const tarjetasActualizadas = listaAcudientes.querySelectorAll('.contenedor-acudiente');
        const ultimaTarjeta = tarjetasActualizadas[tarjetasActualizadas.length - 1];
        const primerCampo = ultimaTarjeta.querySelector('input');

        if (primerCampo) {
            primerCampo.focus();
        }
    });

    // Quitar un acudiente.
    listaAcudientes.addEventListener('click', (evento) => {
        const botonQuitar = evento.target.closest('.boton-quitar-acudiente');

        if (!botonQuitar || !listaAcudientes.contains(botonQuitar)) {
            return;
        }

        const tarjetas = listaAcudientes.querySelectorAll('.contenedor-acudiente');

        if (tarjetas.length <= 1) {
            return;
        }

        botonQuitar.closest('.contenedor-acudiente').remove();

        actualizarAcudientes();
    });

    // Inicializar los acudientes existentes.
    actualizarAcudientes();

    console.log('Gestión de acudientes inicializada correctamente.');

    textNumeroDocumento.focus();
});
