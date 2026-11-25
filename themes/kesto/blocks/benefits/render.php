<?php
/**
 * kesto/benefits — mango strip of claims. Biltong-only claims carry a marker that the
 * shopping section's footnote explains; provisional claims are flagged while concept mode is on.
 *
 * @var array $attributes
 */
defined( 'ABSPATH' ) || exit;

$claims      = kesto_claims();
$provisional = array_filter( $claims, static fn( $c ) => 'verified' !== ( $c['status'] ?? '' ) );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'kesto-benefits' ) ); // phpcs:ignore ?> aria-labelledby="kesto-benefits-title">
	<h2 id="kesto-benefits-title" class="screen-reader-text"><?php esc_html_e( 'Why KESTO', 'kesto' ); ?></h2>
	<ul class="kesto-benefits__list" role="list">
		<?php foreach ( $claims as $claim ) : ?>
			<li class="kesto-benefits__item" data-status="<?php echo esc_attr( $claim['status'] ?? 'provisional' ); ?>">
				<?php echo kesto_icon( $claim['icon'] ?? 'star' ); // phpcs:ignore ?>
				<span class="kesto-benefits__label"><?php echo esc_html( $claim['label'] ); ?><?php if ( 'biltong' === ( $claim['scope'] ?? '' ) ) : ?><sup class="kesto-benefits__mark" aria-hidden="true">*</sup><span class="screen-reader-text"><?php esc_html_e( '(biltong only)', 'kesto' ); ?></span><?php endif; ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
	$has_beef_scope = (bool) array_filter( $claims, static fn( $c ) => 'biltong' === ( $c['scope'] ?? '' ) );
	$notes          = array();
	if ( $has_beef_scope ) {
		$notes[] = __( '*Beef claims apply to biltong', 'kesto' );
	}
	if ( $provisional && kesto_concept_mode() ) {
		$notes[] = __( 'Concept claims — pending verification', 'kesto' );
	}
	?>
	<?php if ( $notes ) : ?>
		<p class="kesto-benefits__status"><?php echo esc_html( implode( ' · ', $notes ) ); ?></p>
	<?php endif; ?>
</section>
