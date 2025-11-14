<?php
session_start();
require_once 'conexion.php';

try {
    $conn = Conexion::conectar();

    // --------- Filtros desde GET ----------
    $criterio    = isset($_GET['criterio']) ? $_GET['criterio'] : 'marca';
    $q           = isset($_GET['q']) ? trim($_GET['q']) : '';
    $precio_min  = isset($_GET['precio_min']) ? trim($_GET['precio_min']) : '';
    $precio_max  = isset($_GET['precio_max']) ? trim($_GET['precio_max']) : '';

    $sql    = "SELECT * FROM motocicletas";
    $conds  = [];
    $params = [];

    // Filtro por marca
    if ($criterio === 'marca' && $q !== '') {
        // ILIKE = case-insensitive en PostgreSQL
        $conds[]        = "marca ILIKE :q";
        $params[':q']   = "%" . $q . "%";
    }

    // Filtro por tipo
    if ($criterio === 'tipo' && $q !== '') {
        $conds[]        = "tipo ILIKE :q";
        $params[':q']   = "%" . $q . "%";
    }

    // Filtro por rango de precio
    if ($criterio === 'precio') {
        if ($precio_min !== '' && is_numeric($precio_min)) {
            $conds[]              = "precio >= :pmin";
            $params[':pmin']      = (float)$precio_min;
        }
        if ($precio_max !== '' && is_numeric($precio_max)) {
            $conds[]              = "precio <= :pmax";
            $params[':pmax']      = (float)$precio_max;
        }
    }

    if ($conds) {
        $sql .= " WHERE " . implode(" AND ", $conds);
    }

    // Puedes ordenar si quieres, por ejemplo por id desc:
    $sql .= " ORDER BY id DESC";

    $stmt  = $conn->prepare($sql);
    $stmt->execute($params);
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

<!-- BUSCADOR AVANZADO -->
<form class="buscador" method="get" action="">
  <span class="icon" aria-hidden="true">🔎</span>

  <!-- Criterio -->
  <select name="criterio" id="criterio" class="select-criterio">
    <option value="marca"  <?= $criterio === 'marca'  ? 'selected' : '' ?>>Marca</option>
    <option value="tipo"   <?= $criterio === 'tipo'   ? 'selected' : '' ?>>Tipo</option>
    <option value="precio" <?= $criterio === 'precio' ? 'selected' : '' ?>>Rango de precio</option>
  </select>

  <!-- Búsqueda por texto (marca/tipo) -->
  <div id="grupo-texto" class="grupo-texto">
    <input
      type="search"
      class="campo"
      name="q"
      placeholder="Buscar..."
      aria-label="Buscar motocicletas"
      value="<?= htmlspecialchars($q) ?>"
    />
  </div>

  <!-- Búsqueda por rango de precio -->
  <div id="grupo-precio" class="grupo-precio">
    <input
      type="number"
      class="campo-num"
      name="precio_min"
      min="0"
      placeholder="Mín"
      value="<?= htmlspecialchars($precio_min) ?>"
    />
    <span class="separador">-</span>
    <input
      type="number"
      class="campo-num"
      name="precio_max"
      min="0"
      placeholder="Máx"
      value="<?= htmlspecialchars($precio_max) ?>"
    />
  </div>

  <button class="btn-buscar" type="submit">Buscar</button>
  <a class="btn-limpiar" href="catalogo.php">Limpiar</a>
</form>

<style>
  .buscador{
    --bg: rgba(0, 255, 187, 0.26);
    --bd: rgba(255,255,255,.22);
    --txt:#e9edf1;
    --mut:#b9c2cf;
    display:flex; align-items:center; gap:10px;
    background: var(--bg);
    border:1px solid var(--bd);
    border-radius: 999px;
    padding:10px 14px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    width:min(900px, 96%);
    box-shadow: 0 8px 30px rgba(0,0,0,.25);
    margin: 20px auto;
  }
  .buscador .icon{ font-size:1.1rem; opacity:.9 }

  .select-criterio{
    border-radius:999px;
    border:0;
    padding:6px 10px;
    font-size:.95rem;
    outline:none;
  }

  .grupo-texto,
  .grupo-precio{
    display:flex;
    align-items:center;
    gap:8px;
    flex:1;
  }

  .campo{
    flex:1; min-width:0;
    border:0; outline:none;
    background:white; color:green;
    padding:6px 8px; font-size:1rem;
    border-radius:999px;
  }
  .campo::placeholder{ color:green; opacity:.8 }

  .campo-num{
    width: 100%;
    min-width: 0;
    border:0; outline:none;
    background:white; color:green;
    padding:6px 8px; font-size:.9rem;
    border-radius:999px;
  }

  .separador{
    color:green;
    font-weight:bold;
  }

  .btn-buscar{
    background: #5cc9a7;
    color:green; border:0; font-weight:700;
    padding:8px 14px; border-radius:999px; cursor:pointer;
    white-space:nowrap;
  }
  .btn-buscar:active{ transform: translateY(1px); }

  .btn-limpiar{
    color:var(--mut); text-decoration:none;
    padding:8px 12px; border-radius:999px;
    border:1px dashed rgba(31, 244, 92, 0.28);
    white-space:nowrap;
  }
  .btn-limpiar:hover{ color:var(--txt); border-style:solid; }

  @media (max-width: 768px){
    .buscador{
      flex-wrap:wrap;
      row-gap:8px;
    }
    .grupo-precio{
      flex-basis:100%;
    }
    .grupo-texto{
      flex-basis:100%;
    }
  }
</style>

<script>
// Mostrar/ocultar campos según criterio seleccionado
document.addEventListener('DOMContentLoaded', function () {
    const criterioSelect = document.getElementById('criterio');
    const grupoTexto     = document.getElementById('grupo-texto');
    const grupoPrecio    = document.getElementById('grupo-precio');

    function actualizarCampos() {
        if (criterioSelect.value === 'precio') {
            grupoTexto.style.display  = 'none';
            grupoPrecio.style.display = 'flex';
        } else {
            grupoTexto.style.display  = 'flex';
            grupoPrecio.style.display = 'none';
        }
    }

    criterioSelect.addEventListener('change', actualizarCampos);
    actualizarCampos(); // estado inicial según lo que venga en GET
});
</script>

<div class="container mt-4">
    <div class="row">
        <?php if (empty($motos)): ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    No se encontraron motocicletas con esos filtros.
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($motos as $moto): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?= htmlspecialchars($moto['imagen']) ?>" class="card-img-top" alt="Moto" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <?= htmlspecialchars($moto['marca']) ?> <?= htmlspecialchars($moto['modelo']) ?>
                        </h5>

                        <p class="card-text mb-1"><strong>Año:</strong> <?= (int)$moto['anio'] ?></p>
                        <p class="card-text mb-1"><strong>Precio:</strong> $<?= number_format($moto['precio'], 2) ?></p>
                        <p class="card-text mb-1"><strong>HP:</strong> <?= (int)$moto['hp'] ?> HP</p>
                        <p class="card-text mb-2"><strong>Color:</strong> <?= htmlspecialchars($moto['color']) ?></p>

                        <?php if (!empty($moto['descripcion'])): ?>
                            <p class="card-text small text-muted mb-3">
                                <strong>Descripción:</strong><br>
                                <?= nl2br(htmlspecialchars($moto['descripcion'])) ?>
                            </p>
                        <?php endif; ?>

                        <div class="mt-auto">
                        <?php if ($moto['existencia'] > 0): ?>
                            <form method="post" action="procesar_compra.php" onsubmit="return confirm('¿Confirmar compra?');">
                                <input type="hidden" name="id_moto" value="<?= $moto['id'] ?>">

                                <label for="metodo_pago_<?= $moto['id'] ?>">Método de pago:</label>
                                <select name="metodo_pago" id="metodo_pago_<?= $moto['id'] ?>" class="form-select form-select-sm mb-2" required>
                                    <option value="">Selecciona...</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Efectivo">Efectivo</option>
                                </select>

                                <button type="submit" class="btn btn-success w-100">Comprar</button>
                            </form>
                        <?php else: ?>
                            <p class="text-danger fw-bold mt-3">No disponible por el momento</p>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="text-center mt-4 mb-4">
    <a href="sesionusuario.php" class="btn btn-secondary">Volver al inicio</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
