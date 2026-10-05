// Menú de navegación
const botonMenu = document.querySelector('#botonMenu');
const menuNavegacion = document.querySelector('#menuNavegacion');


//Abre o cierra el menú de navegación.
function alternarMenu() {
    const menuAbierto = menuNavegacion.classList.toggle('menu-abierto');
    botonMenu.setAttribute('aria-expanded', menuAbierto);
}

if (botonMenu && menuNavegacion) {
    botonMenu.addEventListener('click', alternarMenu);
}