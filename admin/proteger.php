<?php

// Iniciar la sesión si todavía no está iniciada.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión.
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit;
}
