#!/usr/bin/env bash
# Despliegue FTPS seguro de Coronilla a Hostinger.
# Las credenciales se leen de ~/.netrc y nunca se guardan en este archivo.
set -euo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

exec python3 - "$@" <<'PY'
from __future__ import annotations

import argparse
import netrc
import os
import sys
from ftplib import FTP_TLS
from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent
FTP_HOST = "iapremium.rafarq.com"
FTP_PORT = 21
REMOTE_DIR = "/domains/coronilla.rafarq.com/public_html"
SKIP_DIRS = {".git", "__pycache__", "tests", "data"}
SKIP_FILES = {".DS_Store", ".gitignore", "README.md", "subir.sh"}
RUNTIME_PREFIXES = ("data/",)


def files_to_upload() -> list[Path]:
    result = []
    for path in sorted(BASE_DIR.rglob("*")):
        if not path.is_file():
            continue
        relative = path.relative_to(BASE_DIR)
        if any(part in SKIP_DIRS for part in relative.parts):
            continue
        if relative.name in SKIP_FILES or relative.as_posix().startswith(RUNTIME_PREFIXES):
            continue
        result.append(relative)
    if not result:
        raise RuntimeError("No se han encontrado ficheros para desplegar")
    return result


def credentials() -> tuple[str, str]:
    auth = netrc.netrc(os.path.expanduser("~/.netrc")).authenticators(FTP_HOST)
    if not auth or not auth[0] or not auth[2]:
        raise RuntimeError(f"No hay credenciales para {FTP_HOST} en ~/.netrc")
    return auth[0], auth[2]


def remote_file(relative: Path) -> str:
    return f"{REMOTE_DIR}/{relative.as_posix()}"


def ensure_remote_dirs(ftps: FTP_TLS, relative: Path) -> None:
    current = REMOTE_DIR
    for part in relative.parent.parts:
        if part == ".":
            continue
        current += "/" + part
        try:
            ftps.mkd(current)
        except Exception:
            pass  # El directorio ya existe.


def upload_one(ftps: FTP_TLS, relative: Path) -> int:
    local = BASE_DIR / relative
    destination = remote_file(relative)
    ensure_remote_dirs(ftps, relative)
    with local.open("rb") as stream:
        ftps.storbinary(f"STOR {destination}", stream)
    remote_size = ftps.size(destination)
    local_size = local.stat().st_size
    if remote_size != local_size:
        raise RuntimeError(
            f"Tamaño incorrecto en {relative}: local={local_size}, remoto={remote_size}"
        )
    return remote_size


def main() -> int:
    parser = argparse.ArgumentParser(description="Despliega Coronilla por FTPS")
    parser.add_argument("--dry-run", action="store_true", help="lista sin subir")
    args = parser.parse_args()

    files = files_to_upload()
    print(f"Destino: {FTP_HOST}{REMOTE_DIR}")
    print(f"Ficheros: {len(files)}")
    for relative in files:
        print(f"  {relative}")

    if args.dry_run:
        print("DRY-RUN: no se ha realizado ninguna subida")
        return 0

    login, password = credentials()
    ftps = FTP_TLS(timeout=30)
    try:
        ftps.connect(FTP_HOST, FTP_PORT)
        ftps.login(login, password)
        ftps.prot_p()
        for relative in files:
            size = upload_one(ftps, relative)
            print(f"SUBIDO {relative} ({size} bytes)")
    finally:
        try:
            ftps.quit()
        except Exception:
            ftps.close()

    print("OK: despliegue completado; tamaños remotos verificados")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
PY
