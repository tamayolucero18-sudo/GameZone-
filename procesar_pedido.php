<?php
/**
 * GameZone · procesar_pedido.php
 * Valida los datos del comprador, recalcula los totales contra el catálogo
 * del servidor, registra el pedido y muestra la pantalla de confirmación.
 */
require __DIR__ . '/datos.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

/* ---------- 1. Validación ---------- */
$nombre    = trim($_POST['nombre']    ?? '');
$email     = trim($_POST['email']     ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$pago      = trim($_POST['pago']      ?? '');

$errores = [];
if (mb_strlen($nombre) < 3)                     $errores[] = 'Escribe tu nombre completo.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'El correo no tiene un formato válido.';
if (mb_strlen($direccion) < 6)                  $errores[] = 'La dirección está incompleta.';
if ($pago === '')                               $errores[] = 'Elige un método de pago.';
if (!carrito())                                 $errores[] = 'El carrito está vacío.';

// Si algo falla, se devuelven los datos al formulario y se reabre el modal.
if ($errores) {
    $_SESSION['errores'] = $errores;
    $_SESSION['form']    = compact('nombre', 'email', 'direccion', 'pago');
    header('Location: index.php?checkout=1#catalogo');
    exit;
}

/* ---------- 2. Recálculo de totales en el servidor ---------- */
$lineas = [];
foreach (carrito() as $id => $qty) {
    if (!isset($JUEGOS[$id])) continue;
    $lineas[] = [
        'nombre'   => $JUEGOS[$id]['nombre'],
        'cantidad' => $qty,
        'importe'  => $JUEGOS[$id]['precio'] * $qty,
    ];
}
$t     = totales($JUEGOS);
$folio = 'GZ-' . date('ymd') . '-' . str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);

/* ---------- 3. Registro del pedido (simulado) ---------- */
$registro = [
    'folio'   => $folio,
    'fecha'   => date('c'),
    'cliente' => compact('nombre', 'email', 'direccion', 'pago'),
    'lineas'  => $lineas,
    'totales' => array_map(fn($v) => round($v, 2), $t),
];
@file_put_contents(__DIR__ . '/pedidos.log', json_encode($registro, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

// Aquí irían el cobro real y el envío del correo con las claves.
$_SESSION['carrito'] = [];   // Vacía el carrito tras la compra.
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pedido <?= e($folio) ?> — GameZone</title>
<link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="header">
  <div class="header__inner">
    <a class="logo" href="index.php">Game<span>Zone</span></a>
  </div>
</header>

<main class="catalogo">
  <div class="modal__box modal__box--ok" style="margin:3rem auto">
    <div class="ok__mark">✓</div>
    <h3>Pedido confirmado</h3>
    <p>Gracias, <?= e(explode(' ', $nombre)[0]) ?>. Enviamos tus claves a <?= e($email) ?>.</p>
    <p class="ok__folio">Folio <?= e($folio) ?></p>

    <ul class="detalle__specs" style="text-align:left">
      <?php foreach ($lineas as $l): ?>
        <li><span><?= e($l['nombre']) ?> × <?= $l['cantidad'] ?></span><span><?= money($l['importe']) ?></span></li>
      <?php endforeach; ?>
      <li><span>Subtotal</span><span><?= money($t['subtotal']) ?></span></li>
      <li><span>IVA (16%)</span><span><?= money($t['iva']) ?></span></li>
      <li><span>Total pagado con <?= e($pago) ?></span><span><?= money($t['total']) ?></span></li>
      <li><span>Envío a</span><span><?= e($direccion) ?></span></li>
    </ul>

    <a class="btn btn--primary btn--block" href="index.php">Seguir comprando</a>
  </div>
</main>

<footer class="footer">
  <p>GameZone — Cancún, México · Claves digitales originales</p>
</footer>
</body>
</html>
