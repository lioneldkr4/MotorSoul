<?php
require_once 'conexion.php';

try {
    $conn = Conexion::conectar();

    $nombre = 'Juan';
    $usuario = 'admin1';
    $contrasenia_plana = '123456';
    $contrasenia_hash = password_hash($contrasenia_plana, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO administradores (nombre, usuario, contrasenia) VALUES (:nombre, :usuario, :contrasenia)");
    $stmt->execute([
        ':nombre' => $nombre,
        ':usuario' => $usuario,
        ':contrasenia' => $contrasenia_hash
    ]);

    echo "Administrador creado correctamente.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
