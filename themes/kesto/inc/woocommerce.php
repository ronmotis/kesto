<?php
/**
 * WooCommerce integration: tile classes, small UX adjustments.
 * WooCommerce stays the source of truth for catalog, prices, stock, cart and checkout.
 *
 * @package kesto
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Carry the art-directed tile colour (product meta _kesto_tile) into product lists.
 */
add_filter(
	'post_class',
	static function ( array $classes, $class, $post_id ) {
		if ( 'product' === get_post_type( $post_id ) ) {
			$tile      = sanitize_html_class( get_post_meta( $post_id, '_kesto_tile', true ) ?: 'cream' );
			$classes[] = 'kesto-tile--' . $tile;
		}
		return $classes;
	},
	10,
	3
);

/** Fewer, larger related products. */
add_filter(
	'woocommerce_output_related_products_args',
	static function ( $args ) {
		$args['posts_per_page'] = 4;
		$args['columns']        = 4;
		return $args;
	}
);

/** Breadcrumb: friendlier home label. */
add_filter(
	'woocommerce_breadcrumb_defaults',
	static function ( $d ) {
		$d['home'] = __( 'Home', 'kesto' );
		return $d;
	}
);

/** Shopping language: "bag", matching the header. */
add_filter( 'woocommerce_product_add_to_cart_text', static fn( $t, $p = null ) => ( $p && $p->is_type( 'simple' ) && $p->is_purchasable() && $p->is_in_stock() ) ? __( 'Add to bag', 'kesto' ) : $t, 10, 2 );
add_filter( 'woocommerce_product_single_add_to_cart_text', static fn() => __( 'Add to bag', 'kesto' ) );

/** Tile colour on single product pages (gallery backdrop). */
add_filter(
	'body_class',
	static function ( array $classes ) {
		if ( is_singular( 'product' ) ) {
			$classes[] = 'kesto-tile--' . sanitize_html_class( get_post_meta( get_the_ID(), '_kesto_tile', true ) ?: 'cream' );
		}
		return $classes;
	}
);

/** Honest placeholder until a product's real packshot is supplied (e.g. Chilli Bites). */
add_filter( 'woocommerce_placeholder_img_src', static fn() => get_theme_file_uri( 'assets/images/placeholder-pouch.svg' ) );

/**
 * WooCommerce auto-inserts Customer Account and Mini-Cart blocks into block-theme headers.
 * KESTO's header already has an accessible "Bag (n)" link, so drop the duplicates.
 */
add_filter(
	'hooked_block_types',
	static function ( $hooked, $position, $anchor, $context ) {
		if ( $context instanceof WP_Block_Template && 'wp_template_part' === $context->type && 'header' === $context->slug ) {
			return array_values( array_diff( $hooked, array( 'woocommerce/customer-account', 'woocommerce/mini-cart' ) ) );
		}
		return $hooked;
	},
	20,
	4
);

/**
 * Performance: WooCommerce's classic stylesheets and scripts are only needed on store
 * screens (product forms, cart, checkout, account). Everywhere else — including the
 * homepage, whose quick-add uses the Store API via kesto.js — skip them.
 */
function kesto_is_store_screen(): bool {
	return is_woocommerce() || is_cart() || is_checkout() || is_account_page();
}
add_filter( 'woocommerce_enqueue_styles', static fn( $styles ) => kesto_is_store_screen() ? $styles : array() );
add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( kesto_is_store_screen() ) {
			return;
		}
		foreach ( array( 'wc-add-to-cart', 'woocommerce', 'wc-cart-fragments', 'jquery-blockui', 'js-cookie' ) as $handle ) {
			wp_dequeue_script( $handle );
		}
	},
	100
);
