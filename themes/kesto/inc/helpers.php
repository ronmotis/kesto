<?php
/**
 * Shared helpers: theme image URLs, responsive sources, claims and concept flags.
 *
 * @package kesto
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL of a compiled theme image.
 */
function kesto_img( string $file ): string {
	return get_theme_file_uri( 'assets/images/' . $file );
}

/**
 * Build a srcset string for a compiled responsive set: name-<w>.webp.
 *
 * @param string $name   Base name, e.g. "hero-desktop".
 * @param int[]  $widths Widths that exist on disk.
 */
function kesto_srcset( string $name, array $widths, string $ext = 'webp' ): string {
	$out = array();
	foreach ( $widths as $w ) {
		$out[] = kesto_img( "{$name}-{$w}.{$ext}" ) . " {$w}w";
	}
	return implode( ', ', $out );
}

/**
 * Default benefit claims. These are PROVISIONAL design claims from the concept.
 * Edit them under KESTO → Claims (kesto-core plugin), which stores the `kesto_claims` option.
 *
 * scope: "biltong" = applies to beef products only; "all" = whole range.
 * status: "provisional" | "verified".
 */
function kesto_default_claims(): array {
	return array(
		array( 'id' => 'protein', 'label' => 'High protein', 'icon' => 'protein', 'scope' => 'biltong', 'status' => 'provisional', 'note' => 'Needs nutrition panel per serving.' ),
		array( 'id' => 'grass-fed', 'label' => 'Grass-fed beef', 'icon' => 'cow', 'scope' => 'biltong', 'status' => 'provisional', 'note' => 'Needs supplier documentation.' ),
		array( 'id' => 'gluten-free', 'label' => 'Gluten-free', 'icon' => 'gluten-free', 'scope' => 'all', 'status' => 'provisional', 'note' => 'Needs ingredient + facility confirmation for every product, including Magunanzi.' ),
		array( 'id' => 'usa', 'label' => 'Made in the USA', 'icon' => 'star', 'scope' => 'all', 'status' => 'provisional', 'note' => 'Needs confirmation of production location (packaging says "for Dallas & Beyond").' ),
	);
}

function kesto_claims(): array {
	$claims = get_option( 'kesto_claims' );
	return is_array( $claims ) && $claims ? $claims : kesto_default_claims();
}

/**
 * Whether the concept disclaimer should be shown (default on until content is verified).
 */
function kesto_concept_mode(): bool {
	return (bool) get_option( 'kesto_concept_mode', true );
}

/**
 * Inline SVG icon (benefit strip and UI). Drawn in one 2px stroke family.
 */
function kesto_icon( string $name ): string {
	$paths = array(
		'protein'     => '<path d="M3 12h18M6 8v8M3.5 9.5v5M18 8v8M20.5 9.5v5"/>',
		'cow'         => '<path d="M5 9c-1.2-.4-2-1.3-2-2.5 1.3 0 2.4.4 3 1.2M19 9c1.2-.4 2-1.3 2-2.5-1.3 0-2.4.4-3 1.2M6 8c1.5-1.3 3.6-2 6-2s4.5.7 6 2l-.6 5.5c-.3 2.6-2.3 4.5-5.4 4.5s-5.1-1.9-5.4-4.5z"/><path d="M10 14.5h.01M14 14.5h.01"/>',
		'gluten-free' => '<circle cx="12" cy="12" r="9"/><path d="M5.6 18.4 18.4 5.6M12 7c-1.5 1-2 2.5-1.5 4M12 7c1.5 1 2 2.5 1.5 4M12 11.5c-1.5 1-2 2.5-1.5 4M12 11.5c1.5 1 2 2.5 1.5 4M12 7v11"/>',
		'star'        => '<path d="m12 3 2.7 5.6 6.1.8-4.5 4.2 1.1 6.1L12 16.8l-5.4 2.9 1.1-6.1-4.5-4.2 6.1-.8z"/>',
		'bag'         => '<path d="M5 8h14l-1 12H6zM9 8V6a3 3 0 0 1 6 0v2"/>',
		'arrow-ne'    => '<path d="M7 17 17 7M8 7h9v9"/>',
		'menu'        => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'       => '<path d="M6 6l12 12M18 6 6 18"/>',
		'plus'        => '<path d="M12 5v14M5 12h14"/>',
		'check'       => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg class="kesto-icon kesto-icon--' . esc_attr( $name ) . '" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}
