<?php
declare(strict_types=1);

$cards = json_decode(file_get_contents(__DIR__ . '/cards.json'), true, 512, JSON_THROW_ON_ERROR);

// Definición de metadatos por tarjeta (por índice 0-based).
$meta = [
    0  => ['type' => 'apertura',     'label' => 'Apertura'],
    1  => ['type' => 'padrenuestro', 'label' => 'Padre nuestro'],
    2  => ['type' => 'avemaria',     'label' => 'Ave María'],
    3  => ['type' => 'credo',        'label' => 'Credo'],
    59 => ['type' => 'invocacion',   'label' => 'Invocación'],
    60 => ['type' => 'invocacion',   'label' => 'Invocación'],
    61 => ['type' => 'invocacion',   'label' => 'Invocación'],
    62 => ['type' => 'conclusion',   'label' => 'Oración conclusión'],
    63 => ['type' => 'final',        'label' => 'Final'],
];

// Granos: los mayores comienzan en 4, 15, 26, 37, 48; el resto son menores.
function cardMeta(int $i, string $title): array
{
    global $meta;
    if (isset($meta[$i])) {
        return $meta[$i];
    }
    if (str_contains($title, 'Grano Mayor')) {
        return ['type' => 'grano_mayor', 'label' => 'Grano mayor'];
    }
    if (str_contains($title, 'Grano menor')) {
        return ['type' => 'grano_menor', 'label' => 'Grano menor'];
    }
    return ['type' => 'grano_menor', 'label' => 'Grano menor'];
}

// Decena a la que pertenece cada tarjeta (1-5) o null.
function decenaFor(int $i): ?int
{
    if ($i >= 4 && $i <= 58) {
        return intdiv($i - 4, 11) + 1;
    }
    return null;
}

