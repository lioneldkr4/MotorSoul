<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: iniciarsesionadmin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]);

    try {
        $conn = Conexion::conectar();

        // Primero eliminamos la imagen del servidor (opcional)
        $stmtImg = $conn->prepare("SELECT imagen FROM motocicletas WHERE id = :id");
        $stmtImg->execute([':id' => $id]);
        $moto = $stmtImg->fetch(PDO::FETCH_ASSOC);

        if ($moto && file_exists($moto['imagen'])) {
            unlink($moto['imagen']); // Elimina la imagen del disco
        }

        // Luego eliminamos el registro de la base
        $stmt = $conn->prepare("DELETE FROM motocicletas WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: CRUDMotos.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al eliminar: " . $e->getMessage();
    }
} else {
    header("Location: CRUDMotos.php");
    exit();
}
