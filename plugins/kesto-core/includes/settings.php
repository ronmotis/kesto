<?php
/**
 * KESTO → Claims & links: benefit claims with verification status, concept mode,
 * verified social links and the enquiry inbox.
 *
 * Options:
 *   kesto_claims        array of { id, label, icon, scope (biltong|all), status (provisional|verified), note }
 *   kesto_concept_mode  bool, shows concept disclaimers (default true)
 *   kesto_social        { instagram|facebook|tiktok: { url, verified } }
 *   kesto_enquiry_email string, defaults to the site admin email
 *
 * @package kesto-core
 */

defined( 'ABSPATH' ) || exit;

function kesto_core_default_claims(): array {
	if ( function_exists( 'kesto_default_claims' ) ) {
		return kesto_default_claims();
	}
	return array(
		array( 'id' => 'protein', 'label' => 'High protein', 'icon' => 'protein', 'scope' => 'biltong', 'status' => 'provisional', 'note' => '' ),
		array( 'id' => 'grass-fed', 'label' => 'Grass-fed beef', 'icon' => 'cow', 'scope' => 'biltong', 'status' => 'provisional', 'note' => '' ),
		array( 'id' => 'gluten-free', 'label' => 'Gluten-free', 'icon' => 'gluten-free', 'scope' => 'all', 'status' => 'provisional', 'note' => '' ),
		array( 'id' => 'usa', 'label' => 'Made in the USA', 'icon' => 'star', 'scope' => 'all', 'status' => 'provisional', 'note' => '' ),
	);
}

add_action(
	'admin_menu',
	static function () {
		add_menu_page( __( 'KESTO', 'kesto' ), __( 'KESTO', 'kesto' ), 'manage_options', 'kesto', 'kesto_core_settings_page', 'dashicons-carrot', 56 );
		add_submenu_page( 'kesto', __( 'Claims & links', 'kesto' ), __( 'Claims & links', 'kesto' ), 'manage_options', 'kesto', 'kesto_core_settings_page' );
	}
);

