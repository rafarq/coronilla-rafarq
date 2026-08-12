# Coronilla de la Divina Misericordia

Aplicación web instalable para rezar la Coronilla mediante tarjetas y gestos.

## Estructura

- `index.php`: interfaz y las 64 tarjetas.
- `cards.json`: fuente de datos de las tarjetas.
- `script.js`: navegación por gestos, progreso y contador.
- `counter.php`: endpoint local equivalente al endpoint remoto; persiste el contador en `data/counter.sqlite`.
- `sw.js`: service worker offline.
- `manifest.json`, `icons/`, `icon.png`: instalación PWA.

## Desarrollo

Requisitos: PHP 8.1+ con `pdo_sqlite` y Node.js.

```bash
php -S 127.0.0.1:8765 -t .
```

Abrir <http://127.0.0.1:8765/>.

## Comprobaciones

```bash
php -l index.php
php -l counter.php
node --check script.js
python3 tests/smoke.py
```

El `index.html` descargado inicialmente no forma parte del sitio: era la página 404 de Hostinger devuelta para esa ruta. La aplicación real se sirve en `/` y `/index.php`.

## Bugs corregidos

- Se eliminó del repositorio el falso `index.html` 404.
- Se evitó que varios gestos rápidos se solapen durante la animación y corrompan la pila de tarjetas.
- Se validan las respuestas HTTP/JSON del incremento del contador.
- Se hizo autocontenido el service worker: ya no depende de recursos externos que pueden provocar el fallo de `cache.addAll()`.
- Se añade limpieza de cachés antiguas y activación inmediata del service worker.
- El contador local valida método, payload y errores de persistencia.
