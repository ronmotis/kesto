# Deploying KESTO

The hosting destination is **not decided yet**. This guide covers:
1. Reproducing the site locally, either with this repo's scripts or in WordPress Studio.
2. Producing a reviewable preview.
3. Promoting to production on any WordPress host.

## What ships

| Piece | Where | Notes |
|---|---|---|
| Theme | `dist/kesto-theme-1.1.0.zip` (`bin/package.sh`) | Block theme with all compiled assets: images, fonts, CSS, JS. No build step on the server |
| Plugin | `dist/kesto-core-1.0.0.zip` | Claims settings, product tile fields, enquiries, demo setup. Declares `Requires Plugins: woocommerce` |
| WooCommerce | from wordpress.org | Built and tested with 11.3.1 |
| WordPress | 7.1.5 tested | `Requires at least: 6.6`, PHP 8.1+ |
| Content | Created by `wp kesto setup` / KESTO → Demo setup | Nothing to import: no database dump or media export is needed |

Page images ship inside the theme and are referenced through `get_theme_file_uri()` at render time. Local URLs therefore never end up in the database, and the homepage moves between environments without a search-replace. Product images are imported into the Media Library by the setup routine on each environment, so WooCommerce stays the source of truth.

## 1a. Local, with this repo (verified)

```bash
bin/setup-local.sh serve
```

- This downloads WordPress 7.1.5, WooCommerce and the SQLite Database Integration plugin (the stack WordPress Studio uses), installs everything into `.local-site/`, symlinks the theme and plugin from the repo, and runs the demo setup.
- The site runs at http://localhost:8881 (admin / `kesto-local`, local only).
- Re-running is safe; the clean-copy run and the install from the built ZIPs were both verified.

## 1b. Local, in WordPress Studio

[Unverified] This container has no Studio, so these steps follow Studio's documented workflow but were not run here:

1. In Studio, **Add site** → a blank site.
2. In that site's WP admin: **Plugins → Add New** → install and activate **WooCommerce**. Then **Upload Plugin** → `dist/kesto-core-1.0.0.zip` → activate.
3. **Appearance → Themes → Add New → Upload** → `dist/kesto-theme-1.1.0.zip` → activate.
4. **KESTO → Demo setup → Run setup**, or run `wp kesto setup` from Studio's terminal / WP-CLI.
   - The test payment method is only enabled where `WP_ENVIRONMENT_TYPE` is `local`, `development` or `staging`.
   - If Studio doesn't set that constant, add `define( 'WP_ENVIRONMENT_TYPE', 'local' );` to the site's `wp-config.php`, or enable a test gateway by hand.
5. To develop live from the repo instead of the ZIPs, symlink `themes/kesto` and `plugins/kesto-core` into the Studio site's `wp-content/`.

## 2. Reviewable preview (do this before production)

Use a **staging** site on the eventual host, or a temporary preview site, with `WP_ENVIRONMENT_TYPE` set to `staging`:

1. Install WooCommerce, upload both ZIPs, activate them, then run **KESTO → Demo setup**.
2. Leave **Concept mode** on: disclaimers, demo price tags and "pending verification" notes stay visible.
3. Payments: keep the "Test order — no payment taken" method, or connect your real gateway in **test mode** (WooPayments / Stripe test keys). Never use live keys on staging.
4. Walk through the journeys in `docs/QA.md`, or run `BASE=https://staging.example node tests/journeys.mjs`.

If the host is **WordPress.com**: [Unverified] custom plugins and themes need a plan that allows plugin uploads (Business or Commerce), and Studio's "Sync" can push a local Studio site to such a site. Confirm both against the current plan before choosing this route.

## 3. Production

**Before anything that could overwrite a live site, confirm the destination and take a backup:**
- a full host backup or snapshot (files and database), or `wp db export` plus a copy of `wp-content/uploads`
- write down the active theme, so you can roll back with **Appearance → Themes** or `wp theme activate <previous>`

Then:
1. Install and activate WooCommerce (if it isn't already) and `kesto-core`.
2. Upload and activate the theme. Activation only changes presentation: existing posts, pages, products and orders are untouched.
3. **Do not run the demo setup on a live store with real products** unless you want the demo catalog. If you do run it:
   - it never overwrites products or pages it did not create (it skips and reports them)
   - it only sets the static front page if none is set
   - it sets WooCommerce "coming soon" mode to off, which **makes the store public**
4. Real catalog: create or update products in WooCommerce. For each one, fill in the **KESTO tile** box (tile colour, food layer, descriptor), untick "Price is a demo value", and mark the homepage three as **Featured**. Menu order sets their order.
5. Pages: create **What's biltong?** (`/what-is-biltong/`), **Our roots** (`/our-roots/`), **Contact** (`/contact/`) and **Wholesale** (`/wholesale/`) with the "Story page (full width)" template, and insert the matching **KESTO pages** pattern. The setup routine does exactly this if you prefer.
6. Navigation links are relative (`/shop/`, `/what-is-biltong/`, …). If WordPress lives in a sub-directory, edit them in **Appearance → Editor → Patterns → Header/Footer**.
7. Email: configure SMTP and set the enquiry inbox under **KESTO → Claims & links**.
8. Payments: connect the real gateway and test in sandbox, then switch to live. Disable the cheque-based test method.
9. When every row in `docs/VERIFICATION.md` is resolved, turn **Concept mode** off.
10. Caching: the bag count refreshes from the Store API whenever WooCommerce's cart cookie exists, so full-page caching is safe. Exclude `/cart/`, `/checkout/` and `/my-account/` as usual.

## Rollback

Reactivate the previous theme. Deactivating `kesto-core` hides the claims, enquiry and setup features but keeps their data (options and the `kesto_enquiry` posts). Restore the backup if content was changed.
