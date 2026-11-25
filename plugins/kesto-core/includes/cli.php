<?php
/**
 * WP-CLI: `wp kesto setup` — repeatable demo setup (see includes/setup.php).
 *
 * @package kesto-core
 */

defined( 'ABSPATH' ) || exit;

WP_CLI::add_command(
	'kesto setup',
	static function () {
		foreach ( kesto_core_run_setup() as $line ) {
			str_starts_with( $line, '!' ) ? WP_CLI::warning( $line ) : WP_CLI::log( $line );
		}
		WP_CLI::success( 'KESTO demo setup complete.' );
	},
	array( 'shortdesc' => 'Create or update KESTO demo products, pages, shipping and test payment. Safe to re-run.' )
);
