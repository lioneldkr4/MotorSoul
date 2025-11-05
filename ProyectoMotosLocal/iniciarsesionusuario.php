<?php
require_once 'conexion.php';
session_start();

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $contrasenia = $_POST['contrasenia'];

    try {
        $conn = Conexion::conectar();

        // Verificamos directamente con PostgreSQL si el correo y contraseña coinciden
        $sql = "SELECT id, nombre, correo FROM usuarios WHERE correo = :correo AND contrasenia = crypt(:contrasenia, contrasenia)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':correo' => $correo,
            ':contrasenia' => $contrasenia
        ]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Sesión válida
            $_SESSION['cliente'] = [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'correo' => $usuario['correo']
            ];
            header("Location: sesionusuario.php");
            exit();
        } else {
            $mensaje = "Correo o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        $mensaje = "Error al iniciar sesión: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Inicio de sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>

<div class="formulario-inicio">
    <h1>Inicio de sesión</h1>

    <form method="post" action="iniciarsesionusuario.php">
        <label for="correo">Correo Electrónico</label>
        <input 
            type="text" id="correo" name="correo" placeholder="Correo Electrónico..." required minlength="5"
            pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$">

        <label for="contrasenia">Contraseña</label>
        <input type="password" id="contrasenia" name="contrasenia" placeholder="Contraseña..." required minlength="6">

        <div class="botones">
            <button type="submit">Iniciar Sesión</button>
            <button type="button" onclick="window.location.href='index.php'">Volver al Inicio</button>
        </div>

        <a href="registrousuario.php">¿No tienes una cuenta? Regístrate aquí...</a>
    </form>

    <?php if ($mensaje): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
</div>

<script>
document.querySelector("form").addEventListener("submit", function(e) {
    const correo = document.getElementById("correo").value.trim();
    const contrasenia = document.getElementById("contrasenia").value;

    if (!correo.includes("@") || !correo.includes(".")) {
        alert("El correo no es válido.");
        e.preventDefault();
    } else if (contrasenia.length < 6) {
        alert("La contraseña debe tener al menos 6 caracteres.");
        e.preventDefault();
    }
});
</script>

</body>
</html>
