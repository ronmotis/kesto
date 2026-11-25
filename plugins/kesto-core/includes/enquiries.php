<?php
/**
 * Contact & wholesale enquiries.
 *
 * Submissions are validated, stored as a private `kesto_enquiry` post, then emailed.
 * The form reports success only after the enquiry is stored (the receiving service
 * accepted it). Works without JavaScript via admin-post.php; with JS it uses REST.
 *
 * @package kesto-core
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	static function () {
		register_post_type(
			'kesto_enquiry',
			array(
				'label'           => __( 'Enquiries', 'kesto' ),
				'labels'          => array( 'name' => __( 'Enquiries', 'kesto' ), 'singular_name' => __( 'Enquiry', 'kesto' ) ),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => 'kesto',
				'supports'        => array( 'title', 'editor', 'custom-fields' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
			)
		);
		register_block_type( KESTO_CORE_DIR . '/blocks/enquiry-form' );
	}
);

/**
 * Validate a submission. Returns [ clean_data, errors ].
 */
function kesto_core_validate_enquiry( array $in ): array {
	$type   = ( $in['type'] ?? '' ) === 'wholesale' ? 'wholesale' : 'contact';
	$data   = array(
		'type'     => $type,
		'name'     => sanitize_text_field( $in['name'] ?? '' ),
		'email'    => sanitize_email( $in['email'] ?? '' ),
		'business' => sanitize_text_field( $in['business'] ?? '' ),
		'location' => sanitize_text_field( $in['location'] ?? '' ),
		'volume'   => sanitize_text_field( $in['volume'] ?? '' ),
		'message'  => sanitize_textarea_field( $in['message'] ?? '' ),
	);
	$errors = array();
	if ( '' === $data['name'] ) {
		$errors['name'] = __( 'Please tell us your name.', 'kesto' );
	}
	if ( ! is_email( $data['email'] ) ) {
		$errors['email'] = __( 'Please enter an email address we can reply to, like name@example.com.', 'kesto' );
	}
	if ( 'wholesale' === $type && '' === $data['business'] ) {
		$errors['business'] = __( 'Please add your business or store name.', 'kesto' );
	}
	if ( mb_strlen( $data['message'] ) < 5 ) {
		$errors['message'] = __( 'Please add a short message.', 'kesto' );
	}
	if ( mb_strlen( $data['message'] ) > 5000 ) {
		$errors['message'] = __( 'That message is too long — please keep it under 5,000 characters.', 'kesto' );
	}
	return array( $data, $errors );
}

/**
 * Store + notify. Returns WP_Error or the stored post ID.
 */
