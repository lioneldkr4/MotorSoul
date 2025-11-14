<?php
// iniciarsesion.php
require_once 'conexion.php';
session_start();

$mensaje = "";
$rolSeleccionado = $_POST['rol'] ?? 'cliente';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $contrasenia = $_POST["contrasenia"] ?? '';

    try {
        $conn = Conexion::conectar();

        if ($rolSeleccionado === 'admin') {
            // ---- LOGIN ADMIN (usa usuario + contrasenia con password_verify) ----
            $usuario = trim($_POST["usuario"] ?? '');

            if ($usuario === '' || $contrasenia === '') {
                $mensaje = "Debes ingresar usuario y contraseña.";
            } else {
                $stmt = $conn->prepare("SELECT * FROM administradores WHERE usuario = :usuario");
                $stmt->bindParam(':usuario', $usuario);
                $stmt->execute();

                if ($stmt->rowCount() === 1) {
                    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (password_verify($contrasenia, $admin["contrasenia"])) {
                        // Mantener exactamente lo que ya usabas:
                        $_SESSION["admin"] = $admin["usuario"];
                        // Por si acaso, limpiar la sesión de cliente
                        unset($_SESSION['cliente']);

                        header("Location: sesionadmin.php");
                        exit();
                    } else {
                        $mensaje = "Contraseña incorrecta para administrador.";
                    }
                } else {
                    $mensaje = "Usuario de administrador no encontrado.";
                }
            }

        } else {
            // ---- LOGIN CLIENTE (usa correo + contrasenia con crypt) ----
            $correo = trim($_POST['correo'] ?? '');

            if ($correo === '' || $contrasenia === '') {
                $mensaje = "Debes ingresar correo y contraseña.";
            } else {
                $sql = "SELECT id, nombre, correo 
                        FROM usuarios 
                        WHERE correo = :correo 
                          AND contrasenia = crypt(:contrasenia, contrasenia)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':correo'      => $correo,
                    ':contrasenia' => $contrasenia
                ]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($usuario) {
                    // Mantener exactamente la estructura que ya usabas:
                    $_SESSION['cliente'] = [
                        'id'     => $usuario['id'],
                        'nombre' => $usuario['nombre'],
                        'correo' => $usuario['correo']
                    ];
                    // Limpiar sesión admin por si acaso
                    unset($_SESSION['admin']);

                    header("Location: sesionusuario.php");
                    exit();
                } else {
                    $mensaje = "Correo o contraseña incorrectos para cliente.";
                }
            }
        }

    } catch (PDOException $e) {
        $mensaje = "Error al iniciar sesión: " . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Inicio de sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css" />
</head>
<body>

<div class="formulario-inicio">
    <h1>Inicio de sesión</h1>

    <?php if ($mensaje): ?>
        <div class="alert alert-danger mt-2"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <!-- Selector de rol -->
        <div class="mb-3">
            <label class="form-label d-block">Ingresar como:</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="radio"
                       name="rol"
                       id="rolCliente"
                       value="cliente"
                    <?= $rolSeleccionado === 'cliente' ? 'checked' : '' ?>>
                <label class="form-check-label" for="rolCliente">Cliente</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="radio"
                       name="rol"
                       id="rolAdmin"
                       value="admin"
                    <?= $rolSeleccionado === 'admin' ? 'checked' : '' ?>>
                <label class="form-check-label" for="rolAdmin">Administrador</label>
            </div>
        </div>

        <!-- Grupo cliente: CORREO -->
        <div id="grupo-cliente">
            <label for="correo">Correo Electrónico</label>
            <input 
                type="text" id="correo" name="correo" placeholder="Correo Electrónico..."
                minlength="5"
                pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$"
                value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
        </div>

        <!-- Grupo admin: USUARIO -->
        <div id="grupo-admin">
            <label for="usuario">Usuario (administrador)</label>
            <input 
                type="text" id="usuario" name="usuario" placeholder="Usuario..."
                value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>">
        </div>

        <label for="contrasenia">Contraseña</label>
        <input type="password" id="contrasenia" name="contrasenia" placeholder="Contraseña..." required minlength="6">

        <div class="botones mt-3">
            <button type="submit">Iniciar Sesión</button>
            <button type="button" onclick="window.location.href='index.php'">Volver al Inicio</button>
        </div>

        <div id="link-registro" class="mt-2">
            <a href="registrousuario.php">¿No tienes una cuenta? Regístrate aquí...</a>
        </div>
    </form>
</div>

<script>
// Mostrar/ocultar campos según el rol seleccionado
function actualizarCampos() {
    const rol = document.querySelector('input[name="rol"]:checked').value;
    const grupoCliente = document.getElementById('grupo-cliente');
    const grupoAdmin   = document.getElementById('grupo-admin');
    const linkRegistro = document.getElementById('link-registro');

    if (rol === 'admin') {
        grupoAdmin.style.display   = 'block';
        grupoCliente.style.display = 'none';
        linkRegistro.style.display = 'none'; // registro solo tiene sentido para clientes
    } else {
        grupoAdmin.style.display   = 'none';
        grupoCliente.style.display = 'block';
        linkRegistro.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="rol"]');
    radios.forEach(r => r.addEventListener('change', actualizarCampos));
    actualizarCampos();
});

// Validación sencilla en cliente (similar a la que ya tenías)
document.querySelector("form").addEventListener("submit", function(e) {
    const rol = document.querySelector('input[name="rol"]:checked').value;
    const contrasenia = document.getElementById("contrasenia").value;
    
    if (rol === 'cliente') {
        const correo = document.getElementById("correo").value.trim();
        if (!correo.includes("@") || !correo.includes(".")) {
            alert("El correo no es válido.");
            e.preventDefault();
            return;
        }
    } else {
        const usuario = document.getElementById("usuario").value.trim();
        if (usuario === "") {
            alert("Debes ingresar el usuario de administrador.");
            e.preventDefault();
            return;
        }
    }

    if (contrasenia.length < 6) {
        alert("La contraseña debe tener al menos 6 caracteres.");
        e.preventDefault();
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
