<?php

// Comprobar que el usuario haya iniciado sesión.
require_once __DIR__ . '/proteger.php';

// Verificar si el rol requerido fue definido.
if (!isset($rol_requerido)) {
    http_response_code(500);
    exit('No se ha definido el rol requerido.');
}

// Comprobar si el usuario tiene el rol autorizado.
if ($_SESSION['rol'] !== $rol_requerido) {
    http_response_code(403);
    exit('No tienes permisos para acceder a esta página.');
}
