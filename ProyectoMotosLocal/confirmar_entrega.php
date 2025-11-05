<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: iniciarsesionadmin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $venta_id = intval($_POST['venta_id']);
    $fecha_entrega = trim($_POST['fecha_entrega']);

    if (empty($fecha_entrega)) {
        echo "Error: Debes ingresar una fecha de entrega.";
        exit();
    }

    try {
        $conn = Conexion::conectar();

        // Obtener el id de la moto asociada a la venta
        $stmtMoto = $conn->prepare("SELECT id_moto FROM ventas WHERE id = :venta_id");
        $stmtMoto->execute([':venta_id' => $venta_id]);
        $resultado = $stmtMoto->fetch(PDO::FETCH_ASSOC);

        if ($resultado && isset($resultado['id_moto'])) {
            $id_moto = $resultado['id_moto'];

            // Disminuir existencia en -1 si hay motos disponibles
            $updateMoto = $conn->prepare("UPDATE motocicletas SET existencia = existencia - 1 WHERE id = :id_moto AND existencia > 0");
            $updateMoto->execute([':id_moto' => $id_moto]);

            // Confirmar venta y asignar fecha de entrega
            $stmt = $conn->prepare("UPDATE ventas SET estado = 'Confirmada', fecha_entrega = :fecha WHERE id = :id");
            $stmt->execute([
                ':fecha' => $fecha_entrega,
                ':id' => $venta_id
            ]);

            header("Location: ventas.php");
            exit();
        } else {
            echo "No se encontró la motocicleta asociada a la venta.";
        }
    } catch (PDOException $e) {
        echo "Error al confirmar entrega: " . $e->getMessage();
    }
}
?>
