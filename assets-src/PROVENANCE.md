# Asset provenance

Every raster that ships in `themes/kesto/assets/images/` is built from a master in this folder by `python3 tools/build-images.py`.

## Supplied by KESTO (authoritative)

| File | What it is | Used for |
|---|---|---|
| `product-lineup-campaign.jpg` | Lineup photo of Original, Trail Mix and Chilli pouches (1080×1084) | Source for the pouch packshots and the logo trace |
| `product-trailmix-pouch.jpg` | Single Trail Mix pouch render (526×789) | Reference only |
| `product-biltong-legendary.jpg` | "Legendary Taste" ad with an earlier Original/Chilli label (orange band, no ™) | Reference only. Not used: it shows older packaging |
| `../.impeccable/reference/comp.png` | Approved full-page homepage concept (1024×1536) | Composition reference only. No pixels from it ship |

## Generated for this build (concept imagery, replace with real photography)

All generated on 2026-11-25 with fal.ai **Nano Banana 2.1 Edit** (`google/nano-banana-2.1/edit`, 2K). The matching crop of the approved comp was the composition reference. Each prompt asked for no packaging and no text; the real pouches are layered on separately in the page.

| Master | Prompt summary | Status |
|---|---|---|
| `generated/hero-master.jpg` | Woman laughing mid-bite at a sunny outdoor table, bowl of biltong, friends behind, gingham at right | Concept: replace with a real campaign shoot |
| `generated/community-master.jpg` | Overhead shared table: biltong bowl, roasted corn & nut bowl, limes, hands reaching in | Concept |
| `generated/story-source-master.jpg` | Cattle grazing on pasture, late light | Concept. **Not** a KESTO supplier or farm |
| `generated/story-craft-master.jpg` | Hands slicing seasoned biltong on a board | Concept. Not KESTO's production |
| `generated/story-company-master.jpg` | Friends passing a bowl of biltong | Concept |
| `generated/food-biltong.webp`, `food-chilli.webp`, `food-trailmix.webp` | Food piles generated on white, then background-removed with `fal-ai/birefnet/v2` | Decorative layers |

## Restored from supplied photography

| Master | Process | Status |
|---|---|---|
| `generated/pouch-original.webp`, `pouch-chilli.webp`, `pouch-trail.webp` (+ `*-restored.webp` on grey) | Each pouch was cut from `product-lineup-campaign.jpg` (`fal-ai/birefnet/v2`). It was then restored at 2K with Nano Banana 2.1 Edit, prompted to reproduce the artwork and wording exactly, removing only the food overlapping the base, and cut out again. Label text was checked by eye against the source: every word and spelling matches. | **Interim.** Replace with studio packshots of the physical pouches. Packshots are also how Chilli Bites gets an image (none supplied, so it shows a "Packshot coming soon" placeholder) |

## Logo

`themes/kesto/assets/images/kesto-logo.svg` and `kesto-mark.svg` were traced with potrace from the horn mark and wordmark in `product-lineup-campaign.jpg`. Ask KESTO for the master vector artwork and swap it in. The file names stay the same and the colour comes from `currentColor`.

## Fonts

See `docs/VERIFICATION.md` → Fonts.
