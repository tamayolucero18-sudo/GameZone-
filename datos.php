<?php
/**
 * GameZone · datos.php
 * Catálogo, estado del carrito (sesión) y funciones auxiliares.
 * Este archivo se incluye al inicio de todas las páginas.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const IVA        = 0.16;
const CATEGORIAS = ['Todos', 'Acción', 'Aventura', 'Deportes', 'RPG', 'Estrategia'];

/** Catálogo autoritativo: los precios reales viven aquí, en el servidor. */
$JUEGOS = [
  1  => ['nombre'=>'Neon Vanguard',     'categoria'=>'Acción',     'plataforma'=>'Samsung SM-A566E', 'compatible'=>'Samsung SM-A566E', 'precio'=>1199, 'anio'=>2025, 'estudio'=>'Volta Interactive', 'desc'=>'Bienvenido a Neon Vanguard, un juego arcade sencillo y casual diseñado para el entretenimiento rápido. Con su estilo minimalista neón y mecánicas fáciles de aprender, es una opción relajante para pasar el rato.
Nos centramos en ofrecer una experiencia de juego pura y simple, sin sistemas complicados ni tutoriales extensos' ],
  2  => ['nombre'=>'Ashen Kingdoms',    'categoria'=>'RPG',        'plataforma'=>'PC (Windows, macOS y Linux)',    'precio'=>1499, 'anio'=>2024, 'estudio'=>'Grey Lantern',      'desc'=>'Es una aventura RPG hecha a mano que transforma la experiencia base de Minecraft. Está completamente diseñada en torno a la exploración de nuevas dimensiones, progresión profunda de personajes, forja de equipamiento poderoso, recolección de reliquias antiguas y el enfrentamiento contra imponentes jefes y criaturas legendarias'],
  3  => ['nombre'=>'Turbo Circuit 9',   'categoria'=>'Deportes',   'plataforma'=>'Xbox',   'precio'=>999,  'anio'=>2025, 'estudio'=>'Redline Studio',    'desc'=>'Simulador de carreras con 40 circuitos y clima dinámico.'],
  4  => ['nombre'=>'Isla Mareaverde',   'categoria'=>'Aventura',   'plataforma'=>'Switch', 'precio'=>899,  'anio'=>2023, 'estudio'=>'Coralwave',         'desc'=>'Exploración tranquila de un archipiélago lleno de secretos.'],
  5  => ['nombre'=>'Dominio Táctico',   'categoria'=>'Estrategia', 'plataforma'=>'PC',     'precio'=>749,  'anio'=>2024, 'estudio'=>'Hexline Games',     'desc'=>'Estrategia por turnos sobre mapas hexagonales modulares.'],
  6  => ['nombre'=>'Cazador de Ecos',   'categoria'=>'Acción',     'plataforma'=>'PS5',    'precio'=>1299, 'anio'=>2025, 'estudio'=>'Nocturn',           'desc'=>'Acción en tercera persona con combate basado en sonido.'],
  7  => ['nombre'=>'Corona de Ceniza',  'categoria'=>'RPG',        'plataforma'=>'PC',     'precio'=>1099, 'anio'=>2022, 'estudio'=>'Grey Lantern',      'desc'=>'RPG táctico con seis finales y party personalizable.'],
  8  => ['nombre'=>'Liga Estelar FC',   'categoria'=>'Deportes',   'plataforma'=>'PS5',    'precio'=>1399, 'anio'=>2026, 'estudio'=>'Kickpoint',         'desc'=>'Fútbol arcade con ligas en línea y modo carrera.'],
  9  => ['nombre'=>'Ruta 88',           'categoria'=>'Aventura',   'plataforma'=>'Xbox',   'precio'=>649,  'anio'=>2023, 'estudio'=>'Slowlight',         'desc'=>'Road trip narrativo por carreteras olvidadas.'],
  10 => ['nombre'=>'Imperio de Hierro', 'categoria'=>'Estrategia', 'plataforma'=>'PC',     'precio'=>849,  'anio'=>2024, 'estudio'=>'Forgeworks',        'desc'=>'Construye, comercia y defiende una civilización industrial.'],
  11 => ['nombre'=>'Pulso Cero',        'categoria'=>'Acción',     'plataforma'=>'Xbox',   'precio'=>799,  'anio'=>2023, 'estudio'=>'Volta Interactive', 'desc'=>'Roguelite frenético con armas generadas cada partida.'],
  12 => ['nombre'=>'Bosque de Hilos',   'categoria'=>'Aventura',   'plataforma'=>'Switch', 'precio'=>559,  'anio'=>2022, 'estudio'=>'Coralwave',         'desc'=>'Puzles artesanales en un bosque tejido a mano.'],
  13 => ['nombre'=>'Dinastía Arcana',   'categoria'=>'RPG',        'plataforma'=>'Switch', 'precio'=>1049, 'anio'=>2025, 'estudio'=>'Moonforge',         'desc'=>'JRPG clásico con combate por turnos y magia elemental.'],
  14 => ['nombre'=>'Slam Dunk Arena',   'categoria'=>'Deportes',   'plataforma'=>'Xbox',   'precio'=>899,  'anio'=>2024, 'estudio'=>'Kickpoint',         'desc'=>'Básquetbol 3v3 callejero con físicas exageradas.'],
  15 => ['nombre'=>'Frontera Roja',     'categoria'=>'Estrategia', 'plataforma'=>'PC',     'precio'=>929,  'anio'=>2025, 'estudio'=>'Hexline Games',     'desc'=>'Guerra en tiempo real con logística y moral de tropas.'],
  16 => ['nombre'=>'Último Tren',       'categoria'=>'Aventura',   'plataforma'=>'PC',     'precio'=>599,  'anio'=>2023, 'estudio'=>'Slowlight',         'desc'=>'Thriller de misterio en un vagón que nunca se detiene.'],
  17 => ['nombre'=>'Garra de Titán',    'categoria'=>'Acción',     'plataforma'=>'PS5',    'precio'=>1349, 'anio'=>2026, 'estudio'=>'Nocturn',           'desc'=>'Duelos colosales contra jefes de cien metros.'],
  18 => ['nombre'=>'Códice Perdido',    'categoria'=>'RPG',        'plataforma'=>'Xbox',   'precio'=>1149, 'anio'=>2024, 'estudio'=>'Moonforge',         'desc'=>'RPG de exploración con hechizos que se escriben a mano.'],
  19 => ['nombre'=>'Pista Blanca',      'categoria'=>'Deportes',   'plataforma'=>'Switch', 'precio'=>699,  'anio'=>2022, 'estudio'=>'Redline Studio',    'desc'=>'Snowboard arcade con descensos procedurales.'],
  20 => ['nombre'=>'Colonia Orbital',   'categoria'=>'Estrategia', 'plataforma'=>'PS5',    'precio'=>989,  'anio'=>2026, 'estudio'=>'Forgeworks',        'desc'=>'Gestiona una colonia espacial al límite de sus recursos.'],
];

/** Escapa texto antes de imprimirlo en el HTML. */
function e(string $txt): string {
    return htmlspecialchars($txt, ENT_QUOTES, 'UTF-8');
}

/** Formatea un importe como moneda mexicana. */
function money(float $n): string {
    return '$' . number_format($n, 2) . ' MXN';
}

/** Genera la portada de un juego, usando la imagen local cuando existe. */
function portada(string $nombre, string $color = 'FF2D75'): string {
    $nombreNormalizado = trim($nombre);

    if (strcasecmp($nombreNormalizado, 'Neon Vanguard') === 0) {
        return 'Imagenes/Neon%20Vanguard.jpg';
    }

    if (strcasecmp($nombreNormalizado, 'Ashen Kingdoms') === 0) {
        return 'Imagenes/Ashen%20Kingdoms.jpg';
    }

    return 'https://placehold.co/600x800/1E1B36/' . $color . '?text=' . rawurlencode($nombre);
}

/** El carrito guardado en sesión: [ id => cantidad ]. */
function carrito(): array {
    return $_SESSION['carrito'] ?? [];
}

/** Número total de unidades en el carrito. */
function unidades(): int {
    return array_sum(carrito());
}

/** Calcula subtotal, IVA y total a partir del catálogo del servidor. */
function totales(array $JUEGOS): array {
    $subtotal = 0.0;
    foreach (carrito() as $id => $qty) {
        if (isset($JUEGOS[$id])) {
            $subtotal += $JUEGOS[$id]['precio'] * $qty;
        }
    }
    $iva = $subtotal * IVA;
    return ['subtotal' => $subtotal, 'iva' => $iva, 'total' => $subtotal + $iva];
}

/**
 * Construye una URL conservando los parámetros actuales.
 * Pasa null en una clave para eliminarla: url(['juego' => null]).
 */
function url(array $cambios = [], string $base = 'index.php', string $hash = ''): string {
    $params = array_merge($_GET, $cambios);
    $params = array_filter($params, fn($v) => $v !== null && $v !== '');
    $qs = http_build_query($params);
    return e($base . ($qs ? "?$qs" : '') . $hash);
}
