<?php
require_once 'conexion.php';
session_start();

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $edad = intval($_POST['edad']);
    $correo = trim($_POST['correo']);
    $contrasenia = $_POST['contrasenia'];
    $confirmar = $_POST['confirmar'];

if (strlen($nombre) < 3 || strlen($apellidos) < 3) {
        $mensaje = "El nombre y apellidos deben tener al menos 3 caracteres.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El correo electrónico no es válido.";
    } elseif ($edad < 18 || $edad > 100 || !filter_var($edad, FILTER_VALIDATE_INT)) {
        $mensaje = "La edad debe estar entre 18 y 100 años y solo numeros enteros.";
    } elseif (strlen($contrasenia) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
    } elseif ($contrasenia !== $confirmar) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        try {
            $conn = Conexion::conectar();

            // Verificar si ya existe el correo
            $check = $conn->prepare("SELECT id FROM usuarios WHERE correo = :correo");
            $check->execute([':correo' => $correo]);
            if ($check->fetch()) {
                $mensaje = "Ya existe un usuario registrado con ese correo.";
            } else {
                $sql = "INSERT INTO usuarios (nombre, apellidos, edad, correo, contrasenia)
                        VALUES (:nombre, :apellidos, :edad, :correo, crypt(:contrasenia, gen_salt('bf')))";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':apellidos' => $apellidos,
                    ':edad' => $edad,
                    ':correo' => $correo,
                    ':contrasenia' => $contrasenia
                ]);
                header("Location: iniciarsesionusuario.php");
                exit();
            }
        } catch (PDOException $e) {
            $mensaje = "Error al registrar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Registro de usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>

<div class="formulario-registro">
    <h1>Registro de usuario</h1>

    <form method="post" action="registrousuario.php">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required placeholder="Nombre...">

        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" required placeholder="Apellidos...">

        <label for="edad">Edad</label>
        <input type="number" id="edad" name="edad" min="18" max="100" step="1" required placeholder="Edad...">

        <label for="correo">Correo Electrónico</label>
        <input type="email" id="correo" name="correo" required placeholder="Correo...">

        <label for="contrasenia">Contraseña</label>
        <input type="password" id="contrasenia" name="contrasenia" required placeholder="Contraseña...">

        <label for="confirmar">Confirmar Contraseña</label>
        <input type="password" id="confirmar" name="confirmar" required placeholder="Confirmar contraseña...">

        <p>
            <input type="checkbox" name="terminos_condiciones" required>
            Acepto los <a href="#">términos y condiciones</a>
        </p>

        <div class="botones">
            <button type="submit">Registrarse</button>
            <button type="button" onclick="window.location.href='index.php'">Volver al Inicio</button>
        </div>

        <a href="iniciarsesionusuario.php">¿Ya tienes una cuenta? Inicia sesión aquí...</a>
    </form>

    <?php if ($mensaje): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
</div>

<script>
document.querySelector("form").addEventListener("submit", function(e) {
    const nombre = document.getElementById("nombre").value.trim();
    const apellidos = document.getElementById("apellidos").value.trim();
    const edad = parseInt(document.getElementById("edad").value);
    const correo = document.getElementById("correo").value.trim();
    const contrasenia = document.getElementById("contrasenia").value;
    const confirmar = document.getElementById("confirmar").value;
    const terminos = document.querySelector("[name='terminos_condiciones']").checked;

    if (nombre.length < 3 || apellidos.length < 3) {
        alert("El nombre y apellidos deben tener al menos 3 caracteres.");
        e.preventDefault();
    } else if (edad < 18 || edad > 100 || !Number.isInteger(Number(edad))) {
        alert("Debes tener entre 18 y 100 años y solo se aceptan números enteros.");
        e.preventDefault();
    } else if (!correo.includes("@") || !correo.includes(".")) {
        alert("El correo no es válido.");
        e.preventDefault();
    } else if (contrasenia.length < 6) {
        alert("La contraseña debe tener al menos 6 caracteres.");
        e.preventDefault();
    } else if (contrasenia !== confirmar) {
        alert("Las contraseñas no coinciden.");
        e.preventDefault();
    } else if (!terminos) {
        alert("Debes aceptar los términos y condiciones.");
        e.preventDefault();
    }
});
</script>


</body>
</html>
