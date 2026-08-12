#!/usr/bin/env python3
import json
import re
import subprocess
from pathlib import Path
from urllib.request import urlopen

root = Path(__file__).parent.parent
html = (root / 'index.php').read_text(encoding='utf-8')
cards = json.loads((root / 'cards.json').read_text(encoding='utf-8'))
assert len(cards) == 64
assert html.count('class="card"') == len(cards)
assert [int(i) for i in re.findall(r'class="card" data-index="(\d+)"', html)] == list(range(64))
for card in cards:
    assert card['title'] in html
    assert card['body'] in html

for command in [['php', '-l', 'index.php'], ['php', '-l', 'counter.php'], ['node', '--check', 'script.js']]:
    subprocess.run(command, cwd=root, check=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)

with urlopen('http://127.0.0.1:8765/counter.php?action=get') as response:
    payload = json.load(response)
assert payload['success'] is True
assert isinstance(payload['count'], int)
print(f'OK: {len(cards)} cards; syntax PHP/JS; counter GET={payload["count"]}')
