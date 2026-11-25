#!/usr/bin/env python3
"""Build responsive, art-directed WebP/JPEG sets for the KESTO theme.

Masters live in assets-src/generated (see assets-src/PROVENANCE.md).
Output goes to themes/kesto/assets/images and is committed so the theme ZIP
ships compiled assets. Re-run after replacing any master:

    python3 tools/build-images.py
"""
from pathlib import Path
from PIL import Image

ROOT = Path(__file__).resolve().parent.parent
SRC = ROOT / "assets-src" / "generated"
OUT = ROOT / "themes" / "kesto" / "assets" / "images"
OUT.mkdir(parents=True, exist_ok=True)


def crop_focus(im, ratio, fx, fy):
    """Crop im to aspect `ratio` (w/h) keeping focal point (fx, fy in 0..1)."""
    w, h = im.size
    if w / h > ratio:
        nw = round(h * ratio)
        x = min(max(round(fx * w - nw / 2), 0), w - nw)
        return im.crop((x, 0, x + nw, h))
    nh = round(w / ratio)
    y = min(max(round(fy * h - nh / 2), 0), h - nh)
    return im.crop((0, y, w, y + nh))


def emit(im, name, widths, quality=78, alpha=False):
    for w in widths:
        if w > im.width:
            continue
        r = im.resize((w, round(im.height * w / im.width)), Image.LANCZOS)
        r.save(OUT / f"{name}-{w}.webp", quality=quality, method=6)
        if not alpha and w == widths[len(widths) // 2]:
            r.convert("RGB").save(OUT / f"{name}-{w}.jpg", quality=80, optimize=True, progressive=True)
    print(name, im.size, widths)


def photo(src, name, ratio, focus, widths):
    im = Image.open(SRC / src).convert("RGB")
    emit(crop_focus(im, ratio, *focus), name, widths)


# Hero: desktop wide (woman left-centre, table/gingham right), mobile 4:5 on woman + bowl.
photo("hero-master.jpg", "hero-desktop", 16 / 9, (0.42, 0.5), [960, 1440, 1920, 2560])
photo("hero-master.jpg", "hero-mobile", 4 / 5, (0.36, 0.5), [480, 800, 1200])

# Community overhead: wide desktop, mobile square crop on bowls + hands.
photo("community-master.jpg", "community-desktop", 2.35, (0.5, 0.5), [960, 1600, 2400])
photo("community-master.jpg", "community-mobile", 1.0, (0.66, 0.5), [480, 800, 1200])

# Story stages.
for key, focus in {"source": (0.4, 0.55), "craft": (0.55, 0.5), "company": (0.55, 0.5)}.items():
    photo(f"story-{key}-master.jpg", f"story-{key}", 4 / 3, focus, [480, 800, 1200])

# Pouches (transparent, AI-restored from the supplied product photograph).
for key in ("original", "trail", "chilli"):
    im = Image.open(SRC / f"pouch-{key}.webp").convert("RGBA")
    emit(im, f"pouch-{key}", [240, 400, 640, 960], quality=86, alpha=True)

# Food layers (transparent).
for key in ("biltong", "chilli", "trailmix"):
    im = Image.open(SRC / f"food-{key}.webp").convert("RGBA")
    emit(im, f"food-{key}", [320, 560], quality=84, alpha=True)

# Social/OG image.
og = crop_focus(Image.open(SRC / "hero-master.jpg").convert("RGB"), 1200 / 630, 0.5, 0.45).resize((1200, 630), Image.LANCZOS)
og.save(OUT / "og-kesto.jpg", quality=82, optimize=True)
print("done ->", OUT)
