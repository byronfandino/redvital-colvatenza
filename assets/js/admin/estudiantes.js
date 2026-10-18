
document.addEventListener('DOMContentLoaded', () => {
    const campoBusqueda = document.getElementById('buscar-estudiante');
    const filtroGrado = document.getElementById('filtrar-grado');
    const botonLimpiar = document.getElementById('limpiar-filtros');
    const cuerpoTabla = document.getElementById('cuerpo-tabla-estudiantes');
    const totalEstudiantes = document.getElementById('total-estudiantes');
    const resultadoBusqueda = document.getElementById('resultado-busqueda');
    const mensajeSinResultados = document.getElementById('sin-resultados');

    const modal = document.getElementById('modal-estudiante');
    const contenedorDetalles = document.getElementById('detalles-modal-estudiante');

    // Si la tabla no existe, no hay registros para filtrar.
    if (!campoBusqueda || !filtroGrado || !cuerpoTabla) {
        return;
    }

    const filas = Array.from(cuerpoTabla.querySelectorAll('[data-estudiante]'));
    const totalInicial = filas.length;

    // Filtrar por documento, nombres, apellidos y grado.
    function filtrarEstudiantes() {
        const busqueda = campoBusqueda.value.trim().toLocaleLowerCase('es');
        const cursoSeleccionado = filtroGrado.value;

        let cantidadVisible = 0;

        filas.forEach((fila) => {
            const documento = (fila.dataset.documento || '').toLocaleLowerCase('es');
            const nombres = (fila.dataset.nombres || '').toLocaleLowerCase('es');
            const apellidos = (fila.dataset.apellidos || '').toLocaleLowerCase('es');
            const grado = fila.dataset.grado || '';
            const grupo = fila.dataset.grupo || '';
            const curso = `${grado}-${grupo}`;

            const coincideTexto =
                documento.includes(busqueda) ||
                nombres.includes(busqueda) ||
                apellidos.includes(busqueda);

            const coincideCurso =
                cursoSeleccionado === '' || curso === cursoSeleccionado;

            const mostrar = coincideTexto && coincideCurso;

            fila.hidden = !mostrar;

            if (mostrar) {
                cantidadVisible++;
            }
        });

        if (totalEstudiantes) {
            totalEstudiantes.textContent = cantidadVisible;
        }

        if (resultadoBusqueda) {
            resultadoBusqueda.textContent =
                `Mostrando ${cantidadVisible} de ${totalInicial} estudiantes.`;
        }

        if (mensajeSinResultados) {
            mensajeSinResultados.hidden = cantidadVisible !== 0;
        }
    }

    campoBusqueda.addEventListener('input', filtrarEstudiantes);
    filtroGrado.addEventListener('change', filtrarEstudiantes);

    // Limpiar los filtros.
    if (botonLimpiar) {
        botonLimpiar.addEventListener('click', () => {
            campoBusqueda.value = '';
            filtroGrado.value = '';
            filtrarEstudiantes();
            campoBusqueda.focus();
        });
    }

    // Crear un campo de detalle sin interpretar HTML recibido.
    function crearCampoDetalle(etiqueta, valor) {
        const campo = document.createElement('div');
        campo.className = 'detalle-campo';

        const titulo = document.createElement('strong');
        titulo.textContent = etiqueta;

        const contenido = document.createElement('span');
        contenido.textContent =
            valor === null || valor === undefined || String(valor).trim() === ''
                ? 'Sin registrar'
                : String(valor);

        campo.append(titulo, contenido);

        return campo;
    }

    // Crear un grupo de información para el modal.
    function crearGrupoDetalle(titulo, campos) {
        const grupo = document.createElement('section');
        grupo.className = 'detalle-grupo';

        const encabezado = document.createElement('h3');
        encabezado.textContent = titulo;

        const cuadricula = document.createElement('div');
        cuadricula.className = 'detalle-grid';

        campos.forEach(([etiqueta, valor]) => {
            cuadricula.appendChild(crearCampoDetalle(etiqueta, valor));
        });

        grupo.append(encabezado, cuadricula);

        return grupo;
    }

    // Mostrar la información completa de un estudiante.
    function mostrarDetalles(datos) {
        contenedorDetalles.replaceChildren();

        contenedorDetalles.appendChild(
            crearGrupoDetalle('Información personal', [
                [
                    'Tipo de documento',
                    datos.tipo_documento && datos.abreviatura_documento
                        ? `${datos.tipo_documento} (${datos.abreviatura_documento})`
                        : datos.tipo_documento
                ],
                ['Número de documento', datos.numero_documento],
                ['Nombres', datos.nombres],
                ['Apellidos', datos.apellidos],
                ['Fecha de nacimiento', datos.fecha_nacimiento],
                ['Tipo de sangre', datos.tipo_sangre],
                ['Dirección de residencia', datos.direccion_residencia],
                ['Teléfono de contacto', datos.telefono_contacto],
                [
                    'Condiciones especiales',
                    datos.condiciones_especiales
                ],
                ['Estado', Number(datos.activo) === 1 ? 'Activo' : 'Inactivo']
            ])
        );

        contenedorDetalles.appendChild(
            crearGrupoDetalle('Información académica', [
                ['Curso', `${datos.grado || ''} - ${datos.grupo || ''}`]
            ])
        );

        const grupoAcudientes = document.createElement('section');
        grupoAcudientes.className = 'detalle-grupo';

        const tituloAcudientes = document.createElement('h3');
        tituloAcudientes.textContent = 'Acudientes';

        grupoAcudientes.appendChild(tituloAcudientes);

        const acudientes = Array.isArray(datos.acudientes)
            ? datos.acudientes
            : [];

        if (acudientes.length === 0) {
            const sinAcudientes = document.createElement('p');
            sinAcudientes.className = 'mensaje-vacio';
            sinAcudientes.textContent = 'Este estudiante no tiene acudientes registrados.';
            grupoAcudientes.appendChild(sinAcudientes);
        } else {
            acudientes.forEach((acudiente, indice) => {
                const tarjeta = document.createElement('div');
                tarjeta.className = 'acudiente-modal';

                const titulo = document.createElement('h4');
                titulo.textContent = `Acudiente ${indice + 1}`;

                const cuadricula = document.createElement('div');
                cuadricula.className = 'detalle-grid';

                [
                    ['Parentesco', acudiente.parentesco],
                    ['Número de documento', acudiente.numero_documento],
                    ['Nombres', acudiente.nombres],
                    ['Apellidos', acudiente.apellidos],
                    ['Teléfono', acudiente.telefono_contacto],
                    ['Correo electrónico', acudiente.correo_electronico],
                    ['Dirección', acudiente.direccion_residencia]
                ].forEach(([etiqueta, valor]) => {
                    cuadricula.appendChild(crearCampoDetalle(etiqueta, valor));
                });

                tarjeta.append(titulo, cuadricula);
                grupoAcudientes.appendChild(tarjeta);
            });
        }

        contenedorDetalles.appendChild(grupoAcudientes);

        contenedorDetalles.appendChild(
            crearGrupoDetalle('Registro y auditoría', [
                ['Fecha de creación', datos.creado_en],
                ['Creado por', datos.nombre_creador],
                ['Fecha de actualización', datos.actualizado_en],
                ['Actualizado por', datos.nombre_actualizador]
            ])
        );

        modal.hidden = false;
        document.body.style.overflow = 'hidden';

        const botonCerrar = modal.querySelector('.boton-cerrar-modal');

        if (botonCerrar) {
            botonCerrar.focus();
        }
    }

    // Abrir el modal desde el botón Ver.
    cuerpoTabla.addEventListener('click', (evento) => {
        const botonVer = evento.target.closest('[data-detalles]');

        if (!botonVer) {
            return;
        }

        try {
            const datos = JSON.parse(botonVer.dataset.detalles);
            mostrarDetalles(datos);
        } catch (error) {
            console.error('No fue posible cargar los detalles del estudiante.', error);
        }
    });

    // Cerrar el modal.
    function cerrarModal() {
        modal.hidden = true;
        document.body.style.overflow = '';
    }

    if (modal) {
        modal.querySelectorAll('[data-cerrar-modal]').forEach((elemento) => {
            elemento.addEventListener('click', cerrarModal);
        });
    }

    document.addEventListener('keydown', (evento) => {
        if (evento.key === 'Escape' && modal && !modal.hidden) {
            cerrarModal();
        }
    });

    // Mostrar el total inicial al entrar a la página.
    filtrarEstudiantes();
});
