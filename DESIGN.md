---
name: KESTO
description: Southern African biltong storefront set at a sunny shared table. Cream ground, huge tomato type, mango action.
colors:
  tomato: "#A3120C"
  tomato-deep: "#7E0D08"
  mango: "#F5A511"
  mango-bright: "#FDB41D"
  story-green: "#2B4D07"
  leaf: "#2B4F0E"
  leaf-deep: "#1F3A09"
  cream: "#FBEBD6"
  cream-deep: "#F4DCBB"
  white: "#FFFDF9"
  ink: "#2A1710"
  ink-muted: "#6B4A3A"
  field-border: "#C9A27F"
  story-text: "#F6E5CC"
  story-text-muted: "#E9D8BD"
  tile-orange: "#F68700"
  tile-red: "#BD1C17"
  tile-cream: "#F6D7AE"
typography:
  display:
    fontFamily: "\"Chau Philomene One\", \"Barlow Semi Condensed\", sans-serif"
    fontSize: "clamp(3.4rem, 8.4vw, 9.25rem)"
    fontWeight: 400
    lineHeight: 0.9
    letterSpacing: "0em"
  headline:
    fontFamily: "\"Chau Philomene One\", \"Barlow Semi Condensed\", sans-serif"
    fontSize: "clamp(2.6rem, 5.1vw, 6.9rem)"
    fontWeight: 400
    lineHeight: 0.9
    letterSpacing: "0em"
  title:
    fontFamily: "\"Barlow Semi Condensed\", Barlow, sans-serif"
    fontSize: "clamp(1.15rem, 0.95rem + 0.6vw, 1.5rem)"
    fontWeight: 700
    lineHeight: 1.15
  body:
    fontFamily: "Barlow, \"Helvetica Neue\", Arial, sans-serif"
    fontSize: "1.0625rem"
    fontWeight: 400
    lineHeight: 1.55
  lede:
    fontFamily: "Barlow, \"Helvetica Neue\", Arial, sans-serif"
    fontSize: "clamp(1.05rem, 0.8rem + 0.6vw, 1.35rem)"
    fontWeight: 600
    lineHeight: 1.3
  label:
    fontFamily: "\"Barlow Semi Condensed\", Barlow, sans-serif"
    fontSize: "clamp(1.1rem, 0.9rem + 0.5vw, 1.3rem)"
    fontWeight: 700
    lineHeight: 1
  label-caps:
    fontFamily: "\"Barlow Semi Condensed\", Barlow, sans-serif"
    fontSize: "clamp(1rem, 0.75rem + 0.85vw, 1.55rem)"
    fontWeight: 700
    lineHeight: 1.05
    letterSpacing: "0.01em"
  fine:
    fontFamily: "Barlow, \"Helvetica Neue\", Arial, sans-serif"
    fontSize: "0.8125rem"
    fontWeight: 600
    lineHeight: 1.35
rounded:
  chip: "4px"
  control: "8px"
  frame: "10px"
  tile: "12px"
  pill: "999px"
spacing:
  gutter: "clamp(1rem, 4vw, 2.5rem)"
  "10": "0.5rem"
  "20": "clamp(0.75rem, 0.6rem + 0.5vw, 1rem)"
  "30": "clamp(1rem, 0.8rem + 0.8vw, 1.5rem)"
  "40": "clamp(1.5rem, 1.1rem + 1.6vw, 2.5rem)"
  "50": "clamp(2rem, 1.3rem + 3vw, 4rem)"
  "60": "clamp(3rem, 2rem + 4.5vw, 6rem)"
  max: "1440px"
