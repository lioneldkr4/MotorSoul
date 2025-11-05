<?php
session_start();
if (!isset($_SESSION['cliente'])) {
    header("Location: iniciarsesionusuario.php");
    exit();
}

$nombreCliente = $_SESSION['cliente']['nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorSoul</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<header>
    <nav>
        <div>
            <img src="imagenes/logo.png" alt="" class="logo">
        </div>
        <ul>
            <button type="button" class="boton-nav" onclick="window.location.href='catalogo.php'">Catálogo de Motos</button>   
            <button type="button" class="boton-nav" onclick="window.location.href='compras.php'">Mis Compras</button>   
            <button type="button" class="boton-nav" onclick="window.location.href='perfil.php'">Mi Cuenta</button>
            <button type="button" class="boton-nav" onclick="window.location.href='cerrarsesionusuario.php'">Cerrar sesión</button>
        </ul>
    </nav>
</header>

<div id="banner">
    <h1>¡Bienvenido, <?= htmlspecialchars($nombreCliente) ?>!</h1>
</div>

<div id="introduccion" class="seccion-intro">
    <div class="contenedor-intro">
        <div class="texto-intro">
            <h2>Bienvenido a MotorSoul</h2>
            <p>En MotorSoul, te ofrecemos el catálogo más completo de motocicletas
                de diversas marcas, modelos y estilos. 
                Ya sea que busques comprar, vender o comparar motocicletas, aquí encontrarás 
                toda la información que necesitas para tomar la mejor decisión.
            </p>
        </div>
        <div class="imagen-intro">
            <img src="imagenes/introduccion.png" alt="Motocicletas Ola">
        </div>
    </div>
</div>

<div id="modelos">
    <h2>Modelos Destacados</h2>
    <div class="modelos-destacados">
        <div class="destacados">
            <img src="imagenes/cb.jpg" alt="Modelo CB">
            <h3>Honda CB190R</h3>
        </div>
        <div class="destacados">
            <img src="imagenes/ns.jpg" alt="Modelo NS">
            <h3>Pulsar NS200</h3>
        </div>
    </div>
</div>

    <div id="marcas">
        <h2>ALGUNAS DE NUESTRAS MARCAS</h2>
        <p class="subtitulo">Conoce las marcas líderes que forman parte de nuestro catálogo</p>
        <p class="descripcion">
            Trabajamos con fabricantes reconocidos por su calidad, innovación y desempeño.
        </p>
        <div class="contenedor">
            <div class="tarjeta">
                <img src="imagenes/bajaj.png" alt="Bajaj">
                <h3>Bajaj</h3>
                <p>Marca india reconocida por su eficiencia, durabilidad y excelente relación calidad-precio.</p>
            </div>
            <div class="tarjeta">
                <img src="imagenes/honda.png" alt="Honda">
                <h3>Honda</h3>
                <p>Prestigio japonés con décadas de innovación, confiabilidad y potencia en dos ruedas.</p>
            </div>
            <div class="tarjeta">
                <img src="imagenes/yamaha.png" alt="Yamaha">
                <h3>Yamaha</h3>
                <p>Velocidad, estilo y tecnología de punta. Ideal para quienes buscan adrenalina y confort.</p>
            </div>
            <div class="tarjeta">
                <img src="imagenes/italika.png" alt="Italika">
                <h3>Italika</h3>
                <p>La marca mexicana líder en movilidad accesible, con diseños modernos y funcionales.</p>
            </div>
        </div>
    </div>

<footer>
    <p id="arroba">© 2025 MotorSoul. Todos los derechos reservados.</p>
    <ul>
        <li><a href="#">Aviso de privacidad</a></li>
        <li><a href="#">Términos y condiciones</a></li>
    </ul>
</footer>
</body>
</html>
