<?php
/**
 * Repeatable demo setup.
 *
 * Run with `wp kesto setup` (or KESTO → Demo setup in wp-admin). Safe to re-run:
 *  - Products are matched by SKU, media by `_kesto_asset_key`, pages by slug.
 *  - Entries this routine created carry `_kesto_seed = 1` and are updated in place.
 *  - Existing content WITHOUT that marker is never overwritten; it is reported and skipped.
 *
 * Every price it writes is a DEMO value (meta `_kesto_demo = 1`).
 *
 * @package kesto-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Catalog seed. Facts come from packaging; prices are demo values.
 */
function kesto_core_seed_products(): array {
	return array(
		array(
			'sku'      => 'KESTO-BIL-ORIG-200',
			'name'     => 'Original Biltong',
			'slug'     => 'original-biltong',
			'price'    => '14.99',
			'cat'      => 'biltong',
			'featured' => true,
			'order'    => 1,
			'tile'     => 'orange',
			'food'     => 'biltong',
			'tagline'  => 'Premium air-dried beef',
			'image'    => 'pouch-original',
			'short'    => 'Seasoned beef, air-dried and sliced. The classic place to start. NET 200 g resealable pouch.',
			'long'     => "<p>Biltong is seasoned beef that is air-dried and sliced. A Southern African favourite, ready for your next snack.</p>\n<p>Original is KESTO’s classic: premium air-dried beef, crafted with passion, rooted in savory tradition.</p>\n<p><strong>Pack:</strong> NET 200 g, resealable bag.</p>\n<p><strong>Ingredients, allergens and nutrition:</strong> to be confirmed.</p>",
		),
		array(
			'sku'      => 'KESTO-BIL-CHIL-200',
			'name'     => 'Chilli Biltong',
			'slug'     => 'chilli-biltong',
			'price'    => '15.99',
			'cat'      => 'biltong',
			'featured' => true,
			'order'    => 2,
			'tile'     => 'red',
			'food'     => 'chilli',
			'tagline'  => 'Premium air-dried beef, with chilli',
			'image'    => 'pouch-chilli',
			'short'    => 'Our biltong with a chilli kick. NET 200 g resealable pouch.',
			'long'     => "<p>Biltong is seasoned beef that is air-dried and sliced. Chilli adds heat to KESTO’s premium air-dried beef.</p>\n<p><strong>Pack:</strong> NET 200 g, resealable bag.</p>\n<p><strong>Ingredients, allergens, heat level and nutrition:</strong> to be confirmed.</p>",
		),
		array(
			'sku'      => 'KESTO-MAG-200',
			'name'     => 'Magunanzi Trail Mix',
			'slug'     => 'magunanzi-trail-mix',
			'price'    => '9.99',
			'cat'      => 'trail-mix',
			'featured' => true,
			'order'    => 3,
			'tile'     => 'cream',
			'food'     => 'trailmix',
			'tagline'  => 'Roasted corn & nuts',
			'image'    => 'pouch-trail',
			'short'    => 'A perfect blend of roasted corn and nuts. Energy, nutrition, adventure. NET 200 g resealable pouch.',
			'long'     => "<p>Magunanzi is KESTO’s trail mix: a perfect blend of roasted corn and nuts. Fuel your journey.</p>\n<p><strong>Pack:</strong> NET 200 g, resealable bag.</p>\n<p><strong>Contains nuts.</strong> Full ingredients, allergens and nutrition: to be confirmed.</p>",
		),
		array(
			'sku'      => 'KESTO-BIL-BITES',
			'name'     => 'Chilli Bites',
			'slug'     => 'chilli-bites',
			'price'    => '12.99',
			'cat'      => 'biltong',
			'featured' => false,
			'order'    => 4,
			'tile'     => 'red',
			'food'     => 'chilli',
			'tagline'  => 'Chilli biltong bites',
			'image'    => '',
			'short'    => 'Chilli biltong in bite-size pieces. Pack size and packshot to be confirmed.',
			'long'     => "<p>Chilli Bites: KESTO chilli biltong in bite-size pieces.</p>\n<p><strong>Demo catalog entry.</strong> Pack size, imagery, ingredients and nutrition are to be confirmed before launch.</p>",
		),
	);
}

function kesto_core_seed_pages(): array {
	return array(
		'home'            => array( 'title' => 'Home', 'content' => '', 'template' => '' ),
		'what-is-biltong' => array( 'title' => 'What’s biltong?', 'content' => '<!-- wp:pattern {"slug":"kesto/page-biltong"} /-->', 'template' => 'page-story' ),
		'our-roots'       => array( 'title' => 'Our roots', 'content' => '<!-- wp:pattern {"slug":"kesto/page-roots"} /-->', 'template' => 'page-story' ),
		'contact'         => array( 'title' => 'Contact', 'content' => '<!-- wp:pattern {"slug":"kesto/page-contact"} /-->', 'template' => 'page-story' ),
		'wholesale'       => array( 'title' => 'Wholesale', 'content' => '<!-- wp:pattern {"slug":"kesto/page-wholesale"} /-->', 'template' => 'page-story' ),
	);
}

