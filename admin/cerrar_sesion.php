<?php

// Iniciar la sesión si todavía está activa.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Eliminar todas las variables de sesión.
$_SESSION = [];

// Eliminar la cookie de sesión del navegador.
if (ini_get('session.use_cookies')) {

    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $parametros['path'],
        $parametros['domain'],
        $parametros['secure'],
        $parametros['httponly']
    );
}

// Destruir la sesión actual.
session_destroy();

// Redirigir al formulario de inicio de sesión.
header('Location: ../login.php');
exit;

?>