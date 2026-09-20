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

$busqueda     = trim((string)($_GET['q'] ?? ''));
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
<?php
$configAbierto = isset($_GET['config']);
$deseosAbierto = isset($_GET['deseos']);
$loginAbierto  = isset($_GET['login']);
$tieneModal = $juegoAbierto || $carritoAbierto || $checkoutAbierto || $configAbierto || $deseosAbierto || $loginAbierto;
?>
<body class="<?= $tieneModal ? 'modal-open' : '' ?>">

<!-- ============ HEADER ============ -->
<header class="header">
  <div class="header__inner">
    <a class="logo" href="index.php">Game<span>Zone</span></a>

    <form class="search" method="get" action="index.php" role="search">
      <?php if ($categoria !== 'Todos'): ?>
        <input type="hidden" name="cat" value="<?= e($categoria) ?>">
      <?php endif; ?>
      <input type="search" name="q" value="<?= e($busqueda) ?>"
             placeholder="Busca juegos, recargas y más" aria-label="Buscar videojuegos">
    </form>

    <div class="header__utility">
      <a class="header__action-btn header__action-btn--text" href="historial.php">
        <span>Historial</span>
      </a>
      <a class="header__action-btn" href="<?= url(['carrito' => 1, 'juego' => null], 'index.php', '#catalogo') ?>" aria-label="Carrito">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        <?php if (unidades() > 0): ?>
          <span class="cart-btn__count"><?= unidades() ?></span>
        <?php endif; ?>
      </a>
      <a class="header__action-btn header__action-btn--text header__action-btn--login" href="<?= url(['login' => 1], 'index.php') ?>">
        <span>Acceder</span>
      </a>
    </div>
  </div>
</header>

<!-- ============ HERO ============ -->
<section class="hero" id="inicio">
  <div class="hero__copy">
    <p class="hero__kicker">Entrega digital inmediata</p>
    <h1>Tu próxima partida empieza aquí</h1>
    <p class="hero__text">Más de 20 títulos para PC, PS5, Xbox y Switch. Claves originales, activación en minutos y soporte en español.</p>

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
         href="<?= url(['cat' => $c === 'Todos' ? null : $c, 'q' => null, 'juego' => null], 'index.php', '#catalogo') ?>"><?= e($c) ?></a>
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

<!-- ============ MODAL: CONFIGURACIÓN ============ -->
<?php if ($configAbierto): ?>
<div class="modal" role="dialog" aria-modal="true" aria-label="Configuración">
  <div class="modal__box modal__box--config">
    <a class="modal__close" href="<?= url(['config' => null], 'index.php') ?>" aria-label="Cerrar">×</a>
    <h3>Actualiza tu configuración</h3>
    <p class="form__resumen" style="border:0; margin-bottom: 1.5rem; padding:0;">Establece tu región preferida, idioma y moneda preferida.</p>
    <form method="get" action="index.php">
      <label>Región
        <select name="region">
          <option value="MX" selected>🇲🇽 México</option>
        </select>
      </label>
      <label>Idioma
        <select name="idioma">
          <option value="es-419" selected>Español Latinoamericano</option>
        </select>
      </label>
      <label>Moneda
        <select name="moneda">
          <option value="MXN" selected>Peso mexicano (MXN)</option>
        </select>
      </label>
      <div class="config__actions">
        <a class="btn btn--ghost" href="<?= url(['config' => null], 'index.php') ?>">Cancelar</a>
        <button class="btn btn--yellow" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- ============ DRAWER: LISTA DE DESEOS ============ -->
<?php if ($deseosAbierto): ?>
<a class="overlay" href="<?= url(['deseos' => null], 'index.php') ?>" aria-label="Cerrar deseos"></a>
<aside class="drawer drawer--deseos" aria-label="Lista de deseos">
  <div class="drawer__head">
    <h2>Lista de deseos</h2>
    <a class="modal__close" href="<?= url(['deseos' => null], 'index.php') ?>" aria-label="Cerrar">×</a>
  </div>
  <div class="drawer__notice">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
    <p>Configura notificaciones por correo electrónico cuando el precio baje para cualquiera de tus productos incluidos en la lista de deseos. <a href="<?= url(['login' => 1, 'deseos' => null], 'index.php') ?>">Iniciar sesión</a> o Regístrate</p>
  </div>
  <div class="drawer__items" style="align-items: center; justify-content: center;">
    <div class="deseos__vacio">
      <div class="deseos__icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
      </div>
      <p>Todavía no tienes productos en la Lista de deseos.</p>
      <strong>Añade juegos a la Lista de deseos y aparecerán aquí.</strong>
    </div>
  </div>
</aside>
<?php endif; ?>

<!-- ============ MODAL: LOGIN ============ -->
<?php if ($loginAbierto): ?>
<div class="modal modal--login-overlay" role="dialog" aria-modal="true" aria-label="Iniciar sesión">
  <div class="modal__box modal__box--login">
    <a class="modal__close" href="<?= url(['login' => null], 'index.php') ?>" aria-label="Cerrar">×</a>
    
    <div class="login-layout">
      <!-- Columna Izquierda -->
      <div class="login-layout__left">
        <a class="logo logo--login" href="index.php">Game<span>Zone</span></a>
        <h1 class="login__greeting">¡Hola!<br>¡Qué gusto<br>verte!</h1>
      </div>
      <!-- Columna Derecha -->
      <div class="login-layout__right">
        <h2>Iniciar sesión</h2>
        <p class="login__subtitle">¿Nuevo usuario? <a href="#">Crear una cuenta</a></p>
        
        <div class="login__social">
          <button class="btn-social btn-social--google">
            <svg width="20" height="20" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
            Continuar con Google
          </button>
          <button class="btn-social btn-social--facebook">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
            Continuar con Facebook
          </button>
        </div>

        <form method="get" action="index.php" class="login__form">
          <label class="sr-only">Email</label>
          <input type="email" name="email" required placeholder="Email">
          <button class="btn btn--yellow btn--block" type="button">Obtener enlace mágico</button>
          <button class="btn btn--ghost btn--block" type="button" style="margin-top:.8rem; border:none; border-radius:10px; color:#000; background:#fff; font-weight:600;">Iniciar sesión con contraseña</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>



<footer class="footer">
  <p>GameZone — Mérida, Yucatán · Claves digitales originales</p>
  <p>Proyecto demostrativo. Los precios y productos son simulados.</p>
</footer>
</body>
</html>
