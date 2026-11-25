<?php
/**
 * kesto/concept-note
 *
 * @var array $attributes
 */
defined( 'ABSPATH' ) || exit;
if ( ! kesto_concept_mode() ) {
	return;
}
printf( '<p %s>%s</p>', get_block_wrapper_attributes( array( 'class' => 'kesto-concept-note' ) ), esc_html( $attributes['text'] ) ); // phpcs:ignore
