<?php
/**
 * GameZone · carrito.php
 * Controlador POST del carrito. Modifica la sesión y vuelve a index.php.
 * Nunca imprime HTML: siempre redirige (patrón POST → Redirect → GET).
 */
require __DIR__ . '/datos.php';

$accion  = $_POST['accion']  ?? '';
$id      = (int)($_POST['id'] ?? 0);
$volver  = $_POST['volver'] ?? 'index.php';

// El juego debe existir en el catálogo del servidor.
if ($id && isset($JUEGOS[$id])) {
    $carrito = carrito();

    switch ($accion) {
        case 'agregar':
            $carrito[$id] = ($carrito[$id] ?? 0) + 1;
            break;

        case 'mas':
            $carrito[$id] = min(99, ($carrito[$id] ?? 0) + 1);
            break;

        case 'menos':
            $carrito[$id] = ($carrito[$id] ?? 1) - 1;
            if ($carrito[$id] <= 0) unset($carrito[$id]);
            break;

        case 'eliminar':
            unset($carrito[$id]);
            break;
    }

    $_SESSION['carrito'] = $carrito;
}

if ($accion === 'vaciar') {
    $_SESSION['carrito'] = [];
}

// Vuelve a la página anterior con el carrito abierto.
$destino = str_contains($volver, '?') ? $volver . '&carrito=1' : $volver . '?carrito=1';
header('Location: ' . $destino . '#catalogo');
exit;
