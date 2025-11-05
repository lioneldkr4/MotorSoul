<?php
session_start();
require_once 'conexion.php';

try {
    $conn = Conexion::conectar();
    $stmt = $conn->query("SELECT * FROM motocicletas");
    $motos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar el catálogo: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Motocicletas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>


<body>

<!-- BUSCADOR -->
<form class="buscador" method="get" action="">
  <span class="icon" aria-hidden="true">🔎</span>
  <input
    type="search"
    class="campo"
    name="q"
    placeholder="Buscar por marca o modelo..."
    aria-label="Buscar motocicletas"
  />
  <button class="btn-buscar" type="submit">Buscar</button>
  <a class="btn-limpiar" href="">Limpiar</a>
</form>

<style>
  .buscador{
    --bg: rgba(0, 255, 187, 0.26);
    --bd: rgba(255,255,255,.22);
    --txt:#e9edf1;
    --mut:#b9c2cf;
    --brand:#4ea1f2;
    display:flex; align-items:center; gap:10px;
    background: var(--bg);
    border:1px solid var(--bd);
    border-radius: 999px;
    padding:10px 12px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    width:min(720px, 94%);
    box-shadow: 0 8px 30px rgba(0,0,0,.25);
  }
  .buscador .icon{ font-size:1.1rem; opacity:.9 }
  .buscador .campo{
    flex:1; min-width:0;
    border:0; outline:none;
    background:white; color:green;
    padding:6px 4px; font-size:1rem;
  }
  .buscador .campo::placeholder{ color:green; opacity:.8 }
  .btn-buscar{
    background: #5cc9a7;
    color:green; border:0; font-weight:700;
    padding:8px 14px; border-radius:999px; cursor:pointer;
  }
  .btn-buscar:active{ transform: translateY(1px); }
  .btn-limpiar{
    color:var(--mut); text-decoration:none;
    padding:8px 12px; border-radius:999px; border:1px dashed rgba(31, 244, 92, 0.28);
  }
  .btn-limpiar:hover{ color:var(--txt); border-style:solid; }
</style>



<div class="container">
    <div class="row">
        <?php foreach ($motos as $moto): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="<?= htmlspecialchars($moto['imagen']) ?>" class="card-img-top" alt="Moto" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($moto['marca']) ?> <?= htmlspecialchars($moto['modelo']) ?></h5>
                        <p class="card-text"><strong>Año:</strong> <?= $moto['anio'] ?></p>
                        <p class="card-text"><strong>Precio:</strong> $<?= number_format($moto['precio'], 2) ?></p>
                        <p class="card-text"><strong>HP:</strong> <?= $moto['hp'] ?> HP</p>
                        <p class="card-text"><strong>Color:</strong> <?= htmlspecialchars($moto['color']) ?></p>
<?php if ($moto['existencia'] > 0): ?>
    <form method="post" action="procesar_compra.php" onsubmit="return confirm('¿Confirmar compra?');">
        <input type="hidden" name="id_moto" value="<?= $moto['id'] ?>">

        <label for="metodo_pago_<?= $moto['id'] ?>">Método de pago:</label>
        <select name="metodo_pago" id="metodo_pago_<?= $moto['id'] ?>" required>
            <option value="">Selecciona...</option>
            <option value="Tarjeta">Tarjeta</option>
            <option value="Efectivo">Efectivo</option>
        </select>

        <button type="submit" class="btn btn-success mt-2">Comprar</button>
    </form>
<?php else: ?>
    <p class="text-danger fw-bold mt-3">No disponible por el momento</p>
<?php endif; ?>

                    <script>
                    function confirmarCompra() {
                        return confirm("¿Estás seguro de que deseas comprar esta motocicleta?");
                    }
                    </script>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="text-center mt-4">
    <a href="sesionusuario.php" class="btn btn-secondary">Volver al inicio</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
