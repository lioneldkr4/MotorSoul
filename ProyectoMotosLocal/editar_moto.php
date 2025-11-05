<?php
require_once 'conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin'])) {
    header("Location: iniciarsesionadmin.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: CRUDMotos.php");
    exit();
}

$conn = Conexion::conectar();

$stmt = $conn->prepare("SELECT * FROM motocicletas WHERE id = :id");
$stmt->execute([':id' => $id]);
$moto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$moto) {
    echo "Motocicleta no encontrada.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Motocicleta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="registro-moto">
    <h1>Editar motocicleta</h1>
    <form action="actualizar_moto.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $moto['id'] ?>">

        <label>Marca</label>
        <input type="text" name="marca" value="<?= $moto['marca'] ?>" required>

        <label>Modelo</label>
        <input type="text" name="modelo" value="<?= $moto['modelo'] ?>" required>

        <label>Año</label>
        <input type="number" name="anio" value="<?= $moto['anio'] ?>" required>

        <label>Precio</label>
        <input type="number" name="precio" value="<?= $moto['precio'] ?>" required>

        <label>HP</label>
        <input type="number" name="hp" value="<?= $moto['hp'] ?>" required>

        <label>Transmisión</label>
        <select name="transmision" required>
            <?php
                $opciones = ["Manual", "Semiautomática", "Automática"];
                foreach ($opciones as $opcion) {
                    $selected = $moto['transmision'] === $opcion ? "selected" : "";
                    echo "<option value=\"$opcion\" $selected>$opcion</option>";
                }
            ?>
        </select>

        <label>Tipo</label>
        <select name="tipo" required>
            <?php
                $tipos = ["Deportiva", "Trabajo", "Doble Propósito", "Motoneta", "Cuatrimoto"];
                foreach ($tipos as $tipo) {
                    $selected = $moto['tipo'] === $tipo ? "selected" : "";
                    echo "<option value=\"$tipo\" $selected>$tipo</option>";
                }
            ?>
        </select>

        <label>Color</label>
        <input type="text" name="color" value="<?= $moto['color'] ?>" required>

        <label>Existencia</label>
        <input type="number" name="existencia" value="<?= $moto['existencia'] ?>" required>

        <label>Imagen actual</label><br>
        <img src="<?= $moto['imagen'] ?>" style="width: 100px;"><br>
        <label>Nueva imagen (opcional)</label>
        <input type="file" name="imagen" accept="image/*">

        <div class="botones">
            <button type="submit">Guardar cambios</button>
            <a href="CRUDMotos.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>
