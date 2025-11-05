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
    $stmt = $conn->prepare("SELECT nombre, correo, edad FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $idUsuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $nombre = $usuario['nombre'];
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
        <h2>PERFIL</h2>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($correo) ?></p>
        <p><strong>Edad:</strong> <?= htmlspecialchars($edad) ?></p>
        
        <button type="button" onclick="window.location.href='sesionusuario.php'">Volver al Inicio</button>
        <button type="button" onclick="window.location.href='sesionusuario.php'">Editar perfil</button>
    </form>
    
</body>
</html>