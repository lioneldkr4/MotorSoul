<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: iniciarsesionadmin.php");
    exit();
}

try {
    $conn = Conexion::conectar();

    $sql = "SELECT v.id, v.estado, v.metodo_pago, v.fecha_entrega,
                   u.nombre AS nombre_usuario, u.correo,
                   m.marca, m.modelo, m.precio
            FROM ventas v
            JOIN usuarios u ON v.id_usuario = u.id
            JOIN motocicletas m ON v.id_moto = m.id
            ORDER BY v.id DESC";

    $stmt = $conn->query($sql);
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener ventas: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Ventas realizadas</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Moto</th>
                <th>Precio</th>
                <th>Método de pago</th>
                <th>Estado</th>
                <th>Fecha entrega</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ventas as $venta): ?>
            <tr>
                <td><?= htmlspecialchars($venta['nombre_usuario']) ?></td>
                <td><?= htmlspecialchars($venta['correo']) ?></td>
                <td><?= htmlspecialchars($venta['marca'] . ' ' . $venta['modelo']) ?></td>
                <td>$<?= number_format($venta['precio'], 2) ?></td>
                <td><?= htmlspecialchars($venta['metodo_pago']) ?></td>
                <td><?= htmlspecialchars($venta['estado']) ?></td>
                <td><?= $venta['fecha_entrega'] ? htmlspecialchars($venta['fecha_entrega']) : 'No asignada' ?></td>
                <td>
                    <?php if ($venta['estado'] === 'Pendiente'): ?>
                        <form action="confirmar_entrega.php" method="post" style="display:inline-block;">
                            <input type="hidden" name="venta_id" value="<?= $venta['id'] ?>">
                            <input type="date" name="fecha_entrega" required>
                            <button type="submit" class="btn btn-sm btn-primary">Confirmar</button>
                        </form>
                    <?php else: ?>
                        <span class="text-success">Confirmada</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-center mt-4">
    <a href="sesionadmin.php" class="btn btn-secondary">Volver al inicio</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
