# QA record — 2026-11-25

Environment: WordPress 7.1.5 + WooCommerce 11.3.1 on SQLite (Studio's stack), PHP 8.3 built-in server, Chromium (Playwright 1.x), this container. Screenshots are in `.impeccable/review/` (desktop `home-1440.png` and `home-1920.png`, tablet `home-768.png`, mobile `home-390.png`, first viewports `fv-*.png`, store pages `p-*.png`, journeys `journeys-*.png`, editor `editor-front-page.png`).

## Functional journeys: `node tests/journeys.mjs` (37/37 passed)

Covers:
- homepage → featured product
- explainer → range
- quick add from the homepage, with live bag count and screen-reader announcement
- count persists across reload
- product-page add with quantity
- cart quantity update and removal
- cart → checkout → test order → confirmation, with the bag emptied
- contact form: empty, invalid-email and valid submissions; wholesale enquiry; no-JS fallback
- phone bag visibility; mobile menu open / Escape
- skip link and tab order; visible focus
- reduced motion leaves nothing hidden
- no JS errors

Before re-running, clear the enquiry rate limit (5 per 10 minutes per IP): `wp transient delete --all`.

Also verified:
- **Demo setup idempotency.** A second run updated products and pages in place and created no duplicates (media count unchanged).
- **Clean reproduction.** `bin/setup-local.sh` worked from a clean copy of the repo, and installing the theme and plugin from the built ZIPs rendered every route with status 200.
- **Stored data.** Enquiries were stored as private `kesto_enquiry` posts. Test orders were recorded as "on-hold" with "Test order — no payment taken". Email can't be sent in this container (no sendmail); the Emailed column shows "No" accordingly.
- **Block editor.** The front-page template opens in the Site Editor with 0 invalid and 0 missing blocks and no console errors, and renders close to the front end.

## Visual review against the approved comp

| Area | Result |
|---|---|
| Hero balance, type, crop | Four-line headline left, slanted photo right, mango swash under GREAT, arrow into the CTA, "Pass it round!" note. Phones get two lines (GOOD FOOD. / GREAT MOOD.), then the CTA, then a 4:5 art-directed crop with pouches |
| Product prominence | Three colour tiles (orange / red / cream) with real pouches and food layers. On phones they become a horizontal snap scroller |
| Colour relationships | Cream ground, tomato display type, mango strip and buttons, leaf-green band and closing mango band, all sampled from the comp |
| Rhythm | Section order matches the comp. Plain-language biltong intro added in the green band |
| Arrows | Hero hook, shop loop, story swirls, closing swoop and three swashes. Each is an independent SVG that draws on entry; the CTA arrows nudge on hover and focus |
| Packaging fidelity | Current mango/red labels from the supplied product photo, not the comp's older orange ones |
| Newcomer understanding | "Biltong is seasoned beef that is air-dried and sliced…" appears in the green band, on every biltong product page, at the top of the shop, and on /what-is-biltong/ |
| Phone shopping | The bag stays visible beside the menu button; quick add on tiles; 44px+ targets |

Known deviations:
- Display face is Chau Philomene One (fallback), so letterforms differ from the comp's hand lettering.
- Story headline sits on one line from 1180px and on two lines below that.
- Benefits show a "Concept claims — pending verification" line that the comp does not have, as the brief requires.

## Finish review (Impeccable finish reviewer, independent pass)

Fixed after review:
- Hero pouches enlarged and moved toward her, not tucked in the corner.
- The "Pass it round!" note is anchored above the pouch, so it can't overlap it at any width.
- At 768, the swash moved off "MOOD."
- Story band set to the comp's flat green.
- Flavour swash kept to the width of "PEOPLE.", with an arrow restored on tablet and phone.
- Mobile story arrows moved into the gaps between captions.
- Beef-claims footnote moved into the benefit strip.

Approved by the client for now (2026-11-25): the comp shows a pouch **held in her raised hand**; the build stands the real pouches on the table beside her instead, because a composited grip would look fake and a generated pouch would corrupt the label. A shoot with the real pouch in hand remains the long-term fix.

## Performance: Lighthouse 12.6.1, simulated throttling, this container

| Page | Mobile perf | Desktop perf | A11y | Best practices | SEO | Mobile LCP / CLS |
|---|---|---|---|---|---|---|
| Home | 82 | 98 | 100 | 100 mobile / 96 desktop* | 100 | 4.3 s / 0.001 |
| What's biltong? | 92 | — | 100 | 100 | 100 | 2.9 s / 0.012 |
| Shop | 69 | 98 | 100 | 100 | 100 | 5.1 s / 0.062 |
| Product | 65 | 96 | 100 | 100 | 100 | 6.5 s / 0 |

\*The desktop flag was the hero pouches declaring one shared height. Each pouch now declares its true dimensions; this was fixed after the measurement.

After the first measurement, WooCommerce's classic CSS and jQuery were trimmed from non-store pages, the emoji script dropped, and image `sizes` tightened. Homepage mobile went from 69 to 82.

Biggest remaining factor: **the PHP dev server sends no compression**. Lighthouse estimates 200–400 KB of savings per page from gzip/brotli, which every production host provides. Mobile store pages also load WooCommerce's own block CSS and JS, which is needed for cart and checkout. Re-measure on the real host.
