<?php
/**
 * kesto/enquiry-form
 *
 * @var array $attributes
 */
defined( 'ABSPATH' ) || exit;

$type      = 'wholesale' === ( $attributes['type'] ?? '' ) ? 'wholesale' : 'contact';
$uid       = 'kesto-' . $type . '-' . wp_unique_id();
$state     = isset( $_GET['kesto_enquiry'] ) ? sanitize_key( $_GET['kesto_enquiry'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$errors    = array();
$values    = array();
if ( 'invalid' === $state ) {
	// Look up by the page URL without the query arg (that is what the handler used as referer).
	$page_url = remove_query_arg( 'kesto_enquiry', ( is_ssl() ? 'https://' : 'http://' ) . ( $_SERVER['HTTP_HOST'] ?? '' ) . ( $_SERVER['REQUEST_URI'] ?? '' ) ); // phpcs:ignore
	$stored   = get_transient( 'kesto_enq_err_' . md5( $page_url ) );
	if ( $stored ) {
		$errors = $stored['errors'];
		$values = $stored['data'];
	}
}
$messages = array(
	'sent'    => array( 'is-success', kesto_core_success_message( $type ) ),
	'failed'  => array( 'is-error', __( 'We couldn’t save your message just now. Please try again.', 'kesto' ) ),
	'expired' => array( 'is-error', __( 'This form expired. Please send it again.', 'kesto' ) ),
	'invalid' => array( 'is-error', __( 'Please check the highlighted fields.', 'kesto' ) ),
);

$field = static function ( string $name, string $label, string $kind = 'text', bool $required = false, string $hint = '', string $autocomplete = '' ) use ( $uid, $errors, $values ) {
	$id      = "{$uid}-{$name}";
	$err     = $errors[ $name ] ?? '';
	$desc    = array();
	$hint && $desc[] = "{$id}-hint";
	$desc[] = "{$id}-error";
	$attrs   = sprintf(
		'id="%1$s" name="%2$s"%3$s%4$s aria-describedby="%5$s"%6$s',
		esc_attr( $id ),
		esc_attr( $name ),
		$required ? ' required aria-required="true"' : '',
		$err ? ' aria-invalid="true"' : '',
		esc_attr( implode( ' ', $desc ) ),
		$autocomplete ? ' autocomplete="' . esc_attr( $autocomplete ) . '"' : ''
	);
	$value   = (string) ( $values[ $name ] ?? '' );
	echo '<div class="kesto-field">';
	printf( '<label for="%s">%s%s</label>', esc_attr( $id ), esc_html( $label ), $required ? ' <span class="kesto-req" aria-hidden="true">*</span>' : '' );
	if ( $hint ) {
		printf( '<p class="kesto-field__hint" id="%s-hint">%s</p>', esc_attr( $id ), esc_html( $hint ) );
	}
	if ( 'textarea' === $kind ) {
		printf( '<textarea %s rows="6" maxlength="5000">%s</textarea>', $attrs, esc_textarea( $value ) ); // phpcs:ignore
	} else {
		printf( '<input type="%s" %s value="%s">', esc_attr( $kind ), $attrs, esc_attr( $value ) ); // phpcs:ignore
	}
	printf( '<p class="kesto-field__error" id="%s-error" data-error-for="%s">%s</p>', esc_attr( $id ), esc_attr( $name ), esc_html( $err ) );
	echo '</div>';
};
?>
<form <?php echo get_block_wrapper_attributes( array( 'class' => 'kesto-form', 'id' => 'kesto-form' ) ); // phpcs:ignore ?> method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-kesto-form data-endpoint="<?php echo esc_url( rest_url( 'kesto/v1/enquiry' ) ); ?>" novalidate>
	<input type="hidden" name="action" value="kesto_enquiry">
	<input type="hidden" name="type" value="<?php echo esc_attr( $type ); ?>">
	<?php wp_nonce_field( 'kesto_enquiry', 'kesto_enquiry_nonce' ); ?>
	<p class="kesto-form__status <?php echo esc_attr( $messages[ $state ][0] ?? '' ); ?>" role="status" aria-live="polite" data-kesto-status tabindex="-1"><?php echo esc_html( $messages[ $state ][1] ?? '' ); ?></p>
	<div class="kesto-form__row">
		<?php $field( 'name', __( 'Your name', 'kesto' ), 'text', true, '', 'name' ); ?>
		<?php $field( 'email', __( 'Email', 'kesto' ), 'email', true, '', 'email' ); ?>
	</div>
	<?php if ( 'wholesale' === $type ) : ?>
		<div class="kesto-form__row">
			<?php $field( 'business', __( 'Business or store name', 'kesto' ), 'text', true, '', 'organization' ); ?>
			<?php $field( 'location', __( 'City / state', 'kesto' ), 'text', false, '', 'address-level2' ); ?>
		</div>
		<?php $field( 'volume', __( 'Expected order volume', 'kesto' ), 'text', false, __( 'A rough idea is fine, for example “2 cases a month”.', 'kesto' ) ); ?>
		<?php $field( 'message', __( 'Tell us about your business', 'kesto' ), 'textarea', true ); ?>
	<?php else : ?>
		<?php $field( 'message', __( 'Message', 'kesto' ), 'textarea', true ); ?>
	<?php endif; ?>
	<div class="kesto-form__hp" aria-hidden="true"><label for="<?php echo esc_attr( $uid ); ?>-website">Website</label><input id="<?php echo esc_attr( $uid ); ?>-website" type="text" name="website" tabindex="-1" autocomplete="off"></div>
	<button type="submit" class="kesto-button wp-element-button"><?php echo 'wholesale' === $type ? esc_html__( 'Send wholesale enquiry', 'kesto' ) : esc_html__( 'Send message', 'kesto' ); ?></button>
	<p class="kesto-field__hint"><span aria-hidden="true">*</span> <?php esc_html_e( 'Required', 'kesto' ); ?></p>
</form>
