<?php
/**
 * kesto/social — outputs nothing until a URL is entered and verified.
 *
 * @var array $attributes
 */
defined( 'ABSPATH' ) || exit;
$links = get_option( 'kesto_social', array() );
$out   = '';
foreach ( (array) $links as $network => $data ) {
	if ( empty( $data['url'] ) || empty( $data['verified'] ) ) {
		continue;
	}
	$out .= sprintf( '<li><a href="%s" rel="me noopener" target="_blank">%s<span class="screen-reader-text"> %s</span></a></li>', esc_url( $data['url'] ), esc_html( ucfirst( $network ) ), esc_html__( '(opens in a new tab)', 'kesto' ) );
}
if ( $out ) {
	printf( '<ul %s role="list">%s</ul>', get_block_wrapper_attributes( array( 'class' => 'kesto-social' ) ), $out ); // phpcs:ignore
}
