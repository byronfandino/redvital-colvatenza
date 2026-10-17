
document.addEventListener('DOMContentLoaded', () => {
    const botonAbrir = document.getElementById('boton-menu-movil');
    const botonCerrar = document.getElementById('boton-cerrar-menu');
    const menu = document.getElementById('menu-administrativo');
    const fondo = document.getElementById('fondo-menu-movil');

    if (!botonAbrir || !botonCerrar || !menu || !fondo) {
        return;
    }

    // Evitar registrar los eventos más de una vez.
    if (menu.dataset.eventosRegistrados === 'true') {
        return;
    }

    menu.dataset.eventosRegistrados = 'true';

    // Abrir el menú lateral.
    const abrirMenu = () => {
        menu.classList.add('menu-abierto');
        fondo.hidden = false;

        botonAbrir.classList.add('menu-boton-oculto');
        botonAbrir.setAttribute('aria-expanded', 'true');
        botonAbrir.setAttribute('aria-label', 'Cerrar menú de navegación');

        document.body.style.overflow = 'hidden';
        botonCerrar.focus();
    };

    // Cerrar el menú lateral.
    const cerrarMenu = () => {
        menu.classList.remove('menu-abierto');
        fondo.hidden = true;

        botonAbrir.classList.remove('menu-boton-oculto');
        botonAbrir.setAttribute('aria-expanded', 'false');
        botonAbrir.setAttribute('aria-label', 'Abrir menú de navegación');

        document.body.style.overflow = '';
        botonAbrir.focus();
    };

    // Abrir o cerrar desde el botón hamburguesa.
    botonAbrir.addEventListener('click', () => {
        if (menu.classList.contains('menu-abierto')) {
            cerrarMenu();
        } else {
            abrirMenu();
        }
    });

    // Cerrar desde el botón interior.
    botonCerrar.addEventListener('click', cerrarMenu);

    // Cerrar al pulsar el fondo oscuro.
    fondo.addEventListener('click', cerrarMenu);

    // Cerrar al seleccionar una opción del menú.
    menu.querySelectorAll('.enlace-menu, .enlace-cerrar-sesion')
        .forEach((enlace) => {
            enlace.addEventListener('click', () => {
                menu.classList.remove('menu-abierto');
                fondo.hidden = true;

                botonAbrir.classList.remove('menu-boton-oculto');
                botonAbrir.setAttribute('aria-expanded', 'false');

                document.body.style.overflow = '';
            });
        });

    // Cerrar al pulsar Escape.
    document.addEventListener('keydown', (evento) => {
        if (
            evento.key === 'Escape' &&
            menu.classList.contains('menu-abierto')
        ) {
            cerrarMenu();
        }
    });

    // Restablecer el menú al ampliar la pantalla.
    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            menu.classList.remove('menu-abierto');
            fondo.hidden = true;

            botonAbrir.classList.remove('menu-boton-oculto');
            botonAbrir.setAttribute('aria-expanded', 'false');
            botonAbrir.setAttribute('aria-label', 'Abrir menú de navegación');

            document.body.style.overflow = '';
        }
    });
});
