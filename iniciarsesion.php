<?php
session_start();

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST["correo"] ?? "");
    $contrasenia = $_POST["contrasenia"] ?? "";

    if ($correo === "" || $contrasenia === "") {
        $error_message = "Por favor, completa todos los campos.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error_message = "El correo electrónico no tiene un formato válido.";
    } elseif (strlen($contrasenia) < 6) {
        $error_message = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        if ($correo === "admin@correo.com" && $contrasenia === "123456") {
            $_SESSION["correo"] = $correo;
            header("Location: index.html");
            exit;
        } else {
            $error_message = "Correo o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inicio de sesión</title>
  <link rel="stylesheet" href="estilos.css" />
  <style>
    .error-message {
      color: red;
      margin-top: 10px;
      font-size: 14px;
    }
  </style>
</head>
<body>

  <div class="formulario-inicio">
    <h1>Inicio de sesión</h1>
    <form id="formLogin" action="" method="post" autocomplete="off">
      
      <label for="correo">Correo Electrónico</label>
      <input 
        type="text" id="correo" name="correo" placeholder="Correo Electrónico..." required minlength="5"
        pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$"
        value="<?= htmlspecialchars($correo ?? "") ?>">

      <label for="contrasenia">Contraseña</label>
      <input type="password" id="login-contrasenia" name="contrasenia" placeholder="Contraseña..." required minlength="6">

      <div class="botones">
        <button type="submit">Iniciar Sesión</button>
        <button type="button" onclick="window.location.href='index.html'">Volver al Inicio</button>
      </div>

      <?php if (!empty($error_message)): ?>
        <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
      <?php endif; ?>

      <a href="registro.html">¿No tienes una cuenta? Regístrate aquí...</a>
    </form>
  </div>

  <script src="validaciones.js"></script>
</body>
</html>
