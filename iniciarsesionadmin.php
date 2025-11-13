<?php
require_once 'conexion.php';
session_start();

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"]);
    $contrasenia = $_POST["contrasenia"];

    try {
        $conn = Conexion::conectar();

        $stmt = $conn->prepare("SELECT * FROM administradores WHERE usuario = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($contrasenia, $admin["contrasenia"])) {
                $_SESSION["admin"] = $admin["usuario"];
                header("Location: sesionadmin.php");
                exit();
            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        } else {
            $mensaje = "Usuario no encontrado.";
        }
    } catch (PDOException $e) {
        $mensaje = "Error de conexión: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Inicio de sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="formulario-inicio">
    <h1>Inicio Administrador</h1>
    <form method="POST" action="iniciarsesionadmin.php">
        <label for="usuario">Usuario</label>
        <input type="text" name="usuario" id="usuario" placeholder="Usuario..." required>
        <label for="contrasenia">Contraseña</label>
        <input type="password" name="contrasenia" id="contrasenia" placeholder="Contraseña..." required>

        <div class="botones">
            <button type="submit">Iniciar Sesión</button>
            <button type="button" onclick="window.location.href='index.php'">Volver al Inicio</button>
        </div>
    </form>

    <?php if ($mensaje): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
