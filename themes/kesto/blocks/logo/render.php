<?php
/**
 * kesto/logo — inline SVG lockup (traced from the supplied packaging photograph).
 *
 * @var array $attributes
 */
defined( 'ABSPATH' ) || exit;

static $kesto_logo_svg = null;
if ( null === $kesto_logo_svg ) {
	$kesto_logo_svg = (string) file_get_contents( get_theme_file_path( 'assets/images/kesto-logo.svg' ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$kesto_logo_svg = str_replace( 'role="img" aria-label="KESTO"', 'aria-hidden="true" focusable="false"', $kesto_logo_svg );
}
$color   = sanitize_html_class( $attributes['color'] ?? 'tomato' );
$wrapper = get_block_wrapper_attributes( array( 'class' => 'kesto-logo has-' . $color . '-color' ) );
$label   = esc_html__( 'KESTO home', 'kesto' );

if ( ! empty( $attributes['link'] ) ) {
	printf( '<a %s href="%s" rel="home">%s<span class="screen-reader-text">%s</span></a>', $wrapper, esc_url( home_url( '/' ) ), $kesto_logo_svg, $label ); // phpcs:ignore WordPress.Security.EscapeOutput
} else {
	printf( '<span %s role="img" aria-label="KESTO">%s</span>', $wrapper, $kesto_logo_svg ); // phpcs:ignore WordPress.Security.EscapeOutput
}
