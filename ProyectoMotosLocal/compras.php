<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['cliente'])) {
    header("Location: iniciarsesionusuario.php");
    exit();
}

$id_usuario = $_SESSION['cliente']['id'];
$conn = Conexion::conectar();

$stmt = $conn->prepare("SELECT m.marca, m.modelo, v.fecha_compra, v.estado, v.metodo_pago, v.fecha_entrega
                        FROM ventas v
                        JOIN motocicletas m ON v.id_moto = m.id
                        WHERE v.id_usuario = :id_usuario");
$stmt->execute([':id_usuario' => $id_usuario]);
$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h2>Mis Compras</h2>
<table border="1" class="table table-bordered table-striped">
    <tr>
        <th>Moto</th>
        <th>Fecha de compra</th>
        <th>Metodo de pago</th>
        <th>Estado</th>
        <th>Fecha de entrega</th>
    </tr>
    <?php foreach ($compras as $compra): ?>
    <tr>
        <td><?= $compra['marca'] . ' ' . $compra['modelo'] ?></td>
        <td><?= $compra['fecha_compra'] ?></td>
        <td><?= $compra['metodo_pago'] ?></td>
        <td><?= $compra['estado'] ?></td>
        <td><?= $compra['fecha_entrega'] ?: 'En proceso' ?></td>
    </tr>
    <?php endforeach; ?>
</table>


<div class="text-center mt-4">
    <a href="sesionusuario.php" class="btn btn-secondary">Volver al inicio</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>