<?php
/**
 * Front-end assets. One stylesheet, one small deferred script, no framework.
 *
 * @package kesto
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	static function () {
		$ver = KESTO_THEME_VERSION . '.' . filemtime( get_theme_file_path( 'assets/css/kesto.css' ) );
		wp_enqueue_style( 'kesto', get_theme_file_uri( 'assets/css/kesto.css' ), array(), $ver );

		$jsver = KESTO_THEME_VERSION . '.' . filemtime( get_theme_file_path( 'assets/js/kesto.js' ) );
		wp_enqueue_script(
			'kesto',
			get_theme_file_uri( 'assets/js/kesto.js' ),
			array(),
			$jsver,
			array( 'in_footer' => true, 'strategy' => 'defer' )
		);
		wp_localize_script(
			'kesto',
			'kestoData',
			array(
				'storeApi' => esc_url_raw( rest_url( 'wc/store/v1/' ) ),
				'nonce'    => class_exists( 'WooCommerce' ) ? wp_create_nonce( 'wc_store_api' ) : '',
				'cartUrl'  => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '',
				'i18n'     => array(
					'adding' => __( 'Adding…', 'kesto' ),
					'added'  => __( 'Added to bag', 'kesto' ),
					'error'  => __( 'Couldn’t add that. Please try again.', 'kesto' ),
					'view'   => __( 'View bag', 'kesto' ),
				),
			)
		);
	}
);

/**
 * Mark JS + motion capability before first paint, so entrance states never flash
 * and never hide content when scripts are blocked. If kesto.js has not taken over within 4s
 * (blocked or failed), a timer reveals everything. Chrome sometimes skips a
 * page-to-page view transition and rejects a promise no script can reach; that
 * one expected rejection is marked handled so it isn't reported as an error.
 */
add_action(
	'wp_head',
	static function () {
		echo "<script>(function(d){var c=d.documentElement.classList;c.add('kesto-js');if(window.matchMedia&&!matchMedia('(prefers-reduced-motion: reduce)').matches&&'IntersectionObserver' in window){c.add('kesto-motion');setTimeout(function(){c.contains('kesto-io')||c.add('kesto-revealed')},4000)}addEventListener('pagereveal',function(e){var v=e.viewTransition;if(v){[v.ready,v.finished,v.updateCallbackDone].forEach(function(q){q&&q.catch(function(){})})}});addEventListener('unhandledrejection',function(e){var r=e.reason;if(r&&r.name==='InvalidStateError'&&/transition/i.test(r.message||''))e.preventDefault()})})(document);</script>\n";
	},
	1
);

/**
 * Preload the display face and, on the homepage, the hero photograph.
 */
add_action(
	'wp_head',
	static function () {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_theme_file_uri( 'assets/fonts/chau-philomene-one-400-latin.woff2' ) )
		);
		if ( is_front_page() ) {
			printf(
				'<link rel="preload" as="image" media="(min-width: 600px)" imagesrcset="%s" imagesizes="70vw" fetchpriority="high">' . "\n",
				esc_attr( kesto_srcset( 'hero-desktop', array( 960, 1440, 1920, 2560 ) ) )
			);
			printf(
				'<link rel="preload" as="image" media="(max-width: 599px)" imagesrcset="%s" imagesizes="100vw" fetchpriority="high">' . "\n",
				esc_attr( kesto_srcset( 'hero-mobile', array( 480, 800, 1200 ) ) )
			);
		}
	},
	2
);
