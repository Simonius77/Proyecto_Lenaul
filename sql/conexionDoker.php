<?php

$host = "127.0.0.1";
$puerto = 3307;
$usuario = "root";
$password = "mi_password";
$base_datos = "lenault";

try {
    $conexion = new PDO(
        "mysql:host=$host;port=$puerto;dbname=$base_datos;charset=utf8mb4",
        $usuario,
        $password
    );

    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexión correcta a la base de datos lenault";

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}