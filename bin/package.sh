#!/usr/bin/env bash
# Build installable ZIPs: dist/kesto-theme-<ver>.zip and dist/kesto-core-<ver>.zip.
# Compiled assets (images, fonts, CSS, JS) are committed, so no build step is needed;
# run `python3 tools/build-images.py` first only if you replaced an image master.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
VER=$(grep -m1 '^Version:' "$ROOT/themes/kesto/style.css" | awk '{print $2}')
PVER=$(grep -m1 'Version:' "$ROOT/plugins/kesto-core/kesto-core.php" | awk '{print $3}')
mkdir -p "$ROOT/dist"
rm -f "$ROOT/dist/kesto-theme-$VER.zip" "$ROOT/dist/kesto-core-$PVER.zip"
( cd "$ROOT/themes" && zip -qr "$ROOT/dist/kesto-theme-$VER.zip" kesto -x '*.DS_Store' )
( cd "$ROOT/plugins" && zip -qr "$ROOT/dist/kesto-core-$PVER.zip" kesto-core -x '*.DS_Store' )
ls -lh "$ROOT/dist"
