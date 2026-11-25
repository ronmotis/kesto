<?php
/**
 * Plugin Name: KESTO Core
 * Description: KESTO site functionality: benefit claims & verification status, product tile settings, contact/wholesale enquiries, and a repeatable demo setup (wp kesto setup).
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 8.1
 * Author: KESTO
 * License: Proprietary
 * Text Domain: kesto
 * Requires Plugins: woocommerce
 * WC requires at least: 9.0
 * WC tested up to: 11.3
 *
 * @package kesto-core
 */

defined( 'ABSPATH' ) || exit;

define( 'KESTO_CORE_VERSION', '1.0.0' );
define( 'KESTO_CORE_DIR', __DIR__ );

require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/product-meta.php';
require_once __DIR__ . '/includes/enquiries.php';
require_once __DIR__ . '/includes/setup.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once __DIR__ . '/includes/cli.php';
}

// Declare HPOS + cart/checkout blocks compatibility.
add_action(
	'before_woocommerce_init',
	static function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}
);
