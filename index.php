<?php
/**
 * GameZone · index.php
 * Vista principal. Todo el estado viaja por GET (filtro, búsqueda, modales)
 * y por la sesión (carrito). No se usa JavaScript.
 */
require __DIR__ . '/datos.php';

/* ---------- Estado leído de la URL ---------- */
$categoria    = $_GET['cat'] ?? 'Todos';
if (!in_array($categoria, CATEGORIAS, true)) $categoria = 'Todos';

$busqueda     = trim($_GET['q'] ?? '');
$juegoAbierto = isset($_GET['juego']) && isset($JUEGOS[(int)$_GET['juego']]) ? (int)$_GET['juego'] : null;
$carritoAbierto  = isset($_GET['carrito']);
$checkoutAbierto = isset($_GET['checkout']) && unidades() > 0;

/* ---------- Filtrado del catálogo (servidor) ---------- */
$visibles = array_filter($JUEGOS, function ($j) use ($categoria, $busqueda) {
    $coincideCat = $categoria === 'Todos' || $j['categoria'] === $categoria;
    if ($busqueda === '') return $coincideCat;
    $texto = mb_strtolower($j['nombre'] . ' ' . $j['plataforma'] . ' ' . $j['categoria']);
    return $coincideCat && str_contains($texto, mb_strtolower($busqueda));
});

$t = totales($JUEGOS);
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
// URL actual sin los parámetros de modal, para los formularios POST.
$volver = 'index.php?' . http_build_query(array_filter([
    'cat' => $categoria !== 'Todos' ? $categoria : null,
    'q'   => $busqueda ?: null,
]));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>GameZone — Tienda de videojuegos</title>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- ============ HEADER ============ -->
<header class="header">
  <div class="header__inner">
    <a class="logo" href="index.php">Game<span>Zone</span></a>

    <nav class="nav" aria-label="Principal">
      <a href="index.php">Inicio</a>
      <a href="<?= url([], 'index.php', '#catalogo') ?>">Catálogo</a>
      <a href="<?= url([], 'index.php', '#categorias') ?>">Categorías</a>
      <a href="<?= url([], 'index.php', '#historial') ?>">Historial</a>
    </nav>

    <form class="search" method="get" action="index.php" role="search">
      <?php if ($categoria !== 'Todos'): ?>
        <input type="hidden" name="cat" value="<?= e($categoria) ?>">
      <?php endif; ?>
      <input type="search" name="q" value="<?= e($busqueda) ?>"
             placeholder="Buscar un juego…" aria-label="Buscar videojuegos">
    </form>

    <a class="cart-btn" href="<?= url(['carrito' => 1, 'juego' => null], 'index.php', '#catalogo') ?>">
      Carrito <span class="cart-btn__count"><?= unidades() ?></span>
    </a>
  </div>
</header>

<!-- ============ HERO ============ -->
<section class="hero" id="inicio">
  <div class="hero__copy">
    <p class="hero__kicker">Entrega digital inmediata</p>
    <h1>Tu próxima partida empieza aquí</h1>
    <p class="hero__text">Más de 20 títulos para PC, PS5, Xbox y Switch. Claves originales, activación en minutos y soporte en español.</p>
    <div class="hero__actions">
      <a class="btn btn--primary" href="#catalogo">Ver catálogo</a>
      <a class="btn btn--ghost" href="#categorias">Explorar categorías</a>
    </div>
    <ul class="hero__stats">
      <li><strong><?= count($JUEGOS) ?></strong> títulos disponibles</li>
      <li><strong>4</strong> plataformas</li>
      <li><strong>24/7</strong> activación</li>
    </ul>
  </div>
  <div class="hero__art" aria-hidden="true">
    <img src="Imagenes/GAMEZONE.jpg" alt="GameZone">
  </div>
</section>

<!-- ============ FILTROS ============ -->
<section class="filtros" id="categorias">
  <h2 class="section-title">Categorías</h2>
  <div class="chips">
    <?php foreach (CATEGORIAS as $c): ?>
      <a class="chip<?= $c === $categoria ? ' is-active' : '' ?>"
         href="<?= url(['cat' => $c === 'Todos' ? null : $c, 'juego' => null], 'index.php', '#catalogo') ?>"><?= e($c) ?></a>
    <?php endforeach; ?>
  </div>
