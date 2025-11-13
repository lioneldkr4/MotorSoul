<?php
require_once 'conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin'])) {
    header("Location: iniciarsesionadmin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $marca = $_POST["marca"];
    $modelo = $_POST["modelo"];
    $anio = intval($_POST["anio"]);
    $precio = floatval($_POST["precio"]);
    $hp = intval($_POST["hp"]);
    $transmision = $_POST["transmision"];
    $tipo = $_POST["tipo"];
    $color = $_POST["color"];
    $existencia = intval($_POST["existencia"]);

    $conn = Conexion::conectar();

    try {
        // Obtener imagen actual de la BD
        $stmt = $conn->prepare("SELECT imagen FROM motocicletas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $moto = $stmt->fetch(PDO::FETCH_ASSOC);
        $imagenActual = $moto['imagen'];

        // Manejar imagen nueva
        $rutaDestinoRelativa = $imagenActual;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            
            $nombreImagen = uniqid().".". pathinfo($_FILES['imagen']['name'],PATHINFO_EXTENSION);
            $rutaTemporal = $_FILES['imagen']['tmp_name'];
            $nuevaRutaRelativa = "imagenes/" . $nombreImagen;
            $nuevaRutaCompleta = __DIR__ . "/imagenes/" . $nombreImagen;

            if (move_uploaded_file($rutaTemporal, $nuevaRutaCompleta)) {
                // Eliminar imagen anterior si no es la misma
                if ($imagenActual && file_exists($imagenActual)) {
                    unlink($imagenActual);
                }
                $rutaDestinoRelativa = $nuevaRutaRelativa;
            }
        }

        // Actualizar datos
        $sql = "UPDATE motocicletas SET 
                    marca = :marca,
                    modelo = :modelo,
                    anio = :anio,
                    precio = :precio,
                    hp = :hp,
                    transmision = :transmision,
                    tipo = :tipo,
                    color = :color,
                    existencia = :existencia,
                    imagen = :imagen
                WHERE id = :id";

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
            ":imagen" => $rutaDestinoRelativa,
            ":id" => $id
        ]);

        header("Location: CRUDMotos.php");
        exit();
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error al actualizar: " . $e->getMessage() . "</p>";
    }
} else {
    header("Location: CRUDMotos.php");
    exit();
}
