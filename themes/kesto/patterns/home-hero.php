<?php
/**
 * Title: Home — hero
 * Slug: kesto/home-hero
 * Categories: kesto
 * Description: Asymmetric hero: headline and action left, warm campaign photo with real pouches right.
 * Inserter: yes
 */

defined( 'ABSPATH' ) || exit;

$photo = array(
	'desktopUrl'    => kesto_img( 'hero-desktop-1920.webp' ),
	'desktopSrcset' => kesto_srcset( 'hero-desktop', array( 960, 1440, 1920, 2560 ) ),
	'desktopWidth'  => 1920,
	'desktopHeight' => 1080,
	'mobileUrl'     => kesto_img( 'hero-mobile-800.webp' ),
	'mobileSrcset'  => kesto_srcset( 'hero-mobile', array( 480, 800, 1200 ) ),
	'mobileWidth'   => 800,
	'mobileHeight'  => 1000,
	'sizes'         => '(max-width: 599px) 100vw, 72vw',
	'alt'           => __( 'A woman laughs as she bites into a slice of biltong at a sunny outdoor table with friends, a bowl of sliced biltong in front of her.', 'kesto' ),
	'focal'         => '42% 50%',
	'priority'      => true,
	'className'     => 'kesto-hero__photo',
);
$pouch = static function ( string $key, string $class, string $alt ) {
	return wp_json_encode(
		array(
			'desktopUrl'    => kesto_img( "pouch-{$key}-640.webp" ),
			'desktopSrcset' => kesto_srcset( "pouch-{$key}", array( 240, 400, 640, 960 ) ),
			'desktopWidth'  => 640,
			'desktopHeight' => 966,
			'sizes'         => '(max-width: 781px) 30vw, 12vw',
			'alt'           => $alt,
			'priority'      => true,
			'className'     => $class,
		)
	);
};
?>
<!-- wp:group {"tagName":"section","align":"full","className":"kesto-hero","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull kesto-hero"><!-- wp:group {"className":"kesto-hero__copy","layout":{"type":"default"}} -->
<div class="wp-block-group kesto-hero__copy"><!-- wp:group {"className":"kesto-hero__title","layout":{"type":"default"}} -->
<div class="wp-block-group kesto-hero__title"><!-- wp:heading {"level":1,"className":"kesto-display kesto-hero__headline"} -->
<h1 class="wp-block-heading kesto-display kesto-hero__headline">Good food. Great mood.</h1>
<!-- /wp:heading -->

<!-- wp:kesto/arrow {"variant":"scribble","color":"mango","className":"kesto-hero__underline"} /--></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"kesto-hero__lede"} -->
<p class="kesto-hero__lede">Southern African biltong.<br>Big on protein. Bigger on flavour.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"kesto-hero__cta","layout":{"type":"default"}} -->
<div class="wp-block-group kesto-hero__cta"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"kesto-btn kesto-btn--mango kesto-btn--arrow"} -->
<div class="wp-block-button kesto-btn kesto-btn--mango kesto-btn--arrow"><a class="wp-block-button__link wp-element-button" href="#shop">Find my snack</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:kesto/arrow {"variant":"hook","color":"tomato","className":"kesto-hero__arrow"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"kesto-hero__media","layout":{"type":"default"}} -->
<div class="wp-block-group kesto-hero__media"><!-- wp:kesto/picture <?php echo wp_json_encode( $photo ); ?> /-->

<!-- wp:kesto/picture <?php echo $pouch( 'original', 'kesto-hero__pouch kesto-hero__pouch--a kesto-depth', __( 'KESTO Original Biltong pouch', 'kesto' ) ); ?> /-->

<!-- wp:kesto/picture <?php echo $pouch( 'chilli', 'kesto-hero__pouch kesto-hero__pouch--b kesto-depth', __( 'KESTO Chilli Biltong pouch', 'kesto' ) ); ?> /-->

<!-- wp:paragraph {"className":"kesto-hero__note"} -->
<p class="kesto-hero__note">Pass it round!</p>
<!-- /wp:paragraph -->

<!-- wp:kesto/arrow {"variant":"curve-down","color":"cream","className":"kesto-hero__note-arrow"} /--></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:kesto/benefits {"align":"full"} /-->