function kesto_core_store_enquiry( array $data ) {
	// Honeypot and simple rate limit (5 per 10 minutes per IP hash).
	$ip_hash = md5( ( $_SERVER['REMOTE_ADDR'] ?? '' ) . wp_salt() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$key     = 'kesto_enq_' . $ip_hash;
	$count   = (int) get_transient( $key );
	if ( $count >= 5 ) {
		return new WP_Error( 'rate_limited', __( 'You’ve sent several messages in a short time. Please wait a few minutes and try again.', 'kesto' ), array( 'status' => 429 ) );
	}

	$title = sprintf( '%s — %s', 'wholesale' === $data['type'] ? __( 'Wholesale', 'kesto' ) : __( 'Contact', 'kesto' ), $data['name'] );
	$id    = wp_insert_post(
		array(
			'post_type'    => 'kesto_enquiry',
			'post_status'  => 'private',
			'post_title'   => $title,
			'post_content' => $data['message'],
			'meta_input'   => array(
				'_kesto_type'     => $data['type'],
				'_kesto_email'    => $data['email'],
				'_kesto_business' => $data['business'],
				'_kesto_location' => $data['location'],
				'_kesto_volume'   => $data['volume'],
			),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return new WP_Error( 'store_failed', __( 'We couldn’t save your message just now. Please try again, or email us directly.', 'kesto' ), array( 'status' => 500 ) );
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$to   = get_option( 'kesto_enquiry_email' ) ?: get_option( 'admin_email' );
	$body = "{$title}\n\nEmail: {$data['email']}\n";
	foreach ( array( 'business' => 'Business', 'location' => 'Location', 'volume' => 'Expected volume' ) as $k => $label ) {
		if ( $data[ $k ] ) {
			$body .= "{$label}: {$data[ $k ]}\n";
		}
	}
	$body .= "\n{$data['message']}\n\n" . admin_url( "post.php?post={$id}&action=edit" );
	$sent = wp_mail( $to, '[KESTO] ' . $title, $body, array( 'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>' ) );
	update_post_meta( $id, '_kesto_mailed', $sent ? 1 : 0 );
	return $id;
}

add_action(
	'rest_api_init',
	static function () {
		register_rest_route(
			'kesto/v1',
			'/enquiry',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => static function ( WP_REST_Request $req ) {
					$params = $req->get_params();
					if ( ! empty( $params['website'] ) ) { // Honeypot: pretend success, store nothing.
						return new WP_REST_Response( array( 'ok' => true ), 200 );
					}
					[ $data, $errors ] = kesto_core_validate_enquiry( $params );
					if ( $errors ) {
						return new WP_REST_Response( array( 'ok' => false, 'errors' => $errors, 'message' => __( 'Please check the highlighted fields.', 'kesto' ) ), 422 );
					}
					$result = kesto_core_store_enquiry( $data );
					if ( is_wp_error( $result ) ) {
						return new WP_REST_Response( array( 'ok' => false, 'message' => $result->get_error_message() ), $result->get_error_data()['status'] ?? 500 );
					}
					return new WP_REST_Response( array( 'ok' => true, 'message' => kesto_core_success_message( $data['type'] ) ), 201 );
				},
			)
		);
	}
);

function kesto_core_success_message( string $type ): string {
	return 'wholesale' === $type
		? __( 'Thanks — your wholesale enquiry has been received. We’ll reply by email.', 'kesto' )
		: __( 'Thanks — your message has been received. We’ll reply by email.', 'kesto' );
}

/* No-JS fallback. */
function kesto_core_handle_post(): void {
	$back = remove_query_arg( 'kesto_enquiry', wp_get_referer() ?: home_url( '/' ) );
	if ( ! isset( $_POST['kesto_enquiry_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['kesto_enquiry_nonce'] ), 'kesto_enquiry' ) ) {
		wp_safe_redirect( add_query_arg( 'kesto_enquiry', 'expired', $back ) . '#kesto-form' );
		exit;
	}
	if ( ! empty( $_POST['website'] ) ) {
		wp_safe_redirect( add_query_arg( 'kesto_enquiry', 'sent', $back ) . '#kesto-form' );
		exit;
	}
	[ $data, $errors ] = kesto_core_validate_enquiry( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	if ( $errors ) {
		set_transient( 'kesto_enq_err_' . md5( $back ), array( 'errors' => $errors, 'data' => $data ), 120 );
		wp_safe_redirect( add_query_arg( 'kesto_enquiry', 'invalid', $back ) . '#kesto-form' );
		exit;
	}
	$result = kesto_core_store_enquiry( $data );
	wp_safe_redirect( add_query_arg( 'kesto_enquiry', is_wp_error( $result ) ? 'failed' : 'sent', $back ) . '#kesto-form' );
	exit;
}
add_action( 'admin_post_nopriv_kesto_enquiry', 'kesto_core_handle_post' );
add_action( 'admin_post_kesto_enquiry', 'kesto_core_handle_post' );

/* Admin list columns. */
add_filter(
	'manage_kesto_enquiry_posts_columns',
	static fn( $c ) => array_merge( $c, array( 'kesto_type' => __( 'Type', 'kesto' ), 'kesto_email' => __( 'Email', 'kesto' ), 'kesto_mailed' => __( 'Emailed', 'kesto' ) ) )
);
add_action(
	'manage_kesto_enquiry_posts_custom_column',
	static function ( $col, $id ) {
		if ( 'kesto_type' === $col ) {
			echo esc_html( ucfirst( (string) get_post_meta( $id, '_kesto_type', true ) ) );
		} elseif ( 'kesto_email' === $col ) {
			echo esc_html( (string) get_post_meta( $id, '_kesto_email', true ) );
		} elseif ( 'kesto_mailed' === $col ) {
			echo get_post_meta( $id, '_kesto_mailed', true ) ? esc_html__( 'Yes', 'kesto' ) : esc_html__( 'No — check mail setup', 'kesto' );
		}
	},
	10,
	2
);
