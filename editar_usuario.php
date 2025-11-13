<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: iniciarsesionadmin.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: CRUDUsuarios.php");
    exit();
}

try {
    $conn = Conexion::conectar();
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $_GET['id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        header("Location: CRUDUsuarios.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>
    <div class="formulario-registro">
    <h2>Editar Usuario</h2>
    <form action="actualizar_usuario.php" method="POST">
        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required placeholder="Nombre..."><br>

        <label>Apellidos:</label>
        <input type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required placeholder="Apellidos..."><br>

        <label>Edad:</label>
        <input type="number" name="edad" value="<?= $usuario['edad'] ?>" required placeholder="Edad..."><br>

        <label>Correo:</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required placeholder="Correo..."><br>

        <button type="submit">Guardar Cambios</button>
        <a href="CRUDUsuarios.php">Cancelar</a>
    </form>
    </div>

    
</body>
</html>
