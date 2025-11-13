<?php
// editar_perfil.php
require_once 'conexion.php';
session_start();

// Si no hay usuario logueado, redirigir al login
if (!isset($_SESSION['cliente']) || !isset($_SESSION['cliente']['id'])) {
    header("Location: iniciarsesionusuario.php");
    exit();
}

try {
    $conn = Conexion::conectar();
    $id = (int) $_SESSION['cliente']['id'];

    $stmt = $conn->prepare("SELECT id, nombre, apellidos, edad, correo FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        // Si no existe el usuario en DB, cerrar sesión por seguridad
        session_unset();
        session_destroy();
        header("Location: iniciarsesionusuario.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error al cargar el perfil: " . htmlspecialchars($e->getMessage()));
}

// CSRF token simple
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar mi Perfil</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
    <style>
      /* Estilos mínimos para que se vea correcto si no usas tu CSS */
      .form-card{ max-width:720px; margin:36px auto; padding:20px; border-radius:12px; background: rgba(45, 213, 146, 0.96); border:1px solid rgba(255,255,255,.06);}
      label{ font-weight:600; }
    </style>
</head>
<body>
  <div class="container">
    <div class="form-card">
      <h2 class="mb-3">Editar mi perfil</h2>
      <p class="text-muted">Actualiza tus datos. Dejar el campo de contraseña vacío si no deseas cambiarla.</p>

      <form action="actualizar_perfil.php" method="post" novalidate>
        <input type="hidden" name="id" value="<?= (int)$usuario['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre</label>
          <input id="nombre" name="nombre" class="form-control" required maxlength="100"
                 value="<?= htmlspecialchars($usuario['nombre']) ?>">
        </div>

        <div class="mb-3">
          <label for="apellidos" class="form-label">Apellidos</label>
          <input id="apellidos" name="apellidos" class="form-control" required maxlength="150"
                 value="<?= htmlspecialchars($usuario['apellidos']) ?>">
        </div>

        <div class="mb-3">
          <label for="edad" class="form-label">Edad</label>
          <input id="edad" name="edad" class="form-control" type="number" min="0" max="120"
                 value="<?= htmlspecialchars($usuario['edad']) ?>" required>
        </div>

        <div class="mb-3">
          <label for="correo" class="form-label">Correo</label>
          <input id="correo" name="correo" class="form-control" type="email" maxlength="200"
                 value="<?= htmlspecialchars($usuario['correo']) ?>" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Nueva contraseña (opcional)</label>
          <input id="password" name="password" class="form-control" type="password" minlength="6" placeholder="Dejar vacío para mantener la actual">
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
          <a href="perfil.php" class="btn btn-outline-secondary">Cancelar</a>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
