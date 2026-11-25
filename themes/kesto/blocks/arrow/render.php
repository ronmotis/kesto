<?php
/**
 * kesto/arrow — hand-drawn SVG strokes. Each stroke is its own path so it can draw in sequence.
 *
 * @var array $attributes
 */
defined( 'ABSPATH' ) || exit;

$variants = array(
	// Curls from upper right, loops, and points left into a button.
	'hook'          => array( '0 0 120 90', array( 'M108 6c4 22-6 44-30 55-17 8-38 9-58 6', 'M34 54 18 67l17 10' ) ),
	// Long lazy loop pointing right/down to products.
	'loop'          => array( '0 0 150 80', array( 'M4 18c22 34 58 46 84 34 15-7 16-25 2-27-17-3-18 24-2 37 13 10 34 11 52 6', 'M128 56l14 12-17 6' ) ),
	// Short swirl between story photos.
	'swirl'         => array( '0 0 110 60', array( 'M4 38c15-14 34-20 46-12 9 6 4 18-5 15-10-3-6-19 9-24 15-5 32 0 46 10', 'M88 16l17 11-19 7' ) ),
	// Sweeping arrow pointing left (into a button on its left).
	'swoop'         => array( '0 0 120 70', array( 'M114 8c-1 28-24 47-60 50-14 1-30-1-44-6', 'M24 40 8 52l19 6' ) ),
	// Down-curving arrow (mobile CTA).
	'curve-down'    => array( '0 0 80 110', array( 'M60 4C24 18 14 52 30 92', 'M17 82l13 16 13-17' ) ),
	// Underline swashes.
	'scribble'      => array( '0 0 240 26', array( 'M4 18c48-9 108-14 168-11 22 1 42 3 62 7' ) ),
	'scribble-long' => array( '0 0 360 30', array( 'M4 20c70-12 160-17 250-13 36 2 70 5 100 10' ) ),
);
$variant = isset( $variants[ $attributes['variant'] ] ) ? $attributes['variant'] : 'hook';
[ $viewbox, $paths ] = $variants[ $variant ];
$color   = in_array( $attributes['color'], array( 'tomato', 'cream', 'mango', 'ink' ), true ) ? $attributes['color'] : 'tomato';

$classes = 'kesto-arrow kesto-arrow--' . $variant . ' has-' . $color . '-color' . ( ! empty( $attributes['flip'] ) ? ' is-flipped' : '' );
$svg     = '<svg viewBox="' . esc_attr( $viewbox ) . '" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">';
foreach ( $paths as $i => $d ) {
	$svg .= '<path pathLength="1" style="--i:' . (int) $i . '" d="' . esc_attr( $d ) . '"/>';
}
$svg .= '</svg>';

printf( '<span %s>%s</span>', get_block_wrapper_attributes( array( 'class' => $classes ) ), $svg ); // phpcs:ignore WordPress.Security.EscapeOutput
