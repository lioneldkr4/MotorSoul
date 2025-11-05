<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['cliente'])) {
    header("Location: iniciarsesionusuario.php");
    exit();
}

$id_usuario = $_SESSION['cliente']['id'];
$id_moto = $_POST['id_moto'] ?? null;
$metodo_pago = $_POST['metodo_pago'] ?? null;

if ($id_moto && $metodo_pago) {
    try {
        $conn = Conexion::conectar();

        $stmt = $conn->prepare("INSERT INTO ventas (id_usuario, id_moto, metodo_pago, estado)
                                VALUES (:id_usuario, :id_moto, :metodo_pago, 'Pendiente')");
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':id_moto' => $id_moto,
            ':metodo_pago' => $metodo_pago
        ]);

        header("Location: compras.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al registrar la compra: " . $e->getMessage();
    }
} else {
    echo "Error: Datos de compra incompletos.";
}
?>
