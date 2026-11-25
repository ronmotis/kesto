<?php
/**
 * kesto/bag — "Bag (n)". Count refreshes from the Store API in kesto.js.
 *
 * @var array $attributes
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_cart_url' ) ) {
	return;
}
$count = 0;
if ( ! is_admin() && function_exists( 'WC' ) && WC()->cart ) {
	$count = (int) WC()->cart->get_cart_contents_count();
}
$label = $attributes['label'] ?: __( 'Bag', 'kesto' );
printf(
	'<a %1$s href="%2$s" data-kesto-bag>%3$s<span class="kesto-bag__label">%4$s</span> <span class="kesto-bag__count" aria-hidden="true">(<span data-kesto-bag-count>%5$d</span>)</span><span class="screen-reader-text" data-kesto-bag-sr>%6$s</span></a>',
	get_block_wrapper_attributes( array( 'class' => 'kesto-bag' ) ), // phpcs:ignore WordPress.Security.EscapeOutput
	esc_url( wc_get_cart_url() ),
	kesto_icon( 'bag' ), // phpcs:ignore WordPress.Security.EscapeOutput
	esc_html( $label ),
	$count,
	/* translators: %d: number of items */
	esc_html( sprintf( _n( '%d item', '%d items', $count, 'kesto' ), $count ) )
);