components:
  button-mango:
    backgroundColor: "{colors.mango}"
    textColor: "{colors.tomato-deep}"
    typography: "{typography.label}"
    rounded: "{rounded.control}"
    padding: "0.7em 1.15em"
    height: "48px"
  button-mango-hover:
    backgroundColor: "{colors.mango-bright}"
    textColor: "{colors.tomato-deep}"
  button-tomato:
    backgroundColor: "{colors.tomato}"
    textColor: "{colors.cream}"
    typography: "{typography.label}"
    rounded: "{rounded.control}"
    padding: "0.7em 1.15em"
    height: "48px"
  button-tomato-hover:
    backgroundColor: "{colors.tomato-deep}"
    textColor: "{colors.cream}"
  button-cream:
    backgroundColor: "{colors.cream}"
    textColor: "{colors.leaf-deep}"
    typography: "{typography.label}"
    rounded: "{rounded.control}"
    padding: "0.7em 1.15em"
    height: "48px"
  button-cream-hover:
    backgroundColor: "{colors.white}"
    textColor: "{colors.leaf-deep}"
  button-text:
    textColor: "{colors.tomato}"
    typography: "{typography.label}"
    padding: "0.7em 0.4em"
  button-add:
    textColor: "{colors.tomato}"
    rounded: "{rounded.control}"
    padding: "0.5em 0.85em"
    height: "44px"
  button-add-hover:
    backgroundColor: "{colors.tomato}"
    textColor: "{colors.cream}"
  button-add-added:
    backgroundColor: "{colors.leaf}"
    textColor: "{colors.cream}"
  nav-link:
    textColor: "{colors.tomato}"
    typography: "{typography.label}"
    height: "44px"
  benefit-strip:
    backgroundColor: "{colors.mango}"
    textColor: "{colors.ink}"
    typography: "{typography.label-caps}"
  product-tile:
    backgroundColor: "{colors.tile-orange}"
    rounded: "{rounded.tile}"
  story-band:
    backgroundColor: "{colors.story-green}"
    textColor: "{colors.cream}"
  story-photo-frame:
    backgroundColor: "{colors.cream}"
    rounded: "{rounded.frame}"
    padding: "6px"
  claim-chip:
    backgroundColor: "{colors.mango}"
    textColor: "{colors.ink}"
    rounded: "{rounded.pill}"
    padding: "0.4rem 0.75rem"
  demo-tag:
    backgroundColor: "{colors.cream-deep}"
    textColor: "{colors.tomato-deep}"
    rounded: "{rounded.chip}"
    padding: "0.1rem 0.35rem"
  input:
    backgroundColor: "{colors.white}"
    textColor: "{colors.ink}"
    rounded: "{rounded.control}"
    padding: "0.7rem 0.85rem"
    height: "48px"
---

# Design System: KESTO

## Overview

**Creative North Star: "The Sunny Shared Table"**

KESTO sells Southern African biltong to people who have mostly never tasted it, so the system refuses the category default of dark, macho protein packaging walls. The page is a warm afternoon outdoors: a cream ground, huge deep-tomato display type, mango for every action, one flat leaf-green band for the story of where the food comes from, and real photography of people laughing over a bowl. The black resealable pouches are the only dark objects on the page, so they pop. Every pouch shown is the real packaging, cut out and layered, never redrawn.

Density is low and the rhythm is poster-like: each home band is one idea, set in display type that fills its column, with a hand-drawn tomato or cream arrow pointing at the next thing to do. Labels and controls are quiet Barlow Semi Condensed; the noise belongs to the headlines and the photos. The approved full-page comp (`.impeccable/reference/comp.png`) is the visual authority; where the build and the comp differ, this file records the build.

The system is in concept mode. The display face is a temporary stand-in, prices are demo values, and benefit claims are provisional; the visual system carries the labels that say so rather than hiding them.

**Key Characteristics:**
- Cream ground, tomato display type, mango action, one flat green band.
- Display type uppercase, tight (0.9 line-height), filling its column.
- Hand-drawn SVG arrows and underline swashes as the signature device; they draw once on entry.
- Real pouch cut-outs layered over photography with soft, warm drop shadows.
- Slanted photo edges (clip-path) that the headline overlaps.
- Soft ease-out motion; fully static under reduced motion.

## Colors

A sun-warmed food palette: one hot red, one ripe yellow, one leaf green, on cream, with a warm brown-black ink instead of black.

### Primary
- **Deep Tomato** (tomato): every display headline, nav links, text links, focus rings, rules and borders (footer top rule, footer nav divider, product-notes rule), the outline add-to-bag button, and the secondary CTA fill. It is the brand's voice colour.
- **Burnt Tomato** (tomato-deep): hover and pressed state of tomato; also the text colour on mango buttons and the benefit strip's footnote.

### Secondary
- **Mango** (mango): the action colour. Primary buttons ("Find my snack", add to cart, place order), the benefit strip, claim chips, selection highlight, and the headline underline swash.
- **Bright Mango** (mango-bright): mango hover; base of the community band, which carries a warm radial bloom from top-left (`#FFC43D` through bright mango to mango); highlight text and focus ring inside the story band.

