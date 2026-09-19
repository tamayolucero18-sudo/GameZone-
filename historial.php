<?php
require __DIR__ . '/datos.php';

$historial = [];
if (is_file(__DIR__ . '/pedidos.log')) {
    foreach (file(__DIR__ . '/pedidos.log', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $pedido = json_decode($linea, true);
        if (is_array($pedido)) {
            $historial[] = $pedido;
        }
    }
    $historial = array_reverse($historial);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Historial de compras — GameZone</title>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="header">
    <div class="header__inner">
      <a class="logo" href="index.php">Game<span>Zone</span></a>
      <nav class="nav" aria-label="Principal">
        <a href="index.php">Inicio</a>
        <a href="historial.php">Historial</a>
      </nav>
    </div>
  </header>

  <main class="historial">
    <div class="catalogo__head">
      <h2 class="section-title">Historial</h2>
      <p class="catalogo__count"><?= count($historial) ?> compras registradas</p>
    </div>

    <?php if (!$historial): ?>
      <p class="vacio">Todavía no hay compras registradas en este historial.</p>
    <?php else: ?>
      <div class="historial__list">
        <?php foreach ($historial as $pedido): ?>
          <article class="historial__item">
            <div class="historial__top">
              <div>
                <p class="historial__label">Cliente</p>
                <h3><?= e($pedido['cliente']['nombre'] ?? 'Cliente') ?></h3>
              </div>
              <span class="historial__folio"><?= e($pedido['folio'] ?? 'Sin folio') ?></span>
            </div>

            <p class="historial__fecha"><?= e(date('d/m/Y H:i', strtotime($pedido['fecha'] ?? 'now'))) ?></p>

            <ul class="historial__lineas">
              <?php foreach ($pedido['lineas'] ?? [] as $linea): ?>
                <li>
                  <span><?= e($linea['nombre'] ?? 'Juego') ?> × <?= (int)($linea['cantidad'] ?? 1) ?></span>
                  <strong><?= money((float)($linea['importe'] ?? 0)) ?></strong>
                </li>
              <?php endforeach; ?>
            </ul>

            <div class="historial__totales">
              <span>Subtotal</span>
              <strong><?= money((float)($pedido['totales']['subtotal'] ?? 0)) ?></strong>
            </div>
            <div class="historial__totales">
              <span>IVA</span>
              <strong><?= money((float)($pedido['totales']['iva'] ?? 0)) ?></strong>
            </div>
            <div class="historial__totales historial__totales--final">
              <span>Total</span>
              <strong><?= money((float)($pedido['totales']['total'] ?? 0)) ?></strong>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="historial__footer">
      <a class="btn btn--primary" href="index.php">Volver a la página principal</a>
    </div>
  </main>
</body>
</html>
