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
  3  => ['nombre'=>'Turbo Circuit',   'categoria'=>'Deportes',   'plataforma'=>'Famicom Disk System (Nintendo).',   'precio'=>999,  'anio'=>1987, 'estudio'=>'Nintendo / HAL Laboratory',    'desc'=>' Es un circuito ambientado en una zona desértica. Su dificultad radica en que está compuesto casi en su totalidad por curvas cerradas en zigzag de 90°, con giros ocasionales de 45° y 135° que ponen a prueba los reflejos del jugador'],
  4  => ['nombre'=>'Isla Mareaverde',   'categoria'=>'Aventura',   'plataforma'=>' Nintendo Switch, PC', 'precio'=>899,  'anio'=>2026, 'estudio'=>'Coralwave',         'desc'=>'Es un relajante simulador de vida y granja de estilo cozy art en 2D. Los jugadores asumen el papel de Ana, una joven que gestiona su propio terreno frente a la playa, cultiva vegetales, cuida flores y recolecta recursos costeros. El objetivo principal es prosperar en la agricultura mientras se interactúa con los habitantes y comercios locales en el cercano Puerto Mareaverde.'],
  5  => ['nombre'=>'Fire Fire',   'categoria'=>'Estrategia', 'plataforma'=>'iOS y Android',     'precio'=>749,  'anio'=>2017, 'estudio'=>'vietnamita 111dots Studio y publicado por la empresa de Singapur, Garena',     'desc'=>'Su dinámica consiste en partidas rápidas de aproximadamente 10 minutos en las que 50 jugadores caen en paracaídas sobre una isla remota. El objetivo principal es explorar el mapa, conseguir armas y equipamiento médico, y eliminar a los rivales mientras el área de juego se reduce constantemente, todo para convertirse en el último sobreviviente.'],
  6  => ['nombre'=>'Elden Ring',   'categoria'=>'Acción',     'plataforma'=>'PS5, PS4, PC, XBOX Series X/S, XBOX ONE',    'precio'=>1033, 'anio'=>2022, 'estudio'=>'FromSoftware',           'desc'=>'Elden Ring nos llevará a un mundo convulso, complejo y sangriento. En esta ocasión, el equipo japonés ha aumentado la escala del título, trasladando y depurando su conocida jugabilidad a una nueva dimensión. El nuevo universo del videojuego será mucho más grande, más que ningún otro juego anterior de FromSoftware, con extensiones de terreno llenas de enemigos, criaturas extrañas y desafíos.'],
  7  => ['nombre'=>'The Witcher 3: Wild Hunt',  'categoria'=>'RPG',        'plataforma'=>'PC, PS4, PS5, Xbox One',     'precio'=>860, 'anio'=>2015, 'estudio'=>'CD Projekt RED.',      'desc'=>'El juego te pone en la piel de Geralt de Rivia, un cazador de monstruos mercenario conocido como "brujo". En un vasto continente de fantasía oscura devastado por la guerra e inspirado en el folclore eslavo, tu misión principal es seguir el rastro de Ciri, la niña de la profecía. Ella representa un arma viviente capaz de alterar el destino del mundo, mientras es perseguida por una orden de espectros conocida como la Cacería Salvaje (Wild Hunt).'],
  8  => ['nombre'=>'Resident Evil 4',   'categoria'=>'Acción',   'plataforma'=>'PS5',    'precio'=>1399, 'anio'=>2023, 'estudio'=>'Capcom',         'desc'=>'Un intenso juego de acción y survival horror donde Leon S. Kennedy se adentra en una misteriosa aldea europea para rescatar a la hija del presidente. Enfréntate a enemigos aterradores, resuelve acertijos y sobrevive a una aventura llena de tensión y combates.'],
  9  => ['nombre'=>'EA SPORTS FC 25',   'categoria'=>'Deportes',   'plataforma'=>'Xbox',   'precio'=>649,  'anio'=>2023, 'estudio'=>'Laliga',         'desc'=>'Disfruta de una experiencia de fútbol más realista con cientos de equipos, jugadores y competiciones. Compite en diferentes modos de juego, crea tu propio equipo y lleva tu carrera futbolística hasta lo más alto.'],
  10 => ['nombre'=>'MotoGP 21',   'categoria'=>'Deportes',   'plataforma'=>'Switch',   'precio'=>899,  'anio'=>2021, 'estudio'=>'Milestone',         'desc'=>'Vive la emoción del campeonato de MotoGP con carreras llenas de velocidad y competencia. Elige a tus pilotos y equipos favoritos, mejora tu motocicleta y compite en diferentes circuitos para demostrar que puedes llegar a lo más alto.'],
  11 => ['nombre'=>'Bloodborne',        'categoria'=>'Acción',     'plataforma'=>'PS4',   'precio'=>1399,  'anio'=>2015, 'estudio'=>'FromSoftware', 'desc'=>'Adéntrate en la oscura ciudad de Yharnam, un lugar consumido por una misteriosa enfermedad y criaturas aterradoras. Explora sus calles, enfrenta enemigos desafiantes y descubre los secretos de una historia llena de misterio, horror y combates intensos.'],
  12 => ['nombre'=>'Bosque de Hilos',   'categoria'=>'Aventura',   'plataforma'=>'Switch', 'precio'=>559,  'anio'=>2022, 'estudio'=>'Coralwave',         'desc'=>'Puzles artesanales en un bosque tejido a mano.'],
  13 => ['nombre'=>'Dinastía Arcana',   'categoria'=>'RPG',        'plataforma'=>'Switch', 'precio'=>1049, 'anio'=>2025, 'estudio'=>'Moonforge',         'desc'=>'JRPG clásico con combate por turnos y magia elemental.'],
  14 => ['nombre'=>'Imperio de Hierro', 'categoria'=>'Estrategia', 'plataforma'=>'PC',     'precio'=>849,  'anio'=>2024, 'estudio'=>'Forgeworks',        'desc'=>'Construye, comercia y defiende una civilización industrial.'],
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

    if (strcasecmp($nombreNormalizado, 'Turbo Circuit') === 0) {
        return 'Imagenes/Turbo%20Circuit.jpg';
    }

    if (strcasecmp($nombreNormalizado, 'Isla Mareaverde') === 0) {
        return 'Imagenes/Isla%20Mareaverde.jpg';
    }

    if (strcasecmp($nombreNormalizado, 'Fire Fire') === 0) {
        return 'Imagenes/Fire%20Fire.jpg';
    }

    if (strcasecmp($nombreNormalizado, 'Elden Ring') === 0) {
        return 'Imagenes/elden-ring.jpg';
    }

    if (strcasecmp($nombreNormalizado, 'The Witcher 3: Wild Hunt') === 0) {
        return 'Imagenes/The%20Witcher%203_Wild%20Hunt.jpg';
    }
    if (strcasecmp($nombreNormalizado, 'Resident Evil 4') === 0) {
        return 'Imagenes/Resident%20Evil%204.jpg';
    }
    if (strcasecmp($nombreNormalizado, 'EA SPORTS FC 25') === 0) {
        return 'Imagenes/EA%20SPORTS%20FC%2025.jpg';
    }
    if (strcasecmp($nombreNormalizado, 'MotoGP 21') === 0) {
        return 'Imagenes/MotoGP%2021.jpg';
    }
    if (strcasecmp($nombreNormalizado, 'Bloodborne') === 0) {
        return 'Imagenes/Bloodborne.jpg';
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