### Tertiary
- **Story Green** (story-green): the flat colour of the "From the farm" story band, sampled directly from the approved comp. It is a flat fill with no gradient or texture.
- **Leaf** (leaf) and **Deep Leaf** (leaf-deep): state and text greens. Leaf fills the "added to bag" state and borders success notices; deep leaf is the text colour on cream buttons and on success messages.

### Neutral
- **Cream** (cream): the page ground, header, footer, shop band, and the label colour on dark fills.
- **Deep Cream** (cream-deep): empty-image and placeholder backgrounds, the demo price tag, cart thumbnails.
- **Warm White** (white): input fields, fact cards, pending-content panels; never the page ground.
- **Ink** (ink): body copy, product names, benefit labels. A brown-black, never pure black.
- **Muted Ink** (ink-muted): hints, legal line, secondary metadata.
- **Field Line** (field-border): the 2px border of every input, quantity selector and select.
- **Story Text** (story-text) and **Story Text Muted** (story-text-muted): cream tints for intro and stage captions on the green band.

### Product tile colours
Each product tile takes a flavour colour, paired with a lighter "sun pool" highlight radiating behind the pouch: Original on **tile orange** (highlight `#FFB347`), Chilli on **tile red** (highlight `#EC4A34`), Trail Mix on **tile cream** (highlight `#FFF1DC`). The same tile + highlight pair is reused on the shop grid, related products and the product-page gallery.

### Named Rules
**The One Dark Object Rule.** Nothing on the page is darker than the pouches except ink text. No black sections, no dark hero, no dark cards.

**The Mango Means Go Rule.** Mango fills belong to the next action (buy, find, check out) and to the benefit strip. Secondary actions are tomato, cream or outlined.

**The Flat Band Rule.** The story band is flat story-green, not a gradient and not the leaf token. Leaf is a state colour.

## Typography

**Display Font:** Chau Philomene One (with Barlow Semi Condensed, sans-serif). This is a **temporary stand-in for Nando's Hand**, the intended brand face, pending the font file and licence. When Nando's Hand arrives it replaces the display family only; sizes and the uppercase setting stay until re-measured.
**Body Font:** Barlow (with Helvetica Neue, Arial)
**Label Font:** Barlow Semi Condensed, 600 and 700

**Character:** A loud, hand-lettered poster face over a plain, friendly grotesque. The display shouts warmly; everything else stays out of its way.

### Hierarchy
- **Display** (400, clamp(3.4rem, 8.4vw, 9.25rem), 0.9): the hero headline only, four short lines, tomato, uppercase, capped near 5.2ch so it stacks.
- **Headline** (400, about clamp(2.6rem, 5vw, 6.9rem) per band, 0.9): section headlines ("Find your flavour", "From the farm", "Sharing is optional"), page h1s (clamp(3rem, 7–8vw, 5.5–6.5rem)). Uppercase, tomato on light grounds, cream on the green band. The mobile menu also sets links in this face.
- **Title** (Barlow Semi Condensed 700, clamp(1.15rem, 0.95rem + 0.6vw, 1.5rem), 1.15): product names, stage labels, checkout step titles, h4. Sentence case, ink or cream.
- **Body** (Barlow 400, 1.0625rem fluid 1 to 1.125rem, 1.55): running copy, max 68ch on content pages, 56ch for intros.
- **Lede** (Barlow 600, clamp(1.05rem, 0.8rem + 0.6vw, 1.35rem), 1.3): hero support lines, 32ch; page ledes run 500 weight at up to 1.55rem.
- **Label** (Barlow Semi Condensed 700, about 1.1 to 1.4rem, 1): nav, bag link, buttons, footer links, field labels.
- **Label caps** (Barlow Semi Condensed 700, clamp(1rem, 0.75rem + 0.85vw, 1.55rem), 0.01em, uppercase): benefit strip items only.
- **Fine** (Barlow 600, 0.8125 to 0.9rem): disclaimers, demo-price notes, concept note, legal.

### Named Rules
**The Poster Stack Rule.** Display and headline text is uppercase, 0.9 line-height, balanced, and constrained by a ch max-width so it breaks into short stacked lines.

**The Quiet Labels Rule.** The display face is for headlines and the hand note only. Buttons, nav, prices and labels are always Barlow Semi Condensed.

## Layout

