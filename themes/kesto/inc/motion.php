<?php
/**
 * Server-side helpers for the motion layer.
 *
 * Headlines with the `kesto-kinetic` class are split into words on the server,
 * so the word-by-word entrance never flashes the unsplit text first. The words
 * stay plain text in the heading: screen readers and the editor see the same copy.
 *
 * @package kesto
 */

defined( 'ABSPATH' ) || exit;

/**
 * Wrap each word of a heading's text nodes in a masked span.
 *
 * @param string $html Heading inner HTML (may contain <br>, <em>, <strong>, links).
 */
function kesto_split_words( string $html ): string {
	$index = 0;
	$parts = preg_split( '/(<[^>]+>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
	$out   = '';
	foreach ( $parts as $part ) {
		if ( '<' === $part[0] ) {
			$out .= $part;
			continue;
		}
		$out .= preg_replace_callback(
			'/[^\s]+/u',
			static function ( $m ) use ( &$index ) {
				return '<span class="kw"><span class="kw__i" style="--w:' . ( $index++ ) . '">' . $m[0] . '</span></span>';
			},
			$part
		);
	}
	return $out;
}

add_filter(
	'render_block_core/heading',
	static function ( string $content, array $block ): string {
		$class = $block['attrs']['className'] ?? '';
		if ( false === strpos( $class, 'kesto-kinetic' ) || is_admin() || wp_is_json_request() ) {
			return $content;
		}
		return preg_replace_callback(
			'#^(\s*<h[1-6][^>]*>)(.*)(</h[1-6]>\s*)$#s',
			static fn( $m ) => $m[1] . kesto_split_words( $m[2] ) . $m[3],
			$content
		);
	},
	10,
	2
);
