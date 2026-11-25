<?php
/**
 * Title: Home — sharing is optional
 * Slug: kesto/home-community
 * Categories: kesto
 * Description: Mango closing band with overhead food photography and the final shopping action.
 */

defined( 'ABSPATH' ) || exit;

$photo = array(
	'desktopUrl'    => kesto_img( 'community-desktop-1600.webp' ),
	'desktopSrcset' => kesto_srcset( 'community-desktop', array( 960, 1600, 2400 ) ),
	'desktopWidth'  => 1600,
	'desktopHeight' => 681,
	'mobileUrl'     => kesto_img( 'community-mobile-800.webp' ),
	'mobileSrcset'  => kesto_srcset( 'community-mobile', array( 480, 800, 1200 ) ),
	'mobileWidth'   => 800,
	'mobileHeight'  => 800,
	'sizes'         => '(max-width: 781px) 100vw, 70vw',
	'alt'           => __( 'Overhead view of a sunny table: hands reach into a bowl of sliced biltong and a bowl of roasted corn and nuts, with limes and gingham cloth.', 'kesto' ),
	'className'     => 'kesto-community__photo',
);
$pouch = static function ( string $key, string $class, string $alt ) {
	return wp_json_encode(
		array(
			'desktopUrl'    => kesto_img( "pouch-{$key}-400.webp" ),
			'desktopSrcset' => kesto_srcset( "pouch-{$key}", array( 240, 400, 640 ) ),
			'desktopWidth'  => 400,
			'desktopHeight' => array( 'original' => 605, 'chilli' => 596, 'trail' => 593 )[ $key ],
			'sizes'         => '(max-width: 781px) 26vw, 11vw',
			'alt'           => $alt,
			'className'     => $class,
		)
	);
};
?>
<!-- wp:group {"tagName":"section","align":"full","className":"kesto-community","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull kesto-community"><!-- wp:group {"className":"kesto-community__copy","layout":{"type":"default"}} -->
<div class="wp-block-group kesto-community__copy"><!-- wp:group {"className":"kesto-community__title","layout":{"type":"default"}} -->
<div class="wp-block-group kesto-community__title"><!-- wp:heading {"className":"kesto-display kesto-community__headline"} -->
<h2 class="wp-block-heading kesto-display kesto-community__headline">Sharing is optional. Good company isn’t.</h2>
<!-- /wp:heading -->

<!-- wp:kesto/arrow {"variant":"scribble","color":"tomato","className":"kesto-community__underline"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"kesto-community__cta","layout":{"type":"default"}} -->
<div class="wp-block-group kesto-community__cta"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"kesto-btn kesto-btn--tomato kesto-btn--arrow"} -->
<div class="wp-block-button kesto-btn kesto-btn--tomato kesto-btn--arrow"><a class="wp-block-button__link wp-element-button" href="/shop/">Shop the snacks</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:kesto/arrow {"variant":"swoop","color":"tomato","className":"kesto-community__arrow"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"kesto-community__media","layout":{"type":"default"}} -->
<div class="wp-block-group kesto-community__media"><!-- wp:kesto/picture <?php echo wp_json_encode( $photo ); ?> /-->

<!-- wp:kesto/picture <?php echo $pouch( 'original', 'kesto-community__pouch kesto-community__pouch--a kesto-depth', __( 'KESTO Original Biltong pouch', 'kesto' ) ); ?> /-->

<!-- wp:kesto/picture <?php echo $pouch( 'chilli', 'kesto-community__pouch kesto-community__pouch--b kesto-depth', __( 'KESTO Chilli Biltong pouch', 'kesto' ) ); ?> /--></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