$rendered = '';
foreach ($cards as $i => $card) {
    $m = cardMeta($i, $card['title']);
    $decena = decenaFor($i);
    $body = htmlspecialchars($card['body'], ENT_QUOTES, 'UTF-8');
    $title = htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8');
    $dataDecena = $decena !== null ? ' data-decena="' . $decena . '"' : '';
    $badge = $m['type'] !== 'apertura' ? '<span class="card-badge" data-type="' . $m['type'] . '">' . $m['label'] . '</span>' : '';
    $rendered .= '<article class="card" data-index="' . $i . '" data-type="' . $m['type'] . '"' . $dataDecena . ' tabindex="0" role="group" aria-label="Tarjeta ' . ($i + 1) . ': ' . $title . '">'
        . $badge
        . '<h2 class="card-title">' . $title . '</h2>'
        . '<p class="card-body">' . nl2br($body) . '</p>'
        . '</article>' . "\n";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Coronilla de la Divina Misericordia</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#f7f4ee">
    <meta name="description" content="Rezo guiado de la Coronilla de la Divina Misericordia">
    <link rel="apple-touch-icon" href="icons/icon-180x180.png">
    <link rel="icon" type="image/png" href="icons/icon-192x192.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f7f4ee;
            --surface: #fffdf9;
            --ink: #2c2a27;
            --muted: #7a7569;
            --accent: #1f3a5f;
            --accent-soft: #e9eef5;
            --gold: #b08a3e;
            --gold-soft: #f3e9d3;
            --line: #e6e0d4;
            --serif: 'Cormorant Garamond', Georgia, serif;
            --sans: 'Inter', system-ui, sans-serif;
        }
        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        html, body { height: 100%; }
        body {
            margin: 0;
            font-family: var(--sans);
            background:
                radial-gradient(1200px 600px at 50% -10%, #efe6d4 0%, transparent 60%),
                var(--bg);
            color: var(--ink);
            display: flex;
            flex-direction: column;
            height: 100dvh;
            overflow: hidden;
            overscroll-behavior: none;
            user-select: none;
            -webkit-user-select: none;
        }
        header.app-header {
            flex: 0 0 auto;
            text-align: center;
            padding: max(0.75rem, env(safe-area-inset-top)) 1rem 0.5rem;
            background: linear-gradient(180deg, rgba(247,244,238,0.95) 60%, rgba(247,244,238,0) 100%);
        }
        .app-title {
            font-family: var(--serif);
            font-weight: 600;
            font-size: 1.35rem;
            letter-spacing: 0.02em;
            margin: 0 0 0.25rem;
            color: var(--accent);
        }
        .app-subtitle {
            font-size: 0.72rem;
            color: var(--gold);
            letter-spacing: 0.18em;
            text-transform: uppercase;
            margin: 0 0 0.6rem;
        }
        .progress-track {
            position: relative;
            width: 100%;
            max-width: 26rem;
            margin: 0 auto;
            height: 6px;
            border-radius: 999px;
            background: var(--line);
            overflow: hidden;
        }
        #progress-bar {
            position: absolute;
            inset: 0 auto 0 0;
            width: 0%;
            background: linear-gradient(90deg, var(--accent), var(--gold));
            border-radius: 999px;
            transition: width 0.3s ease;
        }
        #progress-markers {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }
        .marker {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 10px;
            border-radius: 4px;
            background: #cfc7b6;
            transition: background 0.3s ease;
        }
        .marker.active { background: var(--gold); }
        .marker-label {
            position: absolute;
            top: -1.1rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.5rem;
            color: #b6af9f;
            white-space: nowrap;
        }
        .marker-label.active { color: var(--gold); }

        .decena-indicator {
            font-size: 0.72rem;
            color: var(--muted);
            letter-spacing: 0.08em;
            margin-top: 0.5rem;
            min-height: 1em;
        }
        .decena-indicator strong { color: var(--accent); }

        .card-stage {
            flex: 1 1 auto;
            display: grid;
            grid-template-areas: "stack";
            place-items: start center;
            width: 100%;
            max-width: 26rem;
            margin: 0 auto;
            padding: 0 1rem;
            perspective: 1200px;
            position: relative;
            min-height: 0;
        }
        .card {
            grid-area: stack;
            width: 100%;
            max-height: 100%;
            overflow-y: auto;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 10px 30px -12px rgba(44,42,39,0.25);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.75rem 1.5rem 1.5rem;
            scrollbar-width: thin;
            scrollbar-color: #cfc7b6 transparent;
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
            will-change: transform, opacity;
        }
        .card::-webkit-scrollbar { width: 5px; }
        .card::-webkit-scrollbar-thumb { background: #cfc7b6; border-radius: 8px; }
        .card:focus-visible { outline: 2px solid var(--gold); outline-offset: -2px; }
        .card-badge {
            align-self: flex-start;
            font-size: 0.62rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--accent);
            background: var(--accent-soft);
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            margin-bottom: 0.9rem;
        }
        .card-badge[data-type="grano_mayor"],
        .card-badge[data-type="invocacion"],
        .card-badge[data-type="conclusion"] {
            color: #7a5c1f;
            background: var(--gold-soft);
        }
        .card-title {
            font-family: var(--serif);
            font-weight: 600;
            font-size: 1.7rem;
            text-align: center;
            color: var(--ink);
            margin: 0 0 1rem;
            line-height: 1.15;
        }
        .card-body {
            font-family: var(--serif);
            font-size: 1.22rem;
            line-height: 1.6;
            text-align: center;
            color: #403c35;
            margin: 0;
            white-space: normal;
        }
        .card-body br { content: ""; display: block; margin-bottom: 0.5em; }

        .card[data-type="grano_mayor"] .card-title,
        .card[data-type="grano_menor"] .card-title { font-size: 1.25rem; letter-spacing: 0.05em; text-transform: uppercase; }
        .card[data-type="grano_mayor"] .card-title { color: var(--gold); }
        .card[data-type="grano_menor"] .card-body { font-size: 1.15rem; font-style: italic; }

        /* Tarjeta final / gracias */
        .thanks-title { font-family: var(--serif); font-size: 2rem; color: var(--accent); margin: 0 0 0.75rem; }
        .thanks-text { font-family: var(--serif); font-size: 1.2rem; text-align: center; color: #403c35; margin: 0 0 1.5rem; line-height: 1.6; }
        .final-actions { display: flex; flex-direction: column; gap: 0.6rem; width: 100%; max-width: 17rem; }

        .btn {
            font-family: var(--sans);
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            border-radius: 999px;
            padding: 0.7rem 1.2rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: transform 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
        }
        .btn:focus-visible { outline: 3px solid rgba(176,138,62,0.5); outline-offset: 2px; }
        .btn:active { transform: scale(0.97); }
        .btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 6px 16px -6px rgba(31,58,95,0.5);
        }
        .btn-primary:hover { background: #2a4a75; }
        .btn-ghost {
            background: transparent;
            color: var(--accent);
            border: 1.5px solid var(--accent);
        }
        .btn-ghost:hover { background: var(--accent-soft); }
        .btn-gold { background: var(--gold); color: #fff; }
        .btn-gold:hover { background: #9c7834; }

        nav.controls {
            flex: 0 0 auto;
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            align-items: center;
            padding: 0.6rem 1rem calc(0.9rem + env(safe-area-inset-bottom));
            background: linear-gradient(0deg, rgba(247,244,238,0.95) 60%, rgba(247,244,238,0) 100%);
        }
        .nav-btn {
            flex: 1;
            max-width: 9rem;
        }
        .jump-btn {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
        }
        .jump-row {
            display: flex;
            gap: 0.4rem;
            justify-content: center;
            flex-wrap: wrap;
            padding: 0 1rem 0.35rem;
        }
        .jump-row .btn { max-width: none; }

        .live-region {
            position: absolute;
            width: 1px; height: 1px;
            overflow: hidden;
            clip: rect(0 0 0 0);
            clip-path: inset(50%);
            white-space: nowrap;
        }

        .menu-overlay {
            position: fixed;
            inset: 0;
            background: rgba(44,42,39,0.4);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
            padding: 1.5rem;
        }
        .menu-overlay.open { display: flex; }
        .menu-panel {
            background: var(--surface);
            border-radius: 20px;
            box-shadow: 0 20px 60px -20px rgba(0,0,0,0.4);
            padding: 1.5rem;
            width: 100%;
            max-width: 21rem;
        }
        .menu-panel h3 {
            font-family: var(--serif);
            font-size: 1.3rem;
            color: var(--accent);
            margin: 0 0 1rem;
            text-align: center;
        }
        .menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1px solid var(--line);
            padding: 0.7rem 0.2rem;
            font-family: var(--sans);
            font-size: 0.95rem;
            color: var(--ink);
            cursor: pointer;
            text-align: left;
        }
        .menu-item:hover { color: var(--accent); }
        .menu-item span:last-child { color: var(--muted); font-size: 0.8rem; }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; animation: none !important; scroll-behavior: auto !important; }
        }
    </style>
