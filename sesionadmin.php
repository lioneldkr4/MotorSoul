<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoSport</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <nav>
            <div>
                <img src="imagenes/logo.png" alt="" class="logo">
            </div>
            <ul>
            <button type="button" class="boton-nav" onclick="window.location.href='CRUDMotos.php'">CRUD Motocicletas</button>   
            <button type="button" class="boton-nav" onclick="window.location.href='CRUDUsuarios.php'">CRUD Usuarios</button>
            <button type="button" class="boton-nav" onclick="window.location.href='ventas.php'">Ventas</button>   
            <button type="button" class="boton-nav" onclick="window.location.href='registromoto.php'">Registrar Motocicleta</button>   
            <button type="button" class="boton-nav" onclick="window.location.href='cerrarsesionadmin.php'">Cerrar sesión</button>
            </ul>
        </nav>
    </header>

    <div id="banner">
        <h1>Sesión iniciada con éxito</h1>
    </div>

  
</body>
</html>