<?php
/**
 * Title: Home — from the farm to your good times
 * Slug: kesto/home-story
 * Categories: kesto
 * Description: Green band: what biltong is, then source, craft and company.
 */

defined( 'ABSPATH' ) || exit;

$stage = static function ( string $key, string $alt ) {
	return wp_json_encode(
		array(
			'desktopUrl'    => kesto_img( "story-{$key}-800.webp" ),
			'desktopSrcset' => kesto_srcset( "story-{$key}", array( 480, 800, 1200 ) ),
			'desktopWidth'  => 800,
			'desktopHeight' => 600,
			'sizes'         => '(max-width: 781px) 88vw, 30vw',
			'alt'           => $alt,
			'className'     => 'kesto-stage__photo',
		)
	);
};
?>
<!-- wp:group {"tagName":"section","align":"full","anchor":"biltong","className":"kesto-story","layout":{"type":"default"}} -->
<section id="biltong" class="wp-block-group alignfull kesto-story"><!-- wp:group {"className":"kesto-story__pin","layout":{"type":"default"}} -->
<div class="wp-block-group kesto-story__pin"><!-- wp:heading {"textAlign":"center","className":"kesto-display kesto-kinetic kesto-story__headline"} -->
<h2 class="wp-block-heading has-text-align-center kesto-display kesto-kinetic kesto-story__headline">From the farm. <br>To your good times.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"kesto-story__intro"} -->
<p class="has-text-align-center kesto-story__intro"><strong>New to biltong?</strong> Biltong is seasoned beef that is air-dried and sliced. A Southern African favourite, ready for your next snack.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"kesto-story__stages","layout":{"type":"default"}} -->
<div class="wp-block-group kesto-story__stages"><!-- wp:group {"tagName":"figure","className":"kesto-stage","layout":{"type":"default"}} -->
<figure class="wp-block-group kesto-stage"><!-- wp:kesto/picture <?php echo $stage( 'source', __( 'Cattle grazing on a green pasture in late-afternoon light. Concept image.', 'kesto' ) ); ?> /-->

<!-- wp:paragraph {"className":"kesto-stage__label"} -->
<p class="kesto-stage__label">The source.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"kesto-stage__text"} -->
<p class="kesto-stage__text">Good beef is where every batch begins.</p>
<!-- /wp:paragraph --></figure>
<!-- /wp:group -->

<!-- wp:kesto/arrow {"variant":"swirl","color":"cream","className":"kesto-story__arrow"} /-->

<!-- wp:group {"tagName":"figure","className":"kesto-stage","layout":{"type":"default"}} -->
<figure class="wp-block-group kesto-stage"><!-- wp:kesto/picture <?php echo $stage( 'craft', __( 'Hands slicing a seasoned piece of biltong on a wooden board.', 'kesto' ) ); ?> /-->

<!-- wp:paragraph {"className":"kesto-stage__label"} -->
<p class="kesto-stage__label">The craft.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"kesto-stage__text"} -->
<p class="kesto-stage__text">Seasoned, air-dried, then sliced.</p>
<!-- /wp:paragraph --></figure>
<!-- /wp:group -->

<!-- wp:kesto/arrow {"variant":"swirl","color":"cream","className":"kesto-story__arrow"} /-->

<!-- wp:group {"tagName":"figure","className":"kesto-stage","layout":{"type":"default"}} -->
<figure class="wp-block-group kesto-stage"><!-- wp:kesto/picture <?php echo $stage( 'company', __( 'Friends laughing as they pass a bowl of biltong across an outdoor table.', 'kesto' ) ); ?> /-->

<!-- wp:paragraph {"className":"kesto-stage__label"} -->
<p class="kesto-stage__label">The company.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"kesto-stage__text"} -->
<p class="kesto-stage__text">Best shared, wherever you are.</p>
<!-- /wp:paragraph --></figure>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:buttons {"className":"kesto-story__cta","layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons kesto-story__cta"><!-- wp:button {"className":"kesto-btn kesto-btn--cream kesto-btn--arrow"} -->
<div class="wp-block-button kesto-btn kesto-btn--cream kesto-btn--arrow"><a class="wp-block-button__link wp-element-button" href="/what-is-biltong/">Explore the story</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