Full-bleed horizontal bands, each with an inner container of 1440px max and a fluid gutter (clamp(1rem, 4vw, 2.5rem)). Copy columns align to the container's left edge even when the band's photo bleeds to the viewport edge.

- **Hero:** 31/69 split, copy left, photo right with a slanted left edge; the headline overlaps the slant by 6vw. Height clamp(520px, 44vw, 720px). The mango benefit strip closes the first viewport on desktop.
- **Shop band:** 27/73 split, headline left with a curling arrow, three product tiles in a row. Below 1100px the headline stacks above; below 600px tiles become a horizontal snap scroller at 72% width (one and a half tiles visible).
- **Story band:** centred headline and intro, three framed photos joined by swirl arrows (5-column grid with 40–80px arrow columns). Below 600px photos stack and arrows rotate to point down.
- **Community band:** 34/66 split, copy left, photo right with a slanted edge, two pouches layered at the edges.
- **Footer:** logo, nav and concept note in a row, separated by 2px tomato rules; stacks below 782px.
- **Breakpoints:** 600px (phone), 782px (WordPress tablet), 1024px, 1100px, 1700px.
- **Spacing:** band padding clamp(2.5rem, 5vw, 4.75rem); theme.json step scale 10 to 60 for block spacing; block gap 1.25rem.

**The Thumb Reach Rule.** The bag link stays visible beside the menu button on phones, and every touch target is at least 44px (buttons 48px, primary checkout 52px).

## Elevation & Depth

Depth comes from layering real objects, not from raised UI. Surfaces are flat; pouches, food and framed photos cast soft, warm, brown-tinted drop shadows as if lying on the table in sun. Buttons carry a soft tinted underglow. No hard offset shadows, no neutral grey shadows.

### Shadow Vocabulary
- **Pouch** (`filter: drop-shadow(0 18px 18px rgba(40, 10, 0, 0.35))`): layered pouches in hero and community; 16px on tiles, 22px on the product page.
- **Food** (`filter: drop-shadow(0 8px 10px rgba(40, 10, 0, 0.3))`): food cut-outs beside a pouch.
- **Button glow** (`box-shadow: 0 10px 22px -14px rgba(90, 20, 4, 0.55)`): filled buttons only; outline and text buttons have none.
- **Photo frame** (`box-shadow: 0 18px 32px -18px rgba(0, 0, 0, 0.55)`): story photos on the green band.
- **Hand note** (`text-shadow: 0 2px 14px rgba(60, 15, 0, 0.55)`): the white "Pass it round!" note over photography, for legibility.

**The Objects Cast Shadows Rule.** Only things that would sit on the table (pouches, food, printed photos) and filled buttons cast shadows. Containers, cards and bands are flat.

## Shapes

Gently rounded controls (8px), softer tiles (12px), and photo prints with a 6px cream mat and 10px corners, set slightly askew (-1.2deg, 0.8deg, -0.6deg). Claim chips are full pills; the demo tag is a small 4px label. Photography gets one strong geometric gesture: a slanted clip-path edge (hero cuts 13% at the bottom-left; community cuts 7% at the top-left), dropped to a straight edge on phones. Pouches sit rotated a few degrees (-7 to 8deg). Hand-drawn arrows and underline swashes use round caps and joins at 3.4px (arrows) or 5.5px (swashes) stroke.

## Components

### Buttons
Tactile and sunny, never pill-shaped.
- **Shape:** gently rounded (control radius), min height 48px, label type, an arrow glyph (inline SVG mask, up-right) after the text on arrow variants.
- **Mango (primary):** the main action on light grounds.
- **Tomato:** the CTA on the mango community band, and form submit.
- **Cream:** the CTA on the green story band; home link on 404.
- **Text:** tomato underlined label (2px), no fill, no shadow; the secondary link beside a filled button.
- **Hover / Focus:** fill steps to its deeper or brighter partner; the inline arrow nudges 2px up-right; press drops 1px. Focus is a 3px tomato outline at 3px offset (bright mango on the green band).

### Add to bag
Outlined tomato button (2px border, 44px), filling tomato on hover, turning leaf green with cream text once added and burnt tomato on error. Sits beside a "Shop" text link with an up-right arrow. The bag count in the header bumps (scale 1.35) when an item is added.

### Navigation
Barlow Semi Condensed 700 tomato links with no underline at rest; a 2px underline grows from the left on hover, focus and the current page. On phones the core overlay sets links in the display face at clamp(2.6rem, 11vw, 3.4rem). Logo: the red horn mark and wordmark, cream variant on dark grounds.

