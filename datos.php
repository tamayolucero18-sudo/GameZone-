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
  12 => ['nombre'=>'Bosque de Hilos',   'categoria'=>'Aventura',   'plataforma'=>'Switch', 'precio'=>559,  'anio'=>2022, 'estudio'=>'Coralwave',         'desc'=>'Adéntrate en el encantador mundo de Bosque de Hilos, un rincón mágico hecho totalmente a mano con lana y creatividad. Explora senderos de estambre, resuelve ingeniosos puzles artesanales y descubre los secretos que aguardan en un entorno acogedor lleno de color, ternura y sorpresas tejidas con amor.'],
  13 => ['nombre'=>'Dinastía Arcana',   'categoria'=>'RPG',        'plataforma'=>'Switch', 'precio'=>1049, 'anio'=>2025, 'estudio'=>'Moonforge',         'desc'=>'Un RPG clásico donde el origen de tu sangre define el destino del reino. Despierta el poder de linajes olvidados y embárcate en una aventura épica de combate táctico por turnos. Explora tierras ancestrales, domina la magia elemental y combina las habilidades de tu escuadrón para derrocar a las fuerzas oscuras que amenazan con consumirlo todo.'],
  14 => ['nombre'=>'Imperio de Hierro', 'categoria'=>'Estrategia', 'plataforma'=>'PC',     'precio'=>849,  'anio'=>2024, 'estudio'=>'Forgeworks',        'desc'=>'Declara el trono. Construye el imperio. Domina la guerra. Enfréntate al caos en un juego de estrategia en tiempo real donde la maquinaria de guerra y la supremacía militar lo son todo. Construye fortalezas inexpugnables, gestiona tus recursos y lidera legiones acorazadas en campos de batalla masivos para reclamar el control total del mapa.'],
  15 => ['nombre'=>'Frontera Roja',     'categoria'=>'Estrategia', 'plataforma'=>'PC',     'precio'=>929,  'anio'=>2025, 'estudio'=>'Hexline Games',     'desc'=>'En Frontera Roja, el verdadero enemigo no siempre viste el uniforme contrario: es la falta de munición, la escasez de combustible y el pánico que se extiende entre tus filas. Lidera combates tácticos en tiempo real donde trazar rutas de reabastecimiento bajo fuego pesado y monitorear la moral de tu escuadrón es la única diferencia entre la victoria y el colapso total. Un batallón sin suministros o al borde del colapso mental abandonará la línea antes de apretar el primer gatillo.'],
  16 => ['nombre'=>'DOOM Eternal',           'categoria'=>'Acción',     'plataforma'=>'PC, PS5, Xbox', 'precio'=>799,  'anio'=>2020, 'estudio'=>'id Software',       'desc'=>'Experimenta la combinación definitiva de velocidad y poder en este vertiginoso juego de disparos en primera persona. Como el Slayer, regresa a la Tierra para acabar con una invasión demoníaca usando un arsenal devastador de armas, lanzallamas y la mítica motosierra. El combate te exige estar en constante movimiento y ser agresivo para recuperar salud, armadura y munición de tus enemigos caídos.'],
  17 => ['nombre'=>'Red Dead Redemption 2', 'categoria'=>'Aventura',   'plataforma'=>'PC, PS4, Xbox', 'precio'=>1299, 'anio'=>2018, 'estudio'=>'Rockstar Games',  'desc'=>'Una épica historia sobre la vida en Estados Unidos en los albores del siglo XX. Sigue a Arthur Morgan y la banda de Van der Linde mientras huyen de la ley a través de un vasto e implacable territorio, robando y luchando para sobrevivir en el corazón de América en declive. El juego ofrece un mundo abierto enormemente detallado e inmersivo.'],
  18 => ['nombre'=>'Baldur\'s Gate 3',      'categoria'=>'RPG',        'plataforma'=>'PC, PS5, Xbox', 'precio'=>1399, 'anio'=>2023, 'estudio'=>'Larian Studios',  'desc'=>'Reúne a tu grupo y regresa a los Reinos Olvidados en un relato de compañerismo y traición, sacrificio y supervivencia, y la tentación del poder absoluto. Basado en el mundo de Dungeons & Dragons, ofrece una narrativa rica con decisiones que alteran la historia, un profundo combate táctico por turnos y una libertad sin precedentes para explorar y experimentar.'],
  19 => ['nombre'=>'Forza Horizon 5',       'categoria'=>'Deportes',   'plataforma'=>'PC, Xbox',      'precio'=>1499, 'anio'=>2021, 'estudio'=>'Playground Games', 'desc'=>'Lidera impresionantes expediciones a través de los vibrantes y cambiantes paisajes de mundo abierto de México, con una acción de conducción ilimitada y divertida en cientos de los mejores coches del mundo. Explora selvas, ciudades históricas, ruinas, playas escondidas y un enorme volcán cubierto de nieve.'],
  20 => ['nombre'=>'Civilization VI',       'categoria'=>'Estrategia', 'plataforma'=>'PC, Switch',    'precio'=>1199, 'anio'=>2016, 'estudio'=>'Firaxis Games',   'desc'=>'Un juego de estrategia por turnos en el que tu objetivo es construir un imperio que resista el paso del tiempo. Explora un nuevo mundo, investiga tecnologías, conquista a tus enemigos y enfréntate a los líderes históricos más famosos mientras intentas llevar a tu civilización desde la Edad de Piedra hasta la Era de la Información.'],
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
    if (strcasecmp($nombreNormalizado, 'Bosque de Hilos') === 0) {
        return 'Imagenes/Bosque%20de%20Hilos.jpg';
    }
    if (strcasecmp($nombreNormalizado, 'Dinastía Arcana') === 0) {
        return 'Imagenes/Dinastia%20Arcana.jpg';
    }
    if (strcasecmp($nombreNormalizado, 'Imperio de Hierro') === 0) {
        return 'Imagenes/Imperio%20de%20Hierro.jpg';
    }
    if (strcasecmp($nombreNormalizado, 'Frontera Roja') === 0) {
        return 'Imagenes/Frontera%20Roja.jpg';
    }
    if (strcasecmp($nombreNormalizado, 'DOOM Eternal') === 0) {
        return 'Imagenes/DOOM_Eternal.jpg';
    }
    if (strcasecmp($nombreNormalizado, 'Red Dead Redemption 2') === 0) {
        return 'Imagenes/Red_Dead_Redemption_2.jpg';
    }
    if (strcasecmp($nombreNormalizado, 'Baldur\'s Gate 3') === 0) {
        return 'Imagenes/Baldurs_Gate_3.jpg';
    }
    if (strcasecmp($nombreNormalizado, 'Forza Horizon 5') === 0) {
        return 'Imagenes/Forza_Horizon_5.jpg';
    }
    if (strcasecmp($nombreNormalizado, 'Civilization VI') === 0) {
        return 'Imagenes/Civilization_VI.jpg';
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
