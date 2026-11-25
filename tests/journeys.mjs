/**
 * KESTO shopper journeys — end-to-end checks against a running local site.
 *   BASE=http://localhost:8881 node tests/journeys.mjs
 * Requires Playwright (pre-installed Chromium or `npx playwright install chromium`).
 */
import { createRequire } from 'module';
const require = createRequire(import.meta.url);
const { chromium } = require('playwright');

const BASE = process.env.BASE || 'http://localhost:8881';
const OUT = process.env.OUT || '.impeccable/review/journeys';
const results = [];
const check = (name, ok, detail = '') => { results.push({ name, ok, detail }); console.log(`${ok ? 'PASS' : 'FAIL'}  ${name}${detail ? ' — ' + detail : ''}`); };

const browser = await chromium.launch();
const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
const page = await ctx.newPage();
const errors = [];
page.on('pageerror', (e) => errors.push(e.message));
// 422s are the deliberate invalid-form submissions below; anything else counts.
page.on('console', (m) => { if (m.type() === 'error' && !/favicon|status of 422/.test(m.text())) errors.push(m.text()); });
const count = () => page.locator('[data-kesto-bag-count]').first().textContent();

try {
	// 1. Homepage → featured product.
	await page.goto(BASE + '/', { waitUntil: 'networkidle' });
	check('Home has one h1', (await page.locator('h1').count()) === 1);
	check('Hero CTA targets the shop section', (await page.locator('.kesto-hero a.wp-block-button__link').getAttribute('href')) === '#shop');
	check('Three featured products render', (await page.locator('.kesto-product').count()) === 3);
	const names = await page.locator('.kesto-product__name').allTextContents();
	check('Featured order matches comp', names.join('|') === 'Original Biltong|Chilli Biltong|Magunanzi Trail Mix', names.join('|'));
	await page.locator('.kesto-product__shop').first().click();
	await page.waitForLoadState('networkidle');
	check('Shop link opens the product page', /\/product\/original-biltong\/?$/.test(page.url()), page.url());

	// 2. Explanation of biltong → back to the range.
	await page.goto(BASE + '/', { waitUntil: 'networkidle' });
	await page.getByRole('link', { name: 'Explore the story' }).click();
	await page.waitForLoadState('networkidle');
	check('Story CTA opens What’s biltong?', /what-is-biltong/.test(page.url()));
	check('Explainer defines biltong', (await page.locator('main').textContent()).includes('Biltong is seasoned beef that is air-dried and sliced'));
	await page.getByRole('link', { name: 'Shop the range' }).click();
	await page.waitForLoadState('networkidle');
	check('Explainer routes back to the range', /\/shop\/?$/.test(page.url()));
	check('Full range lists 4 products incl. Chilli Bites', (await page.locator('.kesto-range li.wc-block-product').count()) === 4 && (await page.locator('main').textContent()).includes('Chilli Bites'));

	// 3. Quick add from the homepage + live bag count.
	await page.goto(BASE + '/', { waitUntil: 'networkidle' });
	check('Bag starts at 0', (await count()) === '0');
	await page.locator('[data-kesto-add]').first().click();
	await page.waitForFunction(() => document.querySelector('[data-kesto-bag-count]').textContent === '1', null, { timeout: 8000 });
	check('Quick add updates bag to 1', true);
	await page.waitForFunction(() => /Added to bag/.test(document.querySelector('[data-kesto-live]').textContent), null, { timeout: 4000 }).catch(() => {});
	check('Quick add announces to screen readers', /Added to bag/.test(await page.locator('[data-kesto-live]').textContent()));
	await page.locator('[data-kesto-add]').nth(2).click();
	await page.waitForFunction(() => document.querySelector('[data-kesto-bag-count]').textContent === '2', null, { timeout: 8000 });
	check('Second product brings bag to 2', true);
	await page.reload({ waitUntil: 'networkidle' });
	check('Bag count survives reload', (await count()) === '2');

	// 4. Product page add with quantity.
	await page.goto(BASE + '/product/chilli-biltong/', { waitUntil: 'networkidle' });
	await page.locator('input.qty').fill('2');
	await Promise.all([page.waitForLoadState('networkidle'), page.locator('.single_add_to_cart_button').click()]);
	await page.waitForTimeout(800);
	check('Product page add (qty 2) → bag 4', (await count()) === '4', await count());

	// 5. Cart: quantity update and removal.
	await page.goto(BASE + '/cart/', { waitUntil: 'networkidle' });
	await page.waitForFunction(() => document.querySelectorAll('.wc-block-cart-items__row').length === 3, null, { timeout: 15000 }).catch(() => {});
	check('Cart shows 3 lines', (await page.locator('.wc-block-cart-items__row').count()) === 3);
	await page.screenshot({ path: `${OUT}-cart.png`, fullPage: true });
	await page.locator('.wc-block-cart-items__row').first().locator('.wc-block-components-quantity-selector__button--plus').click();
	await page.waitForFunction(() => document.querySelector('[data-kesto-bag-count]').textContent === '5', null, { timeout: 10000 }).catch(() => {});
	check('Quantity + updates header bag to 5', (await count()) === '5', await count());
	await page.locator('.wc-block-cart-items__row').last().locator('.wc-block-cart-item__remove-link').click();
	await page.waitForFunction(() => document.querySelectorAll('.wc-block-cart-items__row').length === 2, null, { timeout: 10000 });
	check('Remove item leaves 2 lines', true);

	// 6. Cart → checkout → test order.
	await page.locator('.wc-block-cart__submit-button').click();
	await page.waitForURL(/checkout/, { timeout: 15000 });
	await page.waitForSelector('#email', { timeout: 15000 });
	await page.fill('#email', 'test.shopper@example.com');
	await page.fill('#shipping-first_name', 'Test');
	await page.fill('#shipping-last_name', 'Shopper');
	await page.fill('#shipping-address_1', '100 Main St');
	await page.fill('#shipping-city', 'Dallas');
	const state = page.locator('#shipping-state');
	if (await state.count()) { await state.selectOption({ label: 'Texas' }).catch(async () => { await state.fill('Texas'); }); }
	await page.fill('#shipping-postcode', '75201');
	await page.getByText('Test order — no payment taken').first().waitFor({ timeout: 20000 }).catch(() => {});
	await page.waitForLoadState('networkidle');
	await page.screenshot({ path: `${OUT}-checkout.png`, fullPage: true });
	check('Test payment method offered', (await page.locator('main').textContent()).includes('Test order — no payment taken'));
	await page.locator('.wc-block-components-checkout-place-order-button').click();
	const placed = await page.waitForURL(/order-received/, { timeout: 30000 }).then(() => true).catch(() => false);
	if (!placed) {
		const notice = await page.locator('.wc-block-components-notice-banner, .wc-block-components-validation-error').allTextContents();
		throw new Error('Order not placed: ' + notice.join(' / '));
	}
	check('Order placed → confirmation page', true, page.url().replace(BASE, ''));
	await page.screenshot({ path: `${OUT}-order.png`, fullPage: true });
	await page.goto(BASE + '/', { waitUntil: 'networkidle' });
	check('Bag empties after order', (await count()) === '0', await count());

	// 7. Contact + wholesale enquiries.
	await page.goto(BASE + '/contact/', { waitUntil: 'networkidle' });
	await page.locator('form[data-kesto-form] button[type=submit]').click();
	await page.waitForSelector('.kesto-form__status.is-error');
	check('Empty contact form shows field errors', (await page.locator('[aria-invalid="true"]').count()) >= 3);
	check('Focus moves to first invalid field', await page.evaluate(() => document.activeElement?.name === 'name'));
	await page.fill('input[name=name]', 'Test Person');
	await page.fill('input[name=email]', 'not-an-email');
	await page.fill('textarea[name=message]', 'Do you ship to Austin?');
	await page.locator('form[data-kesto-form] button[type=submit]').click();
	await page.waitForSelector('[name=email][aria-invalid="true"]');
	check('Invalid email rejected by server', true);
	await page.fill('input[name=email]', 'person@example.com');
	await page.locator('form[data-kesto-form] button[type=submit]').click();
	await page.waitForSelector('.kesto-form__status.is-success', { timeout: 10000 });
	check('Valid contact enquiry confirmed only after storage', /received/.test(await page.locator('.kesto-form__status').textContent()));

	await page.goto(BASE + '/wholesale/', { waitUntil: 'networkidle' });
	await page.fill('input[name=name]', 'Store Buyer');
	await page.fill('input[name=email]', 'buyer@example.com');
	await page.fill('input[name=business]', 'Corner Deli');
	await page.fill('textarea[name=message]', 'Interested in stocking Original and Chilli.');
	await page.locator('form[data-kesto-form] button[type=submit]').click();
	await page.waitForSelector('.kesto-form__status.is-success', { timeout: 10000 });
	check('Wholesale enquiry submitted', true);

	// 8. No-JS form fallback.
	const nojs = await browser.newContext({ javaScriptEnabled: false });
	const p2 = await nojs.newPage();
	await p2.goto(BASE + '/contact/');
	await p2.fill('input[name=name]', 'No JS');
	await p2.fill('input[name=email]', 'nojs@example.com');
	await p2.fill('textarea[name=message]', 'Testing the fallback.');
	await Promise.all([p2.waitForNavigation(), p2.locator('form button[type=submit]').click()]);
	check('No-JS submission redirects with success', /kesto_enquiry=sent/.test(p2.url()) && /received/.test(await p2.locator('.kesto-form__status').textContent()));
	await nojs.close();

	// 9. Mobile menu + keyboard.
	const m = await browser.newContext({ viewport: { width: 390, height: 844 }, hasTouch: true, isMobile: true });
	const mp = await m.newPage();
	await mp.goto(BASE + '/', { waitUntil: 'networkidle' });
	check('Bag visible on phone without opening menu', await mp.locator('.kesto-bag').isVisible());
	const toggle = mp.locator('.wp-block-navigation__responsive-container-open').first();
	check('Menu button has accessible name', !!(await toggle.getAttribute('aria-label')));
	await toggle.click();
	await mp.waitForSelector('.wp-block-navigation__responsive-container.is-menu-open');
	await mp.screenshot({ path: `${OUT}-mobile-menu.png` });
	check('Menu opens with links', await mp.getByRole('link', { name: 'What’s biltong?' }).first().isVisible());
	await mp.keyboard.press('Escape');
	await mp.waitForTimeout(400);
	check('Escape closes menu', (await mp.locator('.wp-block-navigation__responsive-container.is-menu-open').count()) === 0);
	await m.close();

	// 10. Keyboard order on desktop + visible focus.
	await page.goto(BASE + '/', { waitUntil: 'networkidle' });
	await page.keyboard.press('Tab');
	const first = await page.evaluate(() => document.activeElement.textContent.trim());
	check('First Tab reaches skip link', /skip/i.test(first), first);
	const tabs = [];
	for (let i = 0; i < 7; i++) { await page.keyboard.press('Tab'); tabs.push(await page.evaluate(() => (document.activeElement.textContent || document.activeElement.getAttribute('aria-label') || '').trim().replace(/\s+/g, ' ').slice(0, 30))); }
	check('Tab order: logo, nav, bag, CTA', tabs.slice(0, 6).join(' | ').includes('Find my snack'), tabs.join(' | '));
	const outline = await page.evaluate(() => getComputedStyle(document.activeElement).outlineStyle);
	check('Focused control shows an outline', outline !== 'none', outline);

	// 11. Reduced motion: everything visible immediately.
	const rm = await browser.newContext({ viewport: { width: 1280, height: 900 }, reducedMotion: 'reduce' });
	const rp = await rm.newPage();
	await rp.goto(BASE + '/', { waitUntil: 'networkidle' });
	const hidden = await rp.evaluate(() => [...document.querySelectorAll('.kesto-stage, .kesto-product, .kesto-arrow path, .kesto-hero__photo')].filter((e) => { const c = getComputedStyle(e); return parseFloat(c.opacity) < 1 || (c.strokeDashoffset && parseFloat(c.strokeDashoffset) > 0.01); }).length);
	check('Reduced motion: no hidden/undrawn elements', hidden === 0, `${hidden} hidden`);
	check('Reduced motion: no motion class', !(await rp.evaluate(() => document.documentElement.classList.contains('kesto-motion'))));
	await rm.close();

	check('No JS errors during journeys', errors.length === 0, errors.slice(0, 3).join(' | '));
} catch (e) {
	check('Journey run completed', false, e.message.split('\n')[0]);
	await page.screenshot({ path: `${OUT}-failure.png`, fullPage: true }).catch(() => {});
}
await browser.close();
const failed = results.filter((r) => !r.ok).length;
console.log(`\n${results.length - failed}/${results.length} checks passed`);
process.exit(failed ? 1 : 0);