### Benefit strip
A mango band of four uppercase label-caps claims, each with a stroked inline SVG icon, divided by 2px burnt-tomato rules at 35% opacity; 2x2 on phones. Biltong-only claims carry an asterisk. A fine right-aligned status line explains the asterisk and, while concept mode is on, reads "Concept claims — pending verification".

### Product tile
A 0.9-ratio tile in its flavour colour with a radial sun pool behind the real pouch cut-out and a food cut-out beside it, 12px corners. The whole tile is clickable. Name in title type, short descriptor and price below, with a small DEMO tag after demo prices. Hover or focus lifts the pouch 6px with a -1.5deg tilt and slides the food 4px.

### Story band
Flat story-green band: cream headline, story-text intro with bright mango highlights, three cream-matted photos joined by cream swirl arrows, cream-label captions, and a cream arrow button.

### Inputs
Warm white fill, 2px field-line border, 8px radius, 48px min height, label type labels with a tomato required mark. Focus turns the border tomato with a 3px 25% tomato ring. Invalid: tomato border on a pale blush (`#FFF4EF`) fill with a tomato message. Status panels: pale green with deep leaf text for success, pale blush with burnt tomato for error.

### Claim chips and concept note
On the product page, claims sit as mango pills with icons under a 2px tomato rule, followed by a fine burnt-tomato line ("Demo price. Claims are pending verification.") while concept mode is on. In the footer, the concept note is fine ink text behind a 2px tomato left rule, stating that product claims, sourcing, imagery, prices and the font are pending verification. Both disappear when concept mode is switched off; claims marked verified drop out of the pending notice.

### Hand-drawn arrows (signature)
Seven SVG stroke variants (hook, loop, swirl, swoop, curve-down, and two underline swashes) in tomato, cream, mango or ink. Each stroke is a separate path with normalised length, so it draws in sequence. They point from copy to the action they introduce and never carry meaning on their own (always `aria-hidden`).

### Motion
Short ease-out (`cubic-bezier(0.16, 1, 0.3, 1)`) and only on entry or direct interaction:
- **Arrows draw once on entry:** each stroke draws over 0.9s as it scrolls into view, strokes staggered 0.45s, never replayed.
- **CTA arrow nudge:** on hover or keyboard focus of the hero and community CTAs, the arrow beside the button moves about 7px toward it.
- **Hero mask reveal:** the hero photo reveals once along its slanted edge (1.1s; a straight wipe on phones), then pouches rise (0.55s and 0.7s delays) and the hand note fades in; headline, lede and CTA rise in sequence.
- **Staggered story photos:** the three stages fade up 28px as the band arrives, 0.14s apart. Product tiles settle in the same way, 0.08s apart.
- **Depth on pouch layers:** layered pouches drift a few pixels against the scroll (factors 14 and 22), driven by a passive, frame-throttled listener.
- **Reduced motion:** everything is static. Start states are applied only when motion is allowed and IntersectionObserver exists, content is fully visible by default, and a 4s safety net reveals anything not yet observed.

## Do's and Don'ts

### Do:
- **Do** set every section headline in the display face, uppercase, tomato on light grounds and cream on the green band, at 0.9 line-height.
- **Do** use mango with burnt-tomato text for the main action on any light ground; use cream on the story band and tomato on the mango band.
- **Do** show packaging only as the real pouch cut-outs, layered with the warm pouch shadow and a few degrees of rotation.
- **Do** pair a new product tile with its flavour tile colour and sun-pool highlight.
- **Do** point a hand-drawn arrow from copy to its action, and let it draw once.
- **Do** label provisional claims and demo prices visibly (status line, DEMO tag, concept note) until they are verified.
- **Do** keep every motion behind the reduced-motion and motion-class gates, with content visible by default.

### Don't:
- **Don't** introduce dark sections, black cards or a dark hero; the pouches are the only dark objects.
- **Don't** fill the story band with the leaf token or a gradient; it is flat story green.
- **Don't** set buttons, nav, prices or labels in the display face.
- **Don't** use pure black or grey shadows; shadows are warm brown and soft, and only objects and filled buttons cast them.
- **Don't** show a benefit claim without its pending-verification note while concept mode is on.
- **Don't** replay entrance animations or add motion that runs without user scroll or interaction.
