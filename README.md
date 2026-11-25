# KESTO — WordPress storefront

Custom WooCommerce block theme and companion plugin for **KESTO™**, Southern African-style snacks: Original Biltong, Chilli Biltong, Chilli Bites and Magunanzi Trail Mix. Built from the approved homepage concept (`.impeccable/reference/comp.png`).

```
themes/kesto/          Block theme: theme.json, templates, parts, patterns, blocks, compiled assets
plugins/kesto-core/    Claims & links settings, product tile fields, enquiries, demo setup (wp kesto setup)
assets-src/            Supplied photos + image masters (see PROVENANCE.md)
tools/build-images.py  Rebuilds responsive images into the theme
bin/setup-local.sh     Reproduces the local site (WordPress + SQLite, Studio's stack) and serves it
bin/package.sh         Builds dist/ ZIPs for the theme and plugin
tests/journeys.mjs     End-to-end shopper journeys (Playwright)
docs/                  DEPLOYMENT.md, VERIFICATION.md (pending facts), QA.md (checks + results)
```

## Quick start

```bash
bin/setup-local.sh serve
```

Open http://localhost:8881 (admin / `kesto-local`, local only), then run the journey tests in another terminal:

```bash
node tests/journeys.mjs
```

## Editing

| What | Where |
|---|---|
| Homepage sections | Appearance → Editor → Templates → Front Page (patterns `kesto/home-*`) |
| Featured products | WooCommerce → Products: tick **Featured**; set the **KESTO tile** box (colour, food layer, descriptor, demo-price flag); order with Menu order |
| Benefit claims, verification status, concept disclaimer, social links, enquiry inbox | KESTO → Claims & links |
| Enquiries | KESTO → Enquiries |
| Story pages | Pages → What's biltong? / Our roots / Contact / Wholesale |
| Display font | `themes/kesto/theme.json` → `fontFamilies` → `display` |

## Status

This is a **concept preview**: prices are demo values, benefit claims are provisional, campaign photography is AI-generated concept imagery, and the display face is a temporary fallback. All of it is labelled on the site and listed in `docs/VERIFICATION.md`.
