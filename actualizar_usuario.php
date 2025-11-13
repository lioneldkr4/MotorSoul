<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: iniciarsesionadmin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $edad = intval($_POST['edad']);
    $correo = trim($_POST['correo']);

    try {
        $conn = Conexion::conectar();
        $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, edad = :edad, correo = :correo WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellidos' => $apellidos,
            ':edad' => $edad,
            ':correo' => $correo,
            ':id' => $id
        ]);

        header("Location: CRUDUsuarios.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al actualizar el usuario: " . htmlspecialchars($e->getMessage());
    }
}
?>
