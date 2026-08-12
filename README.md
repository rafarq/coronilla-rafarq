# Coronilla de la Divina Misericordia

Aplicación web instalable (PWA) para rezar la Coronilla mediante tarjetas, gestos, botones y teclado.

## Estructura

- `index.php`: interfaz; genera las 64 tarjetas desde `cards.json`.
- `cards.json`: fuente única de datos de las tarjetas.
- `script.js`: navegación (gestos, botones, teclado), progreso, persistencia y contador.
- `counter.php`: endpoint del contador; persiste en `data/counter.sqlite`.
- `sw.js`: service worker offline (red primero para navegación, caché para el resto).
- `manifest.json`, `icons/`, `icon.png`: instalación PWA.

## Funcionalidades

- 64 tarjetas con gestos (swipe), botones `Anterior`/`Siguiente`, índice por pasos y teclado (← →, Inicio, Fin).
- Distinción entre desplazamiento vertical (lectura de tarjeta larga) y swipe horizontal (avanzar/retroceder).
- Progreso guardado en `localStorage` para continuar donde se dejó.
- Indicador de decena (`Decena 3 de 5`).
- Accesibilidad: `aria-live`, foco visible, `role` adecuado y `prefers-reduced-motion`.
- Pantalla final con contador de personas, reinicio, instalación PWA y compartir.
- Diseño responsive con `100dvh` y `safe-area-inset`.

## Desarrollo

Requisitos: PHP 8.1+ con `pdo_sqlite` y Node.js.

```bash
php -S 127.0.0.1:8765 -t .
```

Abrir <http://127.0.0.1:8765/>.

## Despliegue

Las credenciales FTPS se leen desde `~/.netrc`. El script no sube `.git`,
`tests`, `data/`, `README.md`, `.gitignore` ni el propio script.

```bash
./subir.sh --dry-run
./subir.sh
```

Destino configurado: `/domains/coronilla.rafarq.com/public_html/`.
El archivo remoto `count.txt` no se sobrescribe, por lo que se conserva el
contador de producción.

## Comprobaciones

```bash
php -l index.php
php -l counter.php
node --check script.js
python3 tests/smoke.py
```

El `index.html` descargado inicialmente no forma parte del sitio: era la página 404 de Hostinger. La aplicación real se sirve en `/` y `/index.php`.

## Bugs corregidos (importación)

- Se eliminó el falso `index.html` 404 del mirror.
- Se evitó que varios gestos rápidos se solaparan durante la animación y corrompieran la pila.
- Se validan las respuestas HTTP/JSON del contador.
- Service worker autocontenido (sin recursos externos que rompían `cache.addAll()`), con limpieza de cachés antiguas, `skipWaiting()` y `clients.claim()`.
- El contador local valida método, payload y errores de persistencia.
- Las tarjetas se generan desde `cards.json` (fuente única), eliminando la duplicación HTML/JSON.

## Mejoras aplicadas

- Estética contemplativa: fondo marfil, azul profundo y dorado, serif para la oración.
- Jerarquía visual por tipo de tarjeta (Grano mayor/menor, Invocación, etc.).
- Botones de navegación visibles y menú de índice por pasos.
- Persistencia del progreso.
- Pantalla final con contador, reinicio, instalar y compartir.
- PWA completa con estrategia de caché versionada.
- Responsive (`100dvh`).
- Privacidad: sin analítica externa; el contador es una métrica agregada documentada.
