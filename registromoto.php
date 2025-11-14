<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: iniciarsesionadmin.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $marca = $_POST["marca"];
    $modelo = $_POST["modelo"];
    $anio = intval($_POST["anio"]);
    $precio = floatval($_POST["precio"]);
    $hp = intval($_POST["caballos"]);
    $transmision = $_POST["transmision"];
    $tipo = $_POST["tipo"];
    $color = $_POST["color"];
    $existencia = intval($_POST["existencia"]);
    $descripcion = $_POST["descripcion"];
    $imagen = $_FILES['imagen'];

    // Validaciones del lado del servidor
    if (strlen($marca) < 2 || strlen($modelo) < 2 || strlen($color) < 3) {
        $mensaje = "Marca, modelo y color deben tener al menos 2-3 caracteres.";
    } elseif ($anio < 2010 || $anio > 2026 || !filter_var($anio, FILTER_VALIDATE_INT)) {
        $mensaje = "El año debe estar entre 2010 y 2026.";
    } elseif ($precio < 5000 || $precio > 1000000) {
        $mensaje = "El precio debe estar entre $5,000 y $1,000,000.";
    } elseif ($hp < 5 || $hp > 500) {
        $mensaje = "Los caballos de fuerza deben estar entre 5 y 500.";
    } elseif (empty($transmision) || empty($tipo)) {
        $mensaje = "Transmisión y tipo son obligatorios.";
    } elseif ($existencia < 0 || $existencia > 20) {
        $mensaje = "La existencia debe estar entre 0 y 20.";
    } elseif (!isset($_FILES['imagen']) || $imagen['error'] !== 0) {
        $mensaje = "Error con la imagen. Asegúrate de subir un archivo válido.";
    } else {
        
        // Procesamiento de la imagen
        $nombreImagen = uniqid() . "." . pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $rutaTemporal = $_FILES['imagen']['tmp_name'];
        $nuevaRutaRelativa = "imagenes/" . $nombreImagen;
        $rutaDestinoCompleta = __DIR__ . "/imagenes/" . $nombreImagen;

        if (move_uploaded_file($rutaTemporal, $rutaDestinoCompleta)) {
            try {
                $conn = Conexion::conectar();
                $sql = "INSERT INTO motocicletas (marca, modelo, anio, precio, hp, transmision, tipo, color, existencia, descripcion, imagen)
                        VALUES (:marca, :modelo, :anio, :precio, :hp, :transmision, :tipo, :color, :existencia, :descripcion, :imagen)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ":marca" => $marca,
                    ":modelo" => $modelo,
                    ":anio" => $anio,
                    ":precio" => $precio,
                    ":hp" => $hp,
                    ":transmision" => $transmision,
                    ":tipo" => $tipo,
                    ":color" => $color,
                    ":existencia" => $existencia,
                    ":descripcion" => $descripcion,
                    ":imagen" => $nuevaRutaRelativa
                ]);

                header("Location: CRUDMotos.php");
                exit();
            } catch (PDOException $e) {
                $mensaje = "Error al registrar: " . $e->getMessage();
            }
        } else {
            $mensaje = "Error al subir la imagen.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Motocicletas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="registro-moto">
    <h1>Registro de motocicleta</h1>
    <form id="formRegistroMoto" action="registromoto.php" method="post" enctype="multipart/form-data">
        <label for="marca">Marca</label>
        <input type="text" name="marca" id="marca" placeholder="Marca..." required>

        <label for="modelo">Modelo</label>
        <input type="text" name="modelo" id="modelo" placeholder="Modelo..." required>

        <label for="anio">Año</label>
        <input type="number" name="anio" id="anio" placeholder="Año..." min="2010" max="2026" step="1" required>

        <label for="precio">Precio</label>
        <input type="number" name="precio" id="precio" placeholder="Precio..." min="5000" max="1000000" required>

        <label for="caballos">HP</label>
        <input type="number" name="caballos" id="caballos" placeholder="Caballos de fuerza..." min="5" max="500" required>

        <label for="transmision">Transmisión</label>
        <select id="transmision" name="transmision" required>
            <option value="">Selecciona una opción</option>
            <option value="Manual">Manual</option>
            <option value="Semiautomática">Semiautomática</option>
            <option value="Automática">Automática</option>
        </select>

        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            <option value="">Selecciona un tipo</option>
            <option value="Deportiva">Deportiva</option>
            <option value="Trabajo">Trabajo</option>
            <option value="Doble Propósito">Doble Propósito</option>
            <option value="Motoneta">Motoneta</option>
            <option value="Cuatrimoto">Cuatrimoto</option>
        </select>

        <label for="color">Color</label>
        <input type="text" name="color" id="color" placeholder="Color..." required>

        <label for="existencia">Existencia</label>
        <input type="number" name="existencia" id="existencia" placeholder="Existencia..." min="0" max="20" required>

        <label for="descripcion" class="form-label">Descripción</label>
        <textarea id="descripcion" name="descripcion"
            class="form-control" rows="3" placeholder="Descripción..."></textarea>

        <label for="imagen">Imagen</label>
        <input class="campos" type="file" name="imagen" id="imagen" accept="image/*" required>
        <img id="preview" style="max-width: 200px; display: block; margin-top: 10px;">

        <div class="botones">
            <button id="btnRegistrar" type="submit">Registrar</button>
            <button type="button" onclick="window.location.href='sesionadmin.php'">Volver al Inicio</button>
        </div>
    </form>

    <?php if ($mensaje): ?>
        <p style="color:red;"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('imagen').addEventListener('change', function(e) {
    const preview = document.getElementById('preview');
    const file = e.target.files[0];
    if (file) {
        preview.src = URL.createObjectURL(file);
    }
});
</script>

<script>
document.getElementById("formRegistroMoto").addEventListener("submit", function(e) {
    const marca = document.getElementById("marca").value.trim();
    const modelo = document.getElementById("modelo").value.trim();
    const anio = parseInt(document.getElementById("anio").value);
    const precio = parseFloat(document.getElementById("precio").value);
    const hp = parseInt(document.getElementById("caballos").value);
    const transmision = document.getElementById("transmision").value;
    const tipo = document.getElementById("tipo").value;
    const color = document.getElementById("color").value.trim();
    const existencia = parseInt(document.getElementById("existencia").value);
    const imagen = document.getElementById("imagen").files[0];

    if (marca.length < 2 || modelo.length < 2 || color.length < 3) {
        alert("Marca, modelo y color deben tener al menos 2-3 caracteres.");
        e.preventDefault();
    } else if (anio < 2010 || anio > 2026 || !Number.isInteger(anioNum)) {
        alert("El año debe estar entre 2010 y 2026.");
        e.preventDefault();
    } else if (precio < 5000 || precio > 1000000) {
        alert("El precio debe estar entre $5,000 y $1,000,000.");
        e.preventDefault();
    } else if (hp < 5 || hp > 500) {
        alert("Los caballos de fuerza deben estar entre 5 y 500.");
        e.preventDefault();
    } else if (!transmision || !tipo) {
        alert("Debes seleccionar una transmisión y un tipo.");
        e.preventDefault();
    } else if (existencia < 0 || existencia > 20) {
        alert("La existencia debe ser entre 0 y 20.");
        e.preventDefault();
    } else if (!imagen) {
        alert("Debes seleccionar una imagen.");
        e.preventDefault();
    }
});
</script>
</body>
</html>