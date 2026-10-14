<?php

$host = '127.0.0.1';
$port = '3306';
$dbname = 'redvital';
$username = 'redvital_user';
$password = 'RedVital@Proyecto2026';

try {

    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    die("Error de conexión: " . $e->getMessage());

}