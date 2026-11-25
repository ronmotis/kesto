<?php
/**
 * kesto/product-notes
 *
 * @var array    $attributes
 * @var WP_Block $block
 */
defined( 'ABSPATH' ) || exit;

$product_id = $block->context['postId'] ?? get_the_ID();
$product    = function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
if ( ! $product ) {
	return;
}
$is_biltong = has_term( 'biltong', 'product_cat', $product_id );
$claims     = array_filter(
	kesto_claims(),
	static fn( $c ) => 'all' === ( $c['scope'] ?? 'all' ) || ( 'biltong' === ( $c['scope'] ?? '' ) && $is_biltong )
);
$tagline     = get_post_meta( $product_id, '_kesto_tagline', true );
$demo        = (bool) get_post_meta( $product_id, '_kesto_demo', true );
$provisional = (bool) array_filter( $claims, static fn( $c ) => 'verified' !== ( $c['status'] ?? '' ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'kesto-notes' ) ); // phpcs:ignore ?>>
	<?php if ( $tagline ) : ?>
		<p class="kesto-notes__tagline"><?php echo esc_html( $tagline ); ?></p>
	<?php endif; ?>
	<?php if ( $claims ) : ?>
		<ul class="kesto-notes__claims" role="list" aria-label="<?php esc_attr_e( 'Product benefits', 'kesto' ); ?>">
			<?php foreach ( $claims as $claim ) : ?>
				<li><?php echo kesto_icon( $claim['icon'] ?? 'star' ); // phpcs:ignore ?><?php echo esc_html( $claim['label'] ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<?php if ( kesto_concept_mode() && ( $provisional || $demo ) ) : ?>
		<p class="kesto-notes__concept">
			<?php
			echo esc_html(
				trim(
					( $demo ? __( 'Demo price.', 'kesto' ) . ' ' : '' ) .
					( $provisional ? __( 'Claims are pending verification.', 'kesto' ) : '' )
				)
			);
			?>
		</p>
	<?php endif; ?>
	<?php if ( $is_biltong ) : ?>
		<div class="kesto-notes__explain">
			<p><strong><?php esc_html_e( 'New to biltong?', 'kesto' ); ?></strong> <?php esc_html_e( 'Biltong is seasoned beef that is air-dried and sliced. A Southern African favourite, ready for your next snack.', 'kesto' ); ?></p>
			<p><a href="<?php echo esc_url( home_url( '/what-is-biltong/' ) ); ?>"><?php esc_html_e( 'Meet biltong', 'kesto' ); ?></a></p>
		</div>
	<?php endif; ?>
</div>
