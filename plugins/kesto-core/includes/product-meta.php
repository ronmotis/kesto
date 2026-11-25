<?php
/**
 * Product presentation fields shown on the homepage tiles and product pages.
 *
 * @package kesto-core
 */

defined( 'ABSPATH' ) || exit;

const KESTO_PRODUCT_META = array( '_kesto_tile', '_kesto_food', '_kesto_tagline', '_kesto_demo' );

add_action(
	'init',
	static function () {
		foreach ( KESTO_PRODUCT_META as $key ) {
			register_post_meta(
				'product',
				$key,
				array(
					'type'          => '_kesto_demo' === $key ? 'boolean' : 'string',
					'single'        => true,
					'show_in_rest'  => true,
					'auth_callback' => static fn() => current_user_can( 'edit_products' ),
				)
			);
		}
	}
);

add_action(
	'add_meta_boxes_product',
	static function () {
		add_meta_box( 'kesto-tile', __( 'KESTO tile', 'kesto' ), 'kesto_core_product_box', 'product', 'side' );
	}
);

function kesto_core_product_box( WP_Post $post ): void {
	wp_nonce_field( 'kesto_product_meta', 'kesto_product_meta_nonce' );
	$tile = get_post_meta( $post->ID, '_kesto_tile', true ) ?: 'cream';
	$food = get_post_meta( $post->ID, '_kesto_food', true );
	?>
	<p><label for="kesto-tile"><strong><?php esc_html_e( 'Tile colour', 'kesto' ); ?></strong></label><br>
	<select id="kesto-tile" name="_kesto_tile" style="width:100%">
		<?php foreach ( array( 'orange' => __( 'Orange (Original)', 'kesto' ), 'red' => __( 'Red (Chilli)', 'kesto' ), 'cream' => __( 'Cream', 'kesto' ) ) as $v => $l ) : ?>
			<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $tile, $v ); ?>><?php echo esc_html( $l ); ?></option>
		<?php endforeach; ?>
	</select></p>
	<p><label for="kesto-food"><strong><?php esc_html_e( 'Food layer', 'kesto' ); ?></strong></label><br>
	<select id="kesto-food" name="_kesto_food" style="width:100%">
		<?php foreach ( array( '' => __( 'None', 'kesto' ), 'biltong' => __( 'Biltong strips', 'kesto' ), 'chilli' => __( 'Chilli biltong + peppers', 'kesto' ), 'trailmix' => __( 'Corn & nuts', 'kesto' ) ) as $v => $l ) : ?>
			<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $food, $v ); ?>><?php echo esc_html( $l ); ?></option>
		<?php endforeach; ?>
	</select></p>
	<p><label for="kesto-tagline"><strong><?php esc_html_e( 'Short descriptor', 'kesto' ); ?></strong></label><br>
	<input id="kesto-tagline" name="_kesto_tagline" type="text" style="width:100%" value="<?php echo esc_attr( get_post_meta( $post->ID, '_kesto_tagline', true ) ); ?>"></p>
	<p><label><input type="checkbox" name="_kesto_demo" value="1" <?php checked( (bool) get_post_meta( $post->ID, '_kesto_demo', true ) ); ?>> <?php esc_html_e( 'Price is a demo value', 'kesto' ); ?></label></p>
	<?php
}

add_action(
	'save_post_product',
	static function ( int $post_id ) {
		if ( ! isset( $_POST['kesto_product_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['kesto_product_meta_nonce'] ), 'kesto_product_meta' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$tile = sanitize_key( $_POST['_kesto_tile'] ?? 'cream' );
		$food = sanitize_key( $_POST['_kesto_food'] ?? '' );
		update_post_meta( $post_id, '_kesto_tile', in_array( $tile, array( 'orange', 'red', 'cream' ), true ) ? $tile : 'cream' );
		update_post_meta( $post_id, '_kesto_food', in_array( $food, array( 'biltong', 'chilli', 'trailmix' ), true ) ? $food : '' );
		update_post_meta( $post_id, '_kesto_tagline', sanitize_text_field( wp_unslash( $_POST['_kesto_tagline'] ?? '' ) ) );
		update_post_meta( $post_id, '_kesto_demo', ! empty( $_POST['_kesto_demo'] ) ? 1 : 0 );
	}
);
