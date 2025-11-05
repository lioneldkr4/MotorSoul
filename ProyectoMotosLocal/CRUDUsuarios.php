<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: iniciarsesionadmin.php");
    exit();
}
require_once 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRUD de Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilos.css">
</head>
<body>

  <div class="crud-container">
    <button class="btn-superior" onclick="window.location.href='registrousuario.php'">Registrar nuevo usuario</button>
    
    <table id="tabla-usuarios" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Edad</th>
          <th>Correo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        try {
            $conn = Conexion::conectar();
            $stmt = $conn->query("SELECT id, nombre, apellidos, edad, correo FROM usuarios");

            while ($usuario = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellidos'];
                echo "<tr>
                        <td>{$usuario['id']}</td>
                        <td>{$nombreCompleto}</td>
                        <td>{$usuario['edad']}</td>
                        <td>{$usuario['correo']}</td>
                        <td>
                          <form method='POST' action='eliminar_usuario.php' style='display:inline;' onsubmit=\"return confirm('¿Seguro que deseas eliminar este usuario?');\">
                            <input type='hidden' name='id' value='{$usuario['id']}'>
                            <button type='submit' class='btn btn-sm btn-danger'>Eliminar</button>
                          </form>
                          <form method='GET' action='editar_usuario.php' style='display:inline;'>
                            <input type='hidden' name='id' value='{$usuario['id']}'>
                            <button type='submit' class='btn btn-sm btn-warning'>Editar</button>
                          </form>
                        </td>
                      </tr>";
            }
        } catch (PDOException $e) {
            echo "<tr><td colspan='5'>Error al cargar usuarios: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
        }
        ?>
      </tbody>
    </table>

    <div class="botones">
      <button onclick="window.location.href='sesionadmin.php'" class="btn btn-secondary">Volver al Inicio</button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
