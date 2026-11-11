<?php
$host = "localhost";
$usuario = "docente_lamp";
$contrasena = "1885f5d24c6907a4c6e1759a1f0f2516";
$base_datos = "practica_lamp";

$conexion = new PDO(
    "mysql:host=$host;dbname=$base_datos;charset=utf8mb4",
    $usuario,
    $contrasena,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);
