<?php
/**
 * kesto/featured-products — catalog data always comes from WooCommerce.
 *
 * Per-product presentation meta (set in the product's "KESTO tile" panel, kesto-core):
 *   _kesto_tile     orange | red | cream
 *   _kesto_food     biltong | chilli | trailmix | ''   (decorative food layer)
 *   _kesto_tagline  short descriptor, e.g. "Roasted corn & nuts"
 *   _kesto_demo     1 when the price is a demo value
 *
 * @var array $attributes
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_products' ) ) {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		echo '<p>' . esc_html__( 'Activate WooCommerce to show featured snacks.', 'kesto' ) . '</p>';
	}
	return;
}

$count    = max( 1, min( 6, (int) ( $attributes['count'] ?? 3 ) ) );
$products = wc_get_products(
	array(
		'status'   => 'publish',
		'featured' => true,
		'limit'    => $count,
		'orderby'  => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'visibility' => 'catalog',
	)
);
if ( ! $products ) {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		echo '<p>' . esc_html__( 'No featured products yet. Mark products as Featured in WooCommerce → Products.', 'kesto' ) . '</p>';
	}
	return;
}

$has_biltong_claims = (bool) array_filter( kesto_claims(), static fn( $c ) => 'biltong' === ( $c['scope'] ?? '' ) );
$any_demo           = false;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'kesto-products' ) ); // phpcs:ignore ?>>
	<ul class="kesto-products__list" role="list">
		<?php
		foreach ( $products as $i => $product ) :
			$id      = $product->get_id();
			$tile    = sanitize_html_class( get_post_meta( $id, '_kesto_tile', true ) ?: 'cream' );
			$food    = sanitize_key( get_post_meta( $id, '_kesto_food', true ) );
			$tagline = get_post_meta( $id, '_kesto_tagline', true );
			$demo    = (bool) get_post_meta( $id, '_kesto_demo', true );
			$any_demo = $any_demo || $demo;
			$link    = $product->get_permalink();
			$name    = $product->get_name();
			$can_add = $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock();
			$img     = $product->get_image_id()
				? wp_get_attachment_image(
					$product->get_image_id(),
					'large',
					false,
					array(
						'class'   => 'kesto-product__pouch',
						'sizes'   => '(max-width: 781px) 40vw, 13vw',
						'loading' => 'lazy',
						'alt'     => '',
					)
				)
				: '';
			?>
			<li class="kesto-product kesto-product--<?php echo esc_attr( $tile ); ?>" style="--i:<?php echo (int) $i; ?>">
				<a class="kesto-product__media" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
					<?php if ( $img ) : ?>
						<?php echo $img; // phpcs:ignore ?>
					<?php else : ?>
						<span class="kesto-product__pending"><?php echo kesto_icon( 'bag' ); // phpcs:ignore ?><span><?php esc_html_e( 'Packshot coming soon', 'kesto' ); ?></span></span>
					<?php endif; ?>
					<?php if ( $food && in_array( $food, array( 'biltong', 'chilli', 'trailmix' ), true ) ) : ?>
						<img class="kesto-product__food" src="<?php echo esc_url( kesto_img( "food-{$food}-560.webp" ) ); ?>" srcset="<?php echo esc_attr( kesto_srcset( "food-{$food}", array( 320, 560 ) ) ); ?>" sizes="(max-width: 781px) 40vw, 12vw" alt="" width="560" height="<?php echo (int) ( array( 'biltong' => 329, 'chilli' => 342, 'trailmix' => 340 )[ $food ] ); ?>" loading="lazy" decoding="async">
					<?php endif; ?>
				</a>
				<h3 class="kesto-product__name"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $name ); ?></a></h3>
				<p class="kesto-product__meta">
					<?php if ( $tagline ) : ?>
						<span><?php echo esc_html( $tagline ); ?></span>
					<?php endif; ?>
					<span class="kesto-product__price"><?php echo wp_kses_post( $product->get_price_html() ); ?><?php if ( $demo ) : ?> <abbr class="kesto-demo" title="<?php esc_attr_e( 'Demo price — not final', 'kesto' ); ?>"><?php esc_html_e( 'demo', 'kesto' ); ?></abbr><?php endif; ?></span>
				</p>
				<div class="kesto-product__actions">
					<a class="kesto-product__shop" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'Shop', 'kesto' ); ?><span class="screen-reader-text"> <?php echo esc_html( $name ); ?></span><?php echo kesto_icon( 'arrow-ne' ); // phpcs:ignore ?></a>
					<?php if ( $can_add ) : ?>
						<a class="kesto-add" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-kesto-add="<?php echo (int) $id; ?>" rel="nofollow">
							<?php echo kesto_icon( 'plus' ); // phpcs:ignore ?><span class="kesto-add__text"><?php esc_html_e( 'Add to bag', 'kesto' ); ?></span><span class="screen-reader-text"> — <?php echo esc_html( $name ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ( ! empty( $attributes['showFootnote'] ) && ( $has_biltong_claims || ( $any_demo && kesto_concept_mode() ) ) ) : ?>
		<p class="kesto-products__note">
			<?php if ( $has_biltong_claims ) : ?><span><?php esc_html_e( '*Beef claims apply to biltong.', 'kesto' ); ?></span><?php endif; ?>
			<?php if ( $any_demo && kesto_concept_mode() ) : ?><span><?php esc_html_e( 'Prices shown are demo values.', 'kesto' ); ?></span><?php endif; ?>
		</p>
	<?php endif; ?>
	<p class="kesto-live screen-reader-text" aria-live="polite" data-kesto-live></p>
</div>