</section>

<!-- ============ CATÁLOGO ============ -->
<main class="catalogo" id="catalogo">
  <div class="catalogo__head">
    <h2 class="section-title">Catálogo</h2>
    <p class="catalogo__count"><?= count($visibles) ?> de <?= count($JUEGOS) ?> títulos</p>
  </div>

  <?php if (!$visibles): ?>
    <p class="vacio">Ningún juego coincide con tu búsqueda. Prueba con otro nombre o categoría.</p>
  <?php else: ?>
  <div class="grid">
    <?php foreach ($visibles as $id => $j): ?>
    <article class="card">
      <a class="card__img" href="<?= url(['juego' => $id, 'carrito' => null], 'index.php', '#catalogo') ?>"
         aria-label="Ver detalles de <?= e($j['nombre']) ?>">
        <img src="<?= portada($j['nombre']) ?>" alt="Portada de <?= e($j['nombre']) ?>" loading="lazy">
        <span class="card__badge"><?= e($j['plataforma']) ?></span>
      </a>
      <div class="card__body">
        <h3 class="card__title"><?= e($j['nombre']) ?></h3>
        <p class="card__meta"><?= e($j['categoria']) ?> · <?= $j['anio'] ?></p>
        <p class="card__desc"><?= e($j['desc']) ?></p>
        <div class="card__foot">
          <span class="card__price"><?= money($j['precio']) ?></span>
          <form method="post" action="carrito.php">
            <input type="hidden" name="accion" value="agregar">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="volver" value="<?= e($volver) ?>">
            <button class="btn btn--primary" type="submit">Agregar</button>
          </form>
        </div>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</main>

<!-- ============ HISTORIAL ============ -->
<section class="historial" id="historial">
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
</section>

<!-- ============ MODAL: DETALLE DEL JUEGO ============ -->
<?php if ($juegoAbierto): $j = $JUEGOS[$juegoAbierto]; ?>
<div class="modal" role="dialog" aria-modal="true" aria-label="Detalle de <?= e($j['nombre']) ?>">
  <div class="modal__box modal__box--detalle">
    <a class="modal__close" href="<?= url(['juego' => null], 'index.php', '#catalogo') ?>" aria-label="Cerrar">×</a>
    <div class="detalle">
      <img src="<?= portada($j['nombre'], '21E6C1') ?>" alt="Portada de <?= e($j['nombre']) ?>">
      <div class="detalle__info">
        <p class="detalle__meta"><?= e($j['categoria']) ?> · <?= e($j['plataforma']) ?></p>
        <h3><?= e($j['nombre']) ?></h3>
        <p><?= e($j['desc']) ?></p>
        <ul class="detalle__specs">
          <li><span>Plataforma</span><span><?= e($j['plataforma']) ?></span></li>
          <li><span>Género</span><span><?= e($j['categoria']) ?></span></li>
          <li><span>Estudio</span><span><?= e($j['estudio']) ?></span></li>
          <li><span>Lanzamiento</span><span><?= $j['anio'] ?></span></li>
          <li><span>Entrega</span><span>Clave digital inmediata</span></li>
        </ul>
        <p class="detalle__precio"><?= money($j['precio']) ?></p>
        <form method="post" action="carrito.php">
          <input type="hidden" name="accion" value="agregar">
          <input type="hidden" name="id" value="<?= $juegoAbierto ?>">
          <input type="hidden" name="volver" value="<?= e($volver) ?>">
          <button class="btn btn--primary" type="submit">Agregar al carrito</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ============ CARRITO (drawer) ============ -->
