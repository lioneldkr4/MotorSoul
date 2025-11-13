<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['cliente'])) {
    header("Location: iniciarsesionusuario.php");
    exit();
}

// Obtener el ID del usuario desde la sesión
$idUsuario = $_SESSION['cliente']['id'];

try {
    $conn = Conexion::conectar();
    $stmt = $conn->prepare("SELECT nombre, apellidos, correo, edad FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $idUsuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $nombre = $usuario['nombre'];
        $apellidos = $usuario['apellidos'];
        $correo = $usuario['correo'];
        $edad = $usuario['edad'];
    } else {
        // Si no encuentra el usuario (raro), redirige o muestra error
        header("Location: cerrarsesion.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error al obtener datos del perfil: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <form class="perfil" action="">
        <h2>Perfil de Usuario</h2>
        <b></b>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></p>
        <p><strong>Apellido:</strong> <?= htmlspecialchars($apellidos) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($correo) ?></p>
        <p><strong>Edad:</strong> <?= htmlspecialchars($edad) ?></p>
        <b></b>
        
        <button type="button" onclick="window.location.href='editar_perfil.php'">Editar perfil</button>
        <button type="button" onclick="window.location.href='sesionusuario.php'">Volver al Inicio</button>
    </form>
    
</body>
</html>