<?php
/**
 * Title: Page — What's biltong?
 * Slug: kesto/page-biltong
 * Categories: kesto-pages
 * Description: A short, readable explanation of biltong with a route back to shopping.
 * Inserter: yes
 */

defined( 'ABSPATH' ) || exit;

$stage = static function ( string $key, string $alt ) {
	return wp_json_encode(
		array(
			'desktopUrl'    => kesto_img( "story-{$key}-800.webp" ),
			'desktopSrcset' => kesto_srcset( "story-{$key}", array( 480, 800, 1200 ) ),
			'desktopWidth'  => 800,
			'desktopHeight' => 600,
			'sizes'         => '(max-width: 781px) 92vw, 30vw',
			'alt'           => $alt,
			'className'     => 'kesto-stage__photo',
		)
	);
};
?>
<!-- wp:group {"className":"kesto-explainer","layout":{"type":"constrained"}} -->
<div class="wp-block-group kesto-explainer"><!-- wp:heading {"level":1,"className":"kesto-display"} -->
<h1 class="wp-block-heading kesto-display">What’s biltong?</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"kesto-lede"} -->
<p class="kesto-lede">Biltong is seasoned beef that is air-dried and sliced. A Southern African favourite, ready for your next snack.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"kesto-facts"} -->
<ul class="wp-block-list kesto-facts"><!-- wp:list-item -->
<li><strong>Seasoned</strong>Beef is seasoned first. That’s where Original and Chilli get their character.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Air-dried</strong>Then it’s dried, so it keeps its rich, savoury bite.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Sliced</strong>Finally it’s sliced, ready to eat straight from the resealable pouch.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">How do you eat it?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Straight from the bag. On a board in the middle of the table. In the car, on the trail, between meetings. Biltong is a snack people pass round, and that’s half the fun.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Not in the mood for meat? <strong>Magunanzi</strong> is our trail mix of roasted corn and nuts.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"kesto-story kesto-story--page","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull kesto-story kesto-story--page"><!-- wp:heading {"textAlign":"center","className":"kesto-display kesto-story__headline"} -->
<h2 class="wp-block-heading has-text-align-center kesto-display kesto-story__headline">From the farm. <br>To your good times.</h2>
<!-- /wp:heading -->

<!-- wp:group {"className":"kesto-story__stages","layout":{"type":"default"}} -->
<div class="wp-block-group kesto-story__stages"><!-- wp:group {"tagName":"figure","className":"kesto-stage","layout":{"type":"default"}} -->
<figure class="wp-block-group kesto-stage"><!-- wp:kesto/picture <?php echo $stage( 'source', __( 'Cattle grazing on a green pasture. Concept image.', 'kesto' ) ); ?> /-->

<!-- wp:paragraph {"className":"kesto-stage__label"} -->
<p class="kesto-stage__label">The source.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"kesto-stage__text"} -->
<p class="kesto-stage__text">Good beef is where every batch begins.</p>
<!-- /wp:paragraph --></figure>
<!-- /wp:group -->

<!-- wp:kesto/arrow {"variant":"swirl","color":"cream","className":"kesto-story__arrow"} /-->

<!-- wp:group {"tagName":"figure","className":"kesto-stage","layout":{"type":"default"}} -->
<figure class="wp-block-group kesto-stage"><!-- wp:kesto/picture <?php echo $stage( 'craft', __( 'Hands slicing seasoned biltong on a wooden board.', 'kesto' ) ); ?> /-->

<!-- wp:paragraph {"className":"kesto-stage__label"} -->
<p class="kesto-stage__label">The craft.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"kesto-stage__text"} -->
<p class="kesto-stage__text">Seasoned, air-dried, then sliced.</p>
<!-- /wp:paragraph --></figure>
<!-- /wp:group -->

<!-- wp:kesto/arrow {"variant":"swirl","color":"cream","className":"kesto-story__arrow"} /-->

<!-- wp:group {"tagName":"figure","className":"kesto-stage","layout":{"type":"default"}} -->
<figure class="wp-block-group kesto-stage"><!-- wp:kesto/picture <?php echo $stage( 'company', __( 'Friends passing a bowl of biltong across a table.', 'kesto' ) ); ?> /-->

<!-- wp:paragraph {"className":"kesto-stage__label"} -->
<p class="kesto-stage__label">The company.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"kesto-stage__text"} -->
<p class="kesto-stage__text">Best shared, wherever you are.</p>
<!-- /wp:paragraph --></figure>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"kesto-explainer kesto-explainer--end","layout":{"type":"constrained"}} -->
<div class="wp-block-group kesto-explainer kesto-explainer--end"><!-- wp:heading -->
<h2 class="wp-block-heading">Ready to try it?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Start with Original. If you like a little heat, go Chilli.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"kesto-btn kesto-btn--mango kesto-btn--arrow"} -->
<div class="wp-block-button kesto-btn kesto-btn--mango kesto-btn--arrow"><a class="wp-block-button__link wp-element-button" href="/shop/">Shop the range</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"kesto-btn kesto-btn--text"} -->
<div class="wp-block-button kesto-btn kesto-btn--text"><a class="wp-block-button__link wp-element-button" href="/our-roots/">Our roots</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