/**
 * Import a compiled theme image into the Media Library once; reuse it afterwards.
 */
function kesto_core_import_asset( string $key, string $alt, array &$log ): int {
	$existing = get_posts(
		array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_kesto_asset_key', 'meta_value' => $key, 'fields' => 'ids', 'numberposts' => 1 ) // phpcs:ignore WordPress.DB.SlowDBQuery
	);
	if ( $existing ) {
		return (int) $existing[0];
	}
	$src = wp_get_theme( 'kesto' )->get_stylesheet_directory() . "/assets/images/{$key}-960.webp";
	if ( ! file_exists( $src ) ) {
		$log[] = "! asset missing: {$src}";
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( $key );
	copy( $src, $tmp );
	$id = media_handle_sideload( array( 'name' => "kesto-{$key}.webp", 'tmp_name' => $tmp ), 0, $alt );
	if ( is_wp_error( $id ) ) {
		$log[] = '! media import failed for ' . $key . ': ' . $id->get_error_message();
		return 0;
	}
	update_post_meta( $id, '_kesto_asset_key', $key );
	update_post_meta( $id, '_kesto_seed', 1 );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	$log[] = "+ media {$key} (#{$id})";
	return (int) $id;
}

function kesto_core_run_setup(): array {
	$log = array();
	if ( ! class_exists( 'WooCommerce' ) ) {
		return array( '! WooCommerce is not active. Activate it, then run setup again.' );
	}

	// Store basics (only set when still at WooCommerce defaults).
	update_option( 'woocommerce_currency', get_option( 'woocommerce_currency' ) ?: 'USD' );
	if ( in_array( get_option( 'woocommerce_default_country' ), array( '', 'US:CA' ), true ) ) {
		update_option( 'woocommerce_default_country', 'US:TX' );
	}
	update_option( 'woocommerce_coming_soon', 'no' );
	update_option( 'woocommerce_store_pages_only', 'no' );
	update_option( 'woocommerce_weight_unit', 'g' );
	update_option( 'woocommerce_enable_reviews', 'no' );
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$log[] = '~ permalinks set to /%postname%/';
	}
	update_option( 'blogdescription', 'Southern African biltong and snacks' );

	// WooCommerce core pages (shop, cart, checkout, account) — creates only missing ones.
	if ( class_exists( 'WC_Install' ) ) {
		WC_Install::create_pages();
	}

	// Categories.
	$cats = array(
		'biltong'   => array( 'Biltong', 'Seasoned beef, air-dried and sliced.' ),
		'trail-mix' => array( 'Trail mix', 'Magunanzi: roasted corn and nuts.' ),
	);
	$cat_ids = array();
	foreach ( $cats as $slug => [ $name, $desc ] ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term ) {
			$t = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug, 'description' => $desc ) );
			$cat_ids[ $slug ] = is_wp_error( $t ) ? 0 : (int) $t['term_id'];
			$log[] = "+ category {$slug}";
		} else {
			$cat_ids[ $slug ] = (int) $term->term_id;
		}
	}

	// Products, keyed by SKU.
	foreach ( kesto_core_seed_products() as $p ) {
		$id      = wc_get_product_id_by_sku( $p['sku'] );
		$product = $id ? wc_get_product( $id ) : null;
		if ( $product && ! get_post_meta( $id, '_kesto_seed', true ) ) {
			$log[] = "= {$p['sku']} exists and was not created by setup — left untouched";
			continue;
		}
		$is_new  = ! $product;
		$product = $product ?: new WC_Product_Simple();
		$product->set_name( $p['name'] );
		$product->set_slug( $p['slug'] );
		$product->set_sku( $p['sku'] );
		$product->set_regular_price( $p['price'] );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_featured( $p['featured'] );
		$product->set_menu_order( $p['order'] );
		$product->set_short_description( $p['short'] );
		$product->set_description( $p['long'] );
		$product->set_manage_stock( false );
		$product->set_stock_status( 'instock' );
		$product->set_category_ids( array_filter( array( $cat_ids[ $p['cat'] ] ?? 0 ) ) );
		if ( $p['image'] ) {
			$img = kesto_core_import_asset( $p['image'], 'KESTO ' . $p['name'] . ' resealable pouch', $log );
			if ( $img ) {
				$product->set_image_id( $img );
			}
		}
		$id = $product->save();
		update_post_meta( $id, '_kesto_seed', 1 );
		update_post_meta( $id, '_kesto_tile', $p['tile'] );
		update_post_meta( $id, '_kesto_food', $p['food'] );
		update_post_meta( $id, '_kesto_tagline', $p['tagline'] );
		update_post_meta( $id, '_kesto_demo', 1 );
		$log[] = ( $is_new ? '+ product ' : '~ product ' ) . "{$p['sku']} (#{$id})";
	}

	// Pages, keyed by slug.
	$page_ids = array();
	foreach ( kesto_core_seed_pages() as $slug => $page ) {
		$existing = get_page_by_path( $slug );
		if ( $existing && ! get_post_meta( $existing->ID, '_kesto_seed', true ) ) {
			$page_ids[ $slug ] = $existing->ID;
			$log[]             = "= page /{$slug}/ exists and was not created by setup — left untouched";
			continue;
		}
		$args = array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $page['title'],
			'post_name'    => $slug,
			'post_content' => $page['content'],
		);
		if ( $existing ) {
			$args['ID'] = $existing->ID;
			// Respect edits: only refresh content still equal to a pattern reference.
			if ( '' !== $existing->post_content && ! str_starts_with( trim( $existing->post_content ), '<!-- wp:pattern' ) ) {
				unset( $args['post_content'] );
			}
		}
		$id = wp_insert_post( $args, true );
		if ( is_wp_error( $id ) ) {
			$log[] = "! page {$slug}: " . $id->get_error_message();
			continue;
		}
		update_post_meta( $id, '_kesto_seed', 1 );
		if ( $page['template'] ) {
			update_post_meta( $id, '_wp_page_template', $page['template'] );
		}
		$page_ids[ $slug ] = $id;
		$log[]             = ( $existing ? '~ page /' : '+ page /' ) . "{$slug}/ (#{$id})";
	}
	if ( ! empty( $page_ids['home'] ) && 'page' !== get_option( 'show_on_front' ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['home'] );
		$log[] = '~ static front page set to Home';
	}

	// Demo shipping: one US zone with a flat rate, created once.
	if ( ! get_option( 'kesto_seed_zone' ) && class_exists( 'WC_Shipping_Zone' ) ) {
		$zone = new WC_Shipping_Zone();
		$zone->set_zone_name( 'United States (demo)' );
		$zone->add_location( 'US', 'country' );
		$zone->save();
		$instance = $zone->add_shipping_method( 'flat_rate' );
		update_option(
			"woocommerce_flat_rate_{$instance}_settings",
			array( 'title' => 'Standard shipping (demo rate)', 'tax_status' => 'none', 'cost' => '5.00' )
		);
		update_option( 'kesto_seed_zone', $zone->get_id() );
		$log[] = '+ shipping zone US (demo flat rate)';
	}

	// Test-mode payment for non-production environments only. Never touches live gateways.
	if ( in_array( wp_get_environment_type(), array( 'local', 'development', 'staging' ), true ) ) {
		$cheque               = (array) get_option( 'woocommerce_cheque_settings', array() );
		$cheque['enabled']    = 'yes';
		$cheque['title']      = 'Test order — no payment taken';
		$cheque['description'] = 'Preview only. Your order is recorded but no payment is collected.';
		update_option( 'woocommerce_cheque_settings', $cheque );
		$log[] = '~ test payment method enabled (' . wp_get_environment_type() . ' environment)';
	}

	if ( false === get_option( 'kesto_concept_mode', false ) ) {
		add_option( 'kesto_concept_mode', true );
	}
	update_option( 'kesto_seeded_once', gmdate( 'c' ) );
	flush_rewrite_rules( false );
	$log[] = 'Done. Prices are demo values; claims are provisional (KESTO → Claims & links).';
	return $log;
}

