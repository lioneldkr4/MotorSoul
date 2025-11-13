<?php
require_once 'conexion.php';
session_start();

// Verificamos si es admin
if (!isset($_SESSION['admin'])) {
    header("Location: iniciarsesionadmin.php");
    exit();
}

// Verificamos si se recibió el ID por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    try {
        $conn = Conexion::conectar();
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Redirigir de vuelta al CRUD
        header("Location: CRUDUsuarios.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al eliminar el usuario: " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "No se recibió el ID del usuario.";
}
