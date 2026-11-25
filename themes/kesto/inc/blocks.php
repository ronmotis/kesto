<?php
/**
 * Register the theme's server-rendered blocks (no build step required).
 *
 * @package kesto
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	static function () {
		wp_register_script(
			'kesto-blocks-editor',
			get_theme_file_uri( 'blocks/editor.js' ),
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
			KESTO_THEME_VERSION . '.' . filemtime( get_theme_file_path( 'blocks/editor.js' ) ),
			true
		);
		wp_localize_script( 'kesto-blocks-editor', 'kestoEditor', array( 'themeUrl' => get_theme_file_uri() ) );

		foreach ( array( 'picture', 'arrow', 'logo', 'bag', 'benefits', 'featured-products', 'concept-note', 'social', 'product-notes' ) as $block ) {
			register_block_type( get_theme_file_path( "blocks/{$block}" ) );
		}
	}
);
