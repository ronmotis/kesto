# Verification list: assets, font, claims, content

Anything below marked **pending** is shown on the site as concept material. The concept disclaimer stays on (KESTO → Claims & links → Concept mode) until every row is resolved.

## Product claims (KESTO → Claims & links)

| Claim | Applies to | Status | Evidence needed |
|---|---|---|---|
| High protein | Biltong only | pending (provisional) | Nutrition panel per serving that meets the labelling rules for a "high protein" claim in the market you sell in |
| Grass-fed beef | Biltong only | pending | Supplier documentation |
| Gluten-free | Whole range | pending | Ingredient and facility confirmation for **every** product, including Magunanzi and Chilli Bites. If any product fails, set this claim to "Biltong only" or remove it |
| Made in the USA | Whole range | pending | Production location. The packaging only says "Proudly made for Dallas & Beyond" |

"Beef claims apply to biltong" appears under the featured products. Product pages show only the claims that apply to that product's category.

## Products (WooCommerce)

| Product | SKU | Price | Pending |
|---|---|---|---|
| Original Biltong | KESTO-BIL-ORIG-200 | $14.99 **demo** | Real price, ingredients, allergens, nutrition |
| Chilli Biltong | KESTO-BIL-CHIL-200 | $15.99 **demo** | Same, plus heat level |
| Magunanzi Trail Mix | KESTO-MAG-200 | $9.99 **demo** | Same; contains nuts |
| Chilli Bites | KESTO-BIL-BITES | $12.99 **demo** | **Demo catalog entry.** Pack size, packshot and all details |

Pack size NET 200 g is taken from the packaging for the first three. Demo prices carry a "demo" tag on the homepage tiles and a note on product pages, while the "Price is a demo value" box is ticked in each product's KESTO tile panel.

## Imagery

See `assets-src/PROVENANCE.md`. In summary:
- Campaign and story photography is AI-generated concept imagery and must be replaced.
- The "source" photo must not be presented as KESTO's farm.
- Pouches are AI-restored from the supplied photo and should be replaced with studio packshots.

## Fonts

| Role | Face | Source | Licence | Status |
|---|---|---|---|---|
| Display (intended) | **Nando's Hand** | Not supplied | Unresolved. [Unverified] Nando's Hand appears to be a proprietary face associated with the Nando's restaurant brand; if so, a production licence would have to come from its owner. Confirm before use | **pending**: the file was not in the supplied assets |
| Display (temporary fallback) | Chau Philomene One 400 | Google Fonts, self-hosted WOFF2 in `themes/kesto/assets/fonts/` | SIL Open Font License 1.1 | in use |
| Labels, navigation, buttons, body | Barlow 400/500/600/700, Barlow Semi Condensed 600/700 | Google Fonts, self-hosted | SIL OFL 1.1 | in use |

To swap the display face, drop the WOFF2 into `themes/kesto/assets/fonts/` and edit the `display` entry in `themes/kesto/theme.json` (`fontFamily` and `fontFace`). Every headline uses `var(--wp--preset--font-family--display)`, so nothing else changes. Re-check line breaks afterwards: headline widths are set in `ch`.

## Story and brand content (Our roots page)

Placeholders are shown as dashed "To be supplied / To be confirmed" boxes and can be edited in the page editor:
- Founder biography and photo
- Beef supplier / farm relationships (and any grass-fed claim)
- Production location and method

No supplier, farm, founder or historical claim was invented.

## Links and inbox

| Item | Status |
|---|---|
| Instagram / Facebook / TikTok | Not supplied. Hidden until a URL is entered **and** ticked "verified" in KESTO → Claims & links |
| Enquiry inbox | Defaults to the site admin email. Set it under KESTO → Claims & links → Enquiries. Every enquiry is also stored under KESTO → Enquiries |
| Outgoing email | Needs SMTP configured on the host (for example WP Mail SMTP). The "Emailed" column shows whether each message went out |
