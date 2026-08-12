#!/usr/bin/env python3
import json
import re
import subprocess
from pathlib import Path
from urllib.request import urlopen

root = Path(__file__).parent.parent

# El HTML de las tarjetas se genera en PHP en tiempo de ejecución;
# por eso validamos la salida servida, no el archivo fuente.
with urlopen('http://127.0.0.1:8765/') as response:
    html = response.read().decode('utf-8')

cards = json.loads((root / 'cards.json').read_text(encoding='utf-8'))
assert len(cards) == 64
assert html.count('class="card"') == len(cards)
assert [int(i) for i in re.findall(r'class="card" data-index="(\d+)"', html)] == list(range(64))
for card in cards:
    assert card['title'] in html
    # El body se sirve con nl2br: los saltos de línea pasan a <br />.
    normalized = card['body'].replace('\n', '<br />\n')
    assert normalized in html, f'body no encontrado: {card["title"]}'
# El HTML debe incluir los controles de navegación y accesibilidad.
for needle in ['btn-next', 'btn-prev', 'btn-menu', 'aria-live', 'data-jump', 'live-region', '100dvh']:
    assert needle in html, f'falta: {needle}'

for command in [['php', '-l', 'index.php'], ['php', '-l', 'counter.php'], ['node', '--check', 'script.js']]:
    subprocess.run(command, cwd=root, check=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)

with urlopen('http://127.0.0.1:8765/counter.php?action=get') as response:
    payload = json.load(response)
assert payload['success'] is True
assert isinstance(payload['count'], int)
print(f'OK: {len(cards)} cards; syntax PHP/JS; counter GET={payload["count"]}')
