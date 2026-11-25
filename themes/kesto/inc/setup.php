<?php
/**
 * Theme supports, pattern categories, meta.
 *
 * @package kesto
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/kesto.css' );
		remove_theme_support( 'core-block-patterns' );
	}
);

add_action(
	'init',
	static function () {
		register_block_pattern_category( 'kesto', array( 'label' => __( 'KESTO', 'kesto' ) ) );
		register_block_pattern_category( 'kesto-pages', array( 'label' => __( 'KESTO pages', 'kesto' ) ) );
	}
);

/**
 * Social sharing meta. Uses the campaign image until real photography is approved.
 */
add_action(
	'wp_head',
	static function () {
		if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
			return; // An SEO plugin owns these tags.
		}
		$desc = is_front_page()
			? __( 'Southern African biltong. Big on protein. Bigger on flavour. Original and Chilli Biltong, Chilli Bites and Magunanzi trail mix.', 'kesto' )
			: get_bloginfo( 'description' );
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $desc ) ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( wp_get_document_title() ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $desc ) ) );
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( kesto_img( 'og-kesto.jpg' ) ) );
		echo '<meta property="og:type" content="website">' . "\n";
		echo '<meta name="theme-color" content="#FBEBD6">' . "\n";
	},
	3
);

/** Performance: no emoji polyfill (system emoji render natively in all supported browsers). */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
