<?php
// actualizar_perfil.php
require_once 'conexion.php';
session_start();

// --> Solo clientes logueados pueden usar este endpoint
if (!isset($_SESSION['cliente']) || !isset($_SESSION['cliente']['id'])) {
    header("Location: iniciarsesionusuario.php");
    exit();
}

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: perfil.php");
    exit();
}

// CSRF
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Solicitud inválida (CSRF).");
}

// Obtener y sanitizar campos
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$apellidos = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
$edad = isset($_POST['edad']) ? (int) $_POST['edad'] : null;
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validaciones básicas
$errors = [];
if ($id <= 0) $errors[] = "ID inválido.";
if ($nombre === '') $errors[] = "Nombre obligatorio.";
if ($apellidos === '') $errors[] = "Apellidos obligatorios.";
if ($edad === null || $edad < 0 || $edad > 120) $errors[] = "Edad inválida.";
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo inválido.";

if ($errors) {
    $_SESSION['form_errors'] = $errors;
    header("Location: editar_perfil.php");
    exit();
}

try {
    $conn = Conexion::conectar();

    // Evitar que un cliente edite otro id (forzar id del session cliente)
    $sessionId = (int) $_SESSION['cliente']['id'];
    if ($sessionId !== $id) {
        // Podrías loguear intento malicioso aquí
        die("No tienes permisos para editar este perfil.");
    }

    // Verificar que el correo no esté en uso por otro usuario
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = :correo AND id != :id LIMIT 1");
    $stmt->execute([':correo' => $correo, ':id' => $id]);
    if ($stmt->fetch()) {
        die("El correo ya está en uso por otro usuario.");
    }

    // Construir UPDATE dinámico
    $fields = [
        'nombre' => $nombre,
        'apellidos' => $apellidos,
        'edad' => $edad,
        'correo' => $correo
    ];
    $params = [];
    $setParts = [];

    foreach ($fields as $col => $val) {
    $setParts[] = "$col = :$col"; // ✅ sin comillas invertidas
    $params[":$col"] = $val;
    }

    // Si proporcionaron contraseña, validarla y hashearla
    if (!empty($password)) {
        if (strlen($password) < 6) {
            die("La contraseña debe tener al menos 6 caracteres.");
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $setParts[] = "password = :password";
        $params[':password'] = $hash;
    }

    $params[':id'] = $id;
    $sql = "UPDATE usuarios SET " . implode(", ", $setParts) . " WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    // Refrescar datos en sesión cliente
    $stmt = $conn->prepare("SELECT id, nombre, apellidos, edad, correo FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $fresh = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($fresh) {
        $_SESSION['cliente'] = $fresh;
    }

    $_SESSION['success_message'] = "Perfil actualizado correctamente.";
    header("Location: perfil.php");
    exit();

} catch (PDOException $e) {
    die("Error al actualizar: " . htmlspecialchars($e->getMessage()));
}
