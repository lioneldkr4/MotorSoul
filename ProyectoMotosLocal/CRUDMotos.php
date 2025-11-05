<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: iniciarsesionadmin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Motocicletas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <button class="btn-superior" onclick="window.location.href='registromoto.php'">Registrar motocicleta</button>
    <table id="tabla-motos" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Marca</th>
          <th>Modelo</th>
          <th>Año</th>
          <th>Precio</th>
          <th>HP</th>
          <th>Transmisión</th>
          <th>Tipo</th>
          <th>Color</th>
          <th>Existencia</th>
          <th>Imagen</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
<?php
require_once 'conexion.php';
if (!isset($_SESSION['admin'])) {
    header("Location: iniciarsesionadmin.php");
    exit();
}

try {
    $conn = Conexion::conectar();
    $stmt = $conn->query("SELECT * FROM motocicletas");

    while ($moto = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$moto['marca']}</td>
                <td>{$moto['modelo']}</td>
                <td>{$moto['anio']}</td>
                <td>\${$moto['precio']}</td>
                <td>{$moto['hp']} HP</td>
                <td>{$moto['transmision']}</td>
                <td>{$moto['tipo']}</td>
                <td>{$moto['color']}</td>
                <td>{$moto['existencia']}</td>
                <td><img src='{$moto['imagen']}' alt='Moto' style='width: 100px;'></td>
<td>
            <form method='POST' action='eliminar_moto.php' onsubmit=\"return confirm('¿Seguro que deseas eliminar esta moto?');\">
                <input type='hidden' name='id' value='{$moto['id']}'>
                <button type='submit' class='btn btn-sm btn-danger'>Eliminar</button>
            </form>
            <form method='GET' action='editar_moto.php'>
                <input type='hidden' name='id' value='{$moto['id']}'>
                <button type='submit' class='btn btn-sm btn-warning'>Editar</button>
            </form>
        </td>
              </tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='11'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>      

      </tbody>
    </table>
    <div id="mdlMensaje" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Inicio de sesion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
            </div>
          </div>
        </div>
    </div>
      <div class="botones">
        <button type="button" onclick="window.location.href='sesionadmin.php'">Volver al Inicio</button>
      </div>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>