<?php
/**
 * kesto/picture — <picture> with an art-directed mobile crop.
 *
 * @var array $attributes
 */
defined( 'ABSPATH' ) || exit;

$a = $attributes;
if ( empty( $a['desktopUrl'] ) ) {
	return;
}
$priority = ! empty( $a['priority'] );
$img_attr = array(
	'src'      => $a['desktopUrl'],
	'srcset'   => $a['desktopSrcset'] ?: '',
	'sizes'    => $a['sizes'] ?: '100vw',
	'alt'      => $a['alt'] ?? '',
	'width'    => $a['desktopWidth'] ?: '',
	'height'   => $a['desktopHeight'] ?: '',
	'loading'  => $priority ? 'eager' : 'lazy',
	'decoding' => $priority ? 'sync' : 'async',
	'style'    => 'object-position:' . preg_replace( '/[^0-9.% ]/', '', $a['focal'] ?: '50% 50%' ),
);
if ( $priority ) {
	$img_attr['fetchpriority'] = 'high';
}
$img = '<img';
foreach ( array_filter( $img_attr, static fn( $v ) => '' !== $v || false ) as $k => $v ) {
	if ( '' === $v && 'alt' !== $k ) {
		continue;
	}
	$img .= ' ' . $k . '="' . esc_attr( (string) $v ) . '"';
}
$img .= '>';

$source = '';
if ( ! empty( $a['mobileUrl'] ) ) {
	$source = sprintf(
		'<source media="(max-width: 599px)" srcset="%s" sizes="100vw"%s%s>',
		esc_attr( $a['mobileSrcset'] ?: $a['mobileUrl'] ),
		$a['mobileWidth'] ? ' width="' . (int) $a['mobileWidth'] . '"' : '',
		$a['mobileHeight'] ? ' height="' . (int) $a['mobileHeight'] . '"' : ''
	);
}
$wrapper = get_block_wrapper_attributes( array( 'class' => 'kesto-picture' ) );
printf( '<picture %s>%s%s</picture>', $wrapper, $source, $img ); // phpcs:ignore WordPress.Security.EscapeOutput -- attributes escaped above.