<?php if ($carritoAbierto): ?>
<a class="overlay" href="<?= url(['carrito' => null], 'index.php', '#catalogo') ?>" aria-label="Cerrar carrito"></a>
<aside class="drawer" aria-label="Carrito de compras">
  <div class="drawer__head">
    <h2>Tu carrito</h2>
    <a class="modal__close" href="<?= url(['carrito' => null], 'index.php', '#catalogo') ?>" aria-label="Cerrar">×</a>
  </div>

  <div class="drawer__items">
    <?php if (!carrito()): ?>
      <p class="drawer__vacio">Tu carrito está vacío.<br>Agrega un juego del catálogo para empezar.</p>
    <?php else: foreach (carrito() as $id => $qty): $j = $JUEGOS[$id]; ?>
      <div class="item">
        <img src="<?= portada($j['nombre']) ?>" alt="">
        <div>
          <h4 class="item__title"><?= e($j['nombre']) ?></h4>
          <p class="item__meta"><?= e($j['plataforma']) ?> · <?= money($j['precio']) ?> c/u</p>
          <div class="item__row">
            <form class="qty" method="post" action="carrito.php">
              <input type="hidden" name="id" value="<?= $id ?>">
              <input type="hidden" name="volver" value="<?= e($volver) ?>">
              <button type="submit" name="accion" value="menos" aria-label="Quitar una unidad">−</button>
              <span><?= $qty ?></span>
              <button type="submit" name="accion" value="mas" aria-label="Agregar una unidad">+</button>
            </form>
            <strong><?= money($j['precio'] * $qty) ?></strong>
          </div>
          <form method="post" action="carrito.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="volver" value="<?= e($volver) ?>">
            <button class="item__del" type="submit" name="accion" value="eliminar">Eliminar</button>
          </form>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>

  <div class="drawer__foot">
    <dl class="totales">
      <div><dt>Subtotal</dt><dd><?= money($t['subtotal']) ?></dd></div>
      <div><dt>IVA (16%)</dt><dd><?= money($t['iva']) ?></dd></div>
      <div class="totales__total"><dt>Total</dt><dd><?= money($t['total']) ?></dd></div>
    </dl>
    <?php if (carrito()): ?>
      <a class="btn btn--primary btn--block" href="<?= url(['checkout' => 1, 'carrito' => null], 'index.php', '#catalogo') ?>">Continuar al pago</a>
    <?php else: ?>
      <button class="btn btn--primary btn--block" disabled>Continuar al pago</button>
    <?php endif; ?>
  </div>
</aside>
<?php endif; ?>

<!-- ============ MODAL: CHECKOUT ============ -->
<?php if ($checkoutAbierto): ?>
<div class="modal" role="dialog" aria-modal="true" aria-label="Datos de compra">
  <div class="modal__box">
    <a class="modal__close" href="<?= url(['checkout' => null], 'index.php', '#catalogo') ?>" aria-label="Cerrar">×</a>
    <h3>Datos de compra</h3>

    <?php // Errores devueltos por procesar_pedido.php
    if (!empty($_SESSION['errores'])): ?>
      <p class="form__error"><?= e(implode(' ', $_SESSION['errores'])) ?></p>
    <?php endif; $viejo = $_SESSION['form'] ?? []; unset($_SESSION['errores'], $_SESSION['form']); ?>

    <form method="post" action="procesar_pedido.php">
      <label>Nombre completo
        <input type="text" name="nombre" required minlength="3" placeholder="Ana Ramírez"
               value="<?= e($viejo['nombre'] ?? '') ?>">
      </label>
      <label>Correo electrónico
        <input type="email" name="email" required placeholder="ana@correo.com"
               value="<?= e($viejo['email'] ?? '') ?>">
      </label>
      <label>Dirección de facturación
        <input type="text" name="direccion" required minlength="6" placeholder="Av. Tulum 120, Cancún"
               value="<?= e($viejo['direccion'] ?? '') ?>">
      </label>
      <label>Método de pago
        <select name="pago" required>
          <option value="">Elige un método</option>
          <?php foreach (['Tarjeta de crédito','Tarjeta de débito','PayPal','Transferencia SPEI'] as $m): ?>
            <option<?= ($viejo['pago'] ?? '') === $m ? ' selected' : '' ?>><?= $m ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <p class="form__resumen">
        Subtotal <?= money($t['subtotal']) ?> · IVA <?= money($t['iva']) ?> ·
        <strong>Total <?= money($t['total']) ?></strong>
      </p>
      <button class="btn btn--primary btn--block" type="submit">Pagar ahora</button>
    </form>
  </div>
</div>
<?php endif; ?>

<footer class="footer">
  <p>GameZone — Cancún, México · Claves digitales originales</p>
  <p>Proyecto demostrativo. Los precios y productos son simulados.</p>
</footer>
</body>
</html>