</head>
<body>

    <header class="app-header">
        <h1 class="app-title">Coronilla de la Divina Misericordia</h1>
        <p class="app-subtitle">Rezo guiado</p>
        <div class="progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-label="Progreso de la oración">
            <div id="progress-bar"></div>
            <div id="progress-markers"></div>
        </div>
        <div class="decena-indicator" id="decena-indicator" aria-live="polite"></div>
    </header>

    <div class="jump-row" id="jump-row">
        <button class="btn btn-ghost jump-btn" data-jump="0">Inicio</button>
        <button class="btn btn-ghost jump-btn" data-jump="1">Padre nuestro</button>
        <button class="btn btn-ghost jump-btn" data-jump="4">Decena 1</button>
        <button class="btn btn-ghost jump-btn" data-jump="15">Decena 2</button>
        <button class="btn btn-ghost jump-btn" data-jump="26">Decena 3</button>
        <button class="btn btn-ghost jump-btn" data-jump="37">Decena 4</button>
        <button class="btn btn-ghost jump-btn" data-jump="48">Decena 5</button>
        <button class="btn btn-ghost jump-btn" data-jump="62">Oración final</button>
    </div>

    <main class="card-stage" id="stack">
        <?= $rendered ?>
    </main>

    <nav class="controls" aria-label="Controles de navegación">
        <button id="btn-prev" class="btn btn-ghost nav-btn" aria-label="Tarjeta anterior">← Anterior</button>
        <button id="btn-menu" class="btn btn-ghost nav-btn" aria-label="Ir a un paso concreto">Índice</button>
        <button id="btn-next" class="btn btn-primary nav-btn" aria-label="Tarjeta siguiente">Siguiente →</button>
    </nav>

    <div class="menu-overlay" id="menu-overlay" role="dialog" aria-modal="true" aria-labelledby="menu-title">
        <div class="menu-panel">
            <h3 id="menu-title">Ir a un paso</h3>
            <button class="menu-item" data-jump="0"><span>Inicio</span><span>1</span></button>
            <button class="menu-item" data-jump="1"><span>Padre nuestro</span><span>2</span></button>
            <button class="menu-item" data-jump="3"><span>Credo</span><span>4</span></button>
            <button class="menu-item" data-jump="4"><span>Decena 1</span><span>5</span></button>
            <button class="menu-item" data-jump="15"><span>Decena 2</span><span>16</span></button>
            <button class="menu-item" data-jump="26"><span>Decena 3</span><span>27</span></button>
            <button class="menu-item" data-jump="37"><span>Decena 4</span><span>38</span></button>
            <button class="menu-item" data-jump="48"><span>Decena 5</span><span>49</span></button>
            <button class="menu-item" data-jump="59"><span>Invocaciones</span><span>60</span></button>
            <button class="menu-item" data-jump="62"><span>Oración final</span><span>63</span></button>
            <button id="btn-restart" class="btn btn-gold" style="width:100%;margin-top:0.8rem">Volver a empezar</button>
        </div>
    </div>

    <div class="live-region" id="live-region" aria-live="polite"></div>

    <script src="script.js?v=3"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('sw.js')
                    .catch(function (error) { console.log('Service Worker registration failed:', error); });
            });
        }
    </script>
</body>
</html>