/* wp-admin button: KESTO → Demo setup. */
add_action(
	'admin_menu',
	static function () {
		add_submenu_page( 'kesto', __( 'Demo setup', 'kesto' ), __( 'Demo setup', 'kesto' ), 'manage_options', 'kesto-setup', 'kesto_core_setup_page' );
	},
	20
);

function kesto_core_setup_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$log = null;
	if ( isset( $_POST['kesto_setup_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['kesto_setup_nonce'] ), 'kesto_setup' ) ) {
		$log = kesto_core_run_setup();
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'KESTO demo setup', 'kesto' ); ?></h1>
		<p><?php esc_html_e( 'Creates or updates the demo catalog (4 products with DEMO prices), story pages, a demo shipping rate and, outside production, a test-only payment method. Content you created yourself is never overwritten.', 'kesto' ); ?></p>
		<form method="post"><?php wp_nonce_field( 'kesto_setup', 'kesto_setup_nonce' ); ?><?php submit_button( __( 'Run setup', 'kesto' ) ); ?></form>
		<?php if ( $log ) : ?>
			<pre style="background:#fff;padding:1em;max-width:900px;white-space:pre-wrap"><?php echo esc_html( implode( "\n", $log ) ); ?></pre>
		<?php endif; ?>
	</div>
	<?php
}