function kesto_core_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$saved = false;
	if ( isset( $_POST['kesto_settings_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['kesto_settings_nonce'] ), 'kesto_settings' ) ) {
		$claims = array();
		$rows   = isset( $_POST['claims'] ) && is_array( $_POST['claims'] ) ? wp_unslash( $_POST['claims'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		foreach ( $rows as $row ) {
			$label = sanitize_text_field( $row['label'] ?? '' );
			if ( '' === $label ) {
				continue;
			}
			$claims[] = array(
				'id'     => sanitize_key( $row['id'] ?? sanitize_title( $label ) ) ?: sanitize_title( $label ),
				'label'  => $label,
				'icon'   => in_array( $row['icon'] ?? '', array( 'protein', 'cow', 'gluten-free', 'star' ), true ) ? $row['icon'] : 'star',
				'scope'  => ( $row['scope'] ?? '' ) === 'biltong' ? 'biltong' : 'all',
				'status' => ( $row['status'] ?? '' ) === 'verified' ? 'verified' : 'provisional',
				'note'   => sanitize_textarea_field( $row['note'] ?? '' ),
			);
		}
		update_option( 'kesto_claims', $claims );
		update_option( 'kesto_concept_mode', ! empty( $_POST['concept_mode'] ) );
		$social = array();
		foreach ( array( 'instagram', 'facebook', 'tiktok' ) as $net ) {
			$social[ $net ] = array(
				'url'      => esc_url_raw( wp_unslash( $_POST['social'][ $net ]['url'] ?? '' ) ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
				'verified' => ! empty( $_POST['social'][ $net ]['verified'] ),
			);
		}
		update_option( 'kesto_social', $social );
		$email = sanitize_email( wp_unslash( $_POST['enquiry_email'] ?? '' ) );
		update_option( 'kesto_enquiry_email', $email );
		$saved = true;
	}

	$claims = get_option( 'kesto_claims' );
	$claims = is_array( $claims ) && $claims ? $claims : kesto_core_default_claims();
	$claims[] = array( 'id' => '', 'label' => '', 'icon' => 'star', 'scope' => 'all', 'status' => 'provisional', 'note' => '' ); // blank row to add one.
	$social = (array) get_option( 'kesto_social', array() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'KESTO — claims & links', 'kesto' ); ?></h1>
		<?php if ( $saved ) : ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Saved.', 'kesto' ); ?></p></div>
		<?php endif; ?>
		<p><?php esc_html_e( 'Benefit claims appear in the homepage strip and on product pages. Keep a claim “provisional” until you hold evidence for it (nutrition panel, supplier documents, certification). “Biltong only” claims are hidden on non-beef products such as Magunanzi.', 'kesto' ); ?></p>
		<form method="post">
			<?php wp_nonce_field( 'kesto_settings', 'kesto_settings_nonce' ); ?>
			<table class="widefat striped" style="max-width:1100px">
				<thead><tr><th><?php esc_html_e( 'Label', 'kesto' ); ?></th><th><?php esc_html_e( 'Icon', 'kesto' ); ?></th><th><?php esc_html_e( 'Applies to', 'kesto' ); ?></th><th><?php esc_html_e( 'Status', 'kesto' ); ?></th><th><?php esc_html_e( 'Evidence / note', 'kesto' ); ?></th></tr></thead>
				<tbody>
				<?php foreach ( $claims as $i => $c ) : ?>
					<tr>
						<td><input type="hidden" name="claims[<?php echo (int) $i; ?>][id]" value="<?php echo esc_attr( $c['id'] ); ?>"><input class="regular-text" name="claims[<?php echo (int) $i; ?>][label]" value="<?php echo esc_attr( $c['label'] ); ?>" aria-label="<?php esc_attr_e( 'Claim label', 'kesto' ); ?>" placeholder="<?php esc_attr_e( 'Add a claim…', 'kesto' ); ?>"></td>
						<td><select name="claims[<?php echo (int) $i; ?>][icon]" aria-label="<?php esc_attr_e( 'Icon', 'kesto' ); ?>"><?php foreach ( array( 'protein', 'cow', 'gluten-free', 'star' ) as $ic ) : ?><option <?php selected( $c['icon'], $ic ); ?>><?php echo esc_html( $ic ); ?></option><?php endforeach; ?></select></td>
						<td><select name="claims[<?php echo (int) $i; ?>][scope]" aria-label="<?php esc_attr_e( 'Applies to', 'kesto' ); ?>"><option value="all" <?php selected( $c['scope'], 'all' ); ?>><?php esc_html_e( 'Whole range', 'kesto' ); ?></option><option value="biltong" <?php selected( $c['scope'], 'biltong' ); ?>><?php esc_html_e( 'Biltong only', 'kesto' ); ?></option></select></td>
						<td><select name="claims[<?php echo (int) $i; ?>][status]" aria-label="<?php esc_attr_e( 'Verification status', 'kesto' ); ?>"><option value="provisional" <?php selected( $c['status'], 'provisional' ); ?>><?php esc_html_e( 'Provisional', 'kesto' ); ?></option><option value="verified" <?php selected( $c['status'], 'verified' ); ?>><?php esc_html_e( 'Verified', 'kesto' ); ?></option></select></td>
						<td><textarea rows="2" cols="40" name="claims[<?php echo (int) $i; ?>][note]" aria-label="<?php esc_attr_e( 'Evidence or note', 'kesto' ); ?>"><?php echo esc_textarea( $c['note'] ?? '' ); ?></textarea></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<h2><?php esc_html_e( 'Concept mode', 'kesto' ); ?></h2>
			<label><input type="checkbox" name="concept_mode" value="1" <?php checked( (bool) get_option( 'kesto_concept_mode', true ) ); ?>> <?php esc_html_e( 'Show concept disclaimers (claims, demo prices, imagery and font pending verification). Turn off only when everything is verified.', 'kesto' ); ?></label>

			<h2><?php esc_html_e( 'Social links', 'kesto' ); ?></h2>
			<p><?php esc_html_e( 'A link appears in the footer only when it has a URL and is ticked as verified.', 'kesto' ); ?></p>
			<table class="form-table">
				<?php foreach ( array( 'instagram', 'facebook', 'tiktok' ) as $net ) : ?>
					<tr><th scope="row"><label for="kesto-social-<?php echo esc_attr( $net ); ?>"><?php echo esc_html( ucfirst( $net ) ); ?></label></th>
					<td><input id="kesto-social-<?php echo esc_attr( $net ); ?>" class="regular-text" type="url" name="social[<?php echo esc_attr( $net ); ?>][url]" value="<?php echo esc_attr( $social[ $net ]['url'] ?? '' ); ?>">
					<label><input type="checkbox" name="social[<?php echo esc_attr( $net ); ?>][verified]" value="1" <?php checked( ! empty( $social[ $net ]['verified'] ) ); ?>> <?php esc_html_e( 'Verified', 'kesto' ); ?></label></td></tr>
				<?php endforeach; ?>
			</table>

			<h2><?php esc_html_e( 'Enquiries', 'kesto' ); ?></h2>
			<table class="form-table"><tr><th scope="row"><label for="kesto-enquiry-email"><?php esc_html_e( 'Send enquiries to', 'kesto' ); ?></label></th>
			<td><input id="kesto-enquiry-email" class="regular-text" type="email" name="enquiry_email" value="<?php echo esc_attr( get_option( 'kesto_enquiry_email', '' ) ); ?>" placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>">
			<p class="description"><?php esc_html_e( 'Every enquiry is also stored under KESTO → Enquiries, so nothing is lost if email delivery fails.', 'kesto' ); ?></p></td></tr></table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
