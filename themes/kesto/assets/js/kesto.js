/**
 * KESTO front-end: entrance choreography, scroll-driven scenes, pointer tilt,
 * page-to-page continuity, live bag count,
 * quick add-to-bag via the WooCommerce Store API. No dependencies.
 * Every feature degrades to plain links / visible content without this file.
 */
( function () {
	'use strict';

	var root = document.documentElement;
	var data = window.kestoData || {};
	var motion = root.classList.contains( 'kesto-motion' );

	/* ==========================================================================
	   Motion layer. Content is visible by default; hidden start states only
	   apply under html.kesto-motion (set in <head> when the visitor allows
	   motion). Everything here reads layout first, then writes custom properties,
	   once per animation frame.
	   ========================================================================== */
	function mq( q ) {
		return window.matchMedia ? window.matchMedia( q ) : { matches: false, addEventListener: function () {} };
	}
	var pinQuery = mq( '(min-width: 1024px) and (min-height: 620px)' );
	var wideQuery = mq( '(min-width: 600px)' );
	var finePointer = mq( '(hover: hover) and (pointer: fine)' ).matches;
	function clamp01( v ) {
		return v < 0 ? 0 : ( v > 1 ? 1 : v );
	}
	function each( sel, fn, scope ) {
		Array.prototype.forEach.call( ( scope || document ).querySelectorAll( sel ), fn );
	}

	/* ---------- Staggered reveals ---------- */
	// [parent, children] pairs: children rise in sequence as their parent arrives.
	var GROUPS = [
		[ '.kesto-community__copy', ':scope > *' ],
		[ '.kesto-shop', ':scope > .kesto-shop__range' ],
		[ '.kesto-footer__inner', ':scope > *' ],
		[ '.kesto-explainer', ':scope > *' ],
		[ '.kesto-page', ':scope > .wp-block-post-title' ],
		[ '.kesto-page > .wp-block-post-content', ':scope > *' ],
		[ '.kesto-shop-page__head', ':scope > *' ],
		[ '.kesto-range .wc-block-product-template', ':scope > li' ],
		[ '.kesto-related .wp-block-post-template', ':scope > li' ],
		[ '.kesto-product-page', ':scope > .kesto-pdp__details' ]
	];

	function tagReveals() {
		var found = [];
		var vh = window.innerHeight;
		GROUPS.forEach( function ( g ) {
			each( g[ 0 ], function ( parent ) {
				var i = 0;
				each( g[ 1 ], function ( el ) {
					// Leave whatever is already on screen alone: hiding it now would flash.
					if ( el.getBoundingClientRect().top < vh ) {
						return;
					}
					el.classList.add( 'kesto-rv' );
					el.style.setProperty( '--i', Math.min( i++, 5 ) );
					found.push( el );
				}, parent );
			} );
		} );
		return found;
	}

	function observeInView() {
		var targets = Array.prototype.slice.call(
			document.querySelectorAll( '.kesto-arrow, .kesto-story, .kesto-products, .kesto-kinetic, .kesto-benefits' )
		);
		if ( ! motion || ! ( 'IntersectionObserver' in window ) ) {
			targets.forEach( function ( t ) { t.classList.add( 'is-inview' ); } );
			return;
		}
		targets = targets.concat( tagReveals() );
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( e ) {
				if ( e.isIntersecting ) {
					e.target.classList.add( 'is-inview' );
					io.unobserve( e.target );
				}
			} );
		}, { rootMargin: '0px 0px -10% 0px', threshold: 0.12 } );
		targets.forEach( function ( t ) { io.observe( t ); } );
		root.classList.add( 'kesto-io' ); // tells the <head> safety timer that JS is in charge
	}

	/* ---------- Scroll-driven scenes ---------- */
	function scrollScenes() {
		if ( ! motion ) {
			return;
		}
		var scenes = [];
		var story = document.querySelector( '.kesto-story' );
		var pinnable = story && story.querySelector( '.kesto-story__pin' );
		var storyArrows = story ? Array.prototype.slice.call( story.querySelectorAll( '.kesto-story__arrow' ) ) : [];

		function pinned() {
			return !! pinnable && pinQuery.matches;
		}

		// Hero: photo eases in, pouches and copy drift apart as it scrolls away.
		var hero = document.querySelector( '.kesto-hero' );
		if ( hero ) {
			scenes.push( {
				el: hero,
				prop: '--hp',
				fn: function ( r ) { return wideQuery.matches ? clamp01( -r.top / r.height ) : 0; }
			} );
		}

		// Ticker: words slide sideways as the band crosses the screen.
		each( '.kesto-ticker', function ( el ) {
			var band = el.querySelector( '.kesto-ticker__band' );
			var list = el.querySelector( '.kesto-ticker__list' );
			if ( band && list ) {
				for ( var k = 0; k < 3; k++ ) {
					var copy = list.cloneNode( true );
					copy.setAttribute( 'aria-hidden', 'true' );
					copy.removeAttribute( 'id' );
					band.appendChild( copy );
				}
			}
			scenes.push( {
				el: el,
				prop: '--tp',
				fn: function ( r, vh ) { return clamp01( ( vh - r.top ) / ( vh * 1.6 ) ); }
			} );
		} );

		// Story: pinned on large screens; scroll progress plays source → craft → company.
		if ( story ) {
			scenes.push( {
				el: story,
				prop: '--p',
				fn: function ( r, vh ) {
					return pinned() ? clamp01( ( vh * 0.5 - r.top ) / ( r.height - vh * 0.5 ) ) : 1;
				}
			} );
		}

		// Arrows (except the hero's, which draw on load) draw as they scroll into view.
		each( '.kesto-arrow', function ( el ) {
			if ( el.closest( '.kesto-hero' ) ) {
				return;
			}
			var n = el.querySelectorAll( 'path' ).length || 1;
			el.setAttribute( 'data-scrub', '' );
			el.style.setProperty( '--n', n );
			var idx = storyArrows.indexOf( el );
			scenes.push( {
				el: el,
				prop: '--draw',
				fn: function ( r, vh ) {
					if ( idx > -1 && pinned() ) {
						var p = story.matches( ':focus-within' ) ? 1 : ( parseFloat( story.style.getPropertyValue( '--p' ) ) || 0 );
						return clamp01( ( p - ( idx ? 0.56 : 0.24 ) ) / 0.14 );
					}
					// Fully drawn by the time its centre is a quarter up the screen, or at the page end.
					if ( window.scrollY + vh >= document.documentElement.scrollHeight - 4 ) {
						return 1;
					}
					return clamp01( ( vh * 0.98 - r.top ) / ( vh * 0.23 + r.height * 0.5 ) );
				}
			} );
		} );

		// Free-floating pouches outside the hero drift a few px against the scroll.
		each( '.kesto-depth', function ( el, i ) {
			if ( el.closest( '.kesto-hero' ) ) {
				return;
			}
			scenes.push( {
				el: el,
				prop: '--depth',
				fn: function ( r, vh ) {
					var centre = ( r.top + r.height / 2 - vh / 2 ) / vh;
					return Math.max( -30, Math.min( 30, -centre * ( i % 2 ? 22 : 14 ) ) );
				}
			} );
		} );

		if ( ! scenes.length ) {
			return;
		}
		root.classList.add( 'kesto-scrub' );

		var ticking = false;
		function update() {
			ticking = false;
			var vh = window.innerHeight;
			if ( story ) {
				story.classList.toggle( 'is-pinned', pinned() );
			}
			// Read every rect first, then write, so the frame lays out once.
			var rects = scenes.map( function ( s ) { return s.el.getBoundingClientRect(); } );
			scenes.forEach( function ( s, i ) {
				var r = rects[ i ];
				var v = s.fn( r, vh );
				if ( s.last === undefined || Math.abs( v - s.last ) > 0.0005 ) {
					s.last = v;
					s.el.style.setProperty( s.prop, v.toFixed( 4 ) );
				}
			} );
		}
		function request() {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( update );
			}
		}
		window.addEventListener( 'scroll', request, { passive: true } );
		window.addEventListener( 'resize', request );
		if ( story ) {
			story.addEventListener( 'focusin', request );
			story.addEventListener( 'focusout', request );
		}
		update();
		// Pinning changes the page height; settle once more after layout.
		window.requestAnimationFrame( update );
	}

	/* ---------- Pointer tilt with a light glint (mouse only) ---------- */
	function tilt() {
		if ( ! motion || ! finePointer ) {
			return;
		}
		function prime( t ) {
			var img = t.querySelector( 'img' );
			if ( ! img ) {
				return;
			}
			var set = function () {
				var src = img.currentSrc || img.src;
				if ( src ) {
					t.style.setProperty( '--shine-mask', 'url("' + src.replace( /"/g, '%22' ) + '")' );
					t.classList.add( 'has-shine' );
				}
			};
			if ( img.complete ) {
				set();
			} else {
				img.addEventListener( 'load', set, { once: true } );
			}
		}
		each( '.kesto-hero__media, .kesto-product', function ( area ) {
			var targets = area.querySelectorAll( '.kesto-tilt' );
			if ( ! targets.length ) {
				return;
			}
			Array.prototype.forEach.call( targets, prime );
			var frame = 0;
			area.addEventListener( 'pointermove', function ( e ) {
				if ( e.pointerType !== 'mouse' || frame ) {
					return;
				}
				frame = window.requestAnimationFrame( function () {
					frame = 0;
					var r = area.getBoundingClientRect();
					var nx = ( ( e.clientX - r.left ) / r.width ) * 2 - 1;
					var ny = ( ( e.clientY - r.top ) / r.height ) * 2 - 1;
					Array.prototype.forEach.call( targets, function ( t ) {
						t.style.setProperty( '--ry', ( nx * 12 ).toFixed( 2 ) );
						t.style.setProperty( '--rx', ( -ny * 9 ).toFixed( 2 ) );
						t.classList.add( 'is-tilting' );
					} );
				} );
			} );
			area.addEventListener( 'pointerleave', function () {
				Array.prototype.forEach.call( targets, function ( t ) {
					t.style.setProperty( '--ry', 0 );
					t.style.setProperty( '--rx', 0 );
					t.classList.remove( 'is-tilting' );
				} );
			} );
		} );
	}

	/* ---------- Page-to-page continuity: the tapped pouch becomes the product photo ---------- */
	function viewTransitions() {
		function clearNames() {
			each( '[data-kesto-vt]', function ( el ) {
				el.style.viewTransitionName = '';
				el.removeAttribute( 'data-kesto-vt' );
			} );
		}
		window.addEventListener( 'pageswap', function ( e ) {
			clearNames();
			if ( ! e.viewTransition ) {
				return;
			}
			// An interrupted transition rejects its promises; that is expected, not an error.
			[ e.viewTransition.ready, e.viewTransition.finished, e.viewTransition.updateCallbackDone ].forEach( function ( q ) {
				if ( q ) {
					q.catch( function () {} );
				}
			} );
			if ( ! e.activation || ! e.activation.entry ) {
				return;
			}
			var url = e.activation.entry.url;
			var match = Array.prototype.find.call( document.querySelectorAll( 'a[href]' ), function ( a ) {
				return a.href === url && a.querySelector( '.kesto-product__pouch, img.wp-post-image' );
			} );
			if ( match ) {
				var img = match.querySelector( '.kesto-product__pouch, img.wp-post-image' );
				img.style.viewTransitionName = 'kesto-pouch';
				img.setAttribute( 'data-kesto-vt', '' );
			}
		} );
		window.addEventListener( 'pageshow', clearNames );
	}

	/* ---------- Added to bag: the pouch hops into the bag ---------- */
	// When the header bag is scrolled away, a small bag docks in the corner to
	// catch the pouch, shows the new count, then tucks away again.
	function dockBag( count ) {
		var dock = document.querySelector( '.kesto-bagdock' );
		if ( ! dock ) {
			dock = document.createElement( 'div' );
			dock.className = 'kesto-bagdock';
			dock.setAttribute( 'aria-hidden', 'true' ); // the live region already announces the add
			dock.innerHTML = '<span class="kesto-bagdock__label"></span>';
			document.body.appendChild( dock );
		}
		window.clearTimeout( dock._t );
		dock.querySelector( '.kesto-bagdock__label' ).textContent = 'Bag (' + count + ')';
		dock.classList.add( 'is-shown' );
		return dock;
	}

	function flyToBag( link, count, done ) {
		var tile = link.closest( '.kesto-product' );
		var src = tile && tile.querySelector( '.kesto-product__pouch' );
		if ( ! motion || ! src || ! src.animate ) {
			done();
			return;
		}
		var vh = window.innerHeight;
		var bag = Array.prototype.find.call( document.querySelectorAll( '[data-kesto-bag]' ), function ( b ) {
			var r = b.getBoundingClientRect();
			return r.width > 0 && r.bottom > 8 && r.top < vh - 8;
		} );
		var dock = bag ? null : dockBag( Math.max( count - 1, 0 ) );
		var target = bag || dock;
		var a = src.getBoundingClientRect();
		var b = target.getBoundingClientRect();
		var tx = b.left + b.width / 2 - ( a.left + a.width / 2 );
		// Keep the whole (shrinking) pouch below the top edge on its way up.
		var ty = Math.max( b.top + b.height / 2, a.height * 0.13 + 10 ) - ( a.top + a.height / 2 );
		// Three nested layers: sideways travel eases in, the rise eases out, so the
		// path bows up toward the bag; the pouch shrinks as it goes and fades on arrival.
		var x = document.createElement( 'div' );
		var y = document.createElement( 'div' );
		var img = document.createElement( 'img' );
		x.className = 'kesto-ghost';
		x.setAttribute( 'aria-hidden', 'true' );
		x.style.cssText = 'left:' + a.left + 'px;top:' + a.top + 'px;width:' + a.width + 'px;height:' + a.height + 'px';
		img.src = src.currentSrc || src.src;
		img.alt = '';
		y.appendChild( img );
		x.appendChild( y );
		document.body.appendChild( x );
		var opts = { duration: 580, fill: 'forwards' };
		x.animate( [ { transform: 'translateX(0)' }, { transform: 'translateX(' + tx + 'px)' } ],
			Object.assign( { easing: 'cubic-bezier(0.45, 0, 0.7, 1)' }, opts ) );
		var rise = y.animate( [ { transform: 'translateY(0)' }, { transform: 'translateY(' + ty + 'px)' } ],
			Object.assign( { easing: 'cubic-bezier(0.4, 0.15, 0.4, 1)' }, opts ) );
		img.animate( [
			{ transform: 'scale(1) rotate(0deg)', opacity: 1 },
			{ transform: 'scale(0.42) rotate(-8deg)', opacity: 1, offset: 0.4 },
			{ transform: 'scale(0.26) rotate(-16deg)', opacity: 1, offset: 0.85 },
			{ transform: 'scale(0.22) rotate(-18deg)', opacity: 0 }
		], Object.assign( { easing: 'linear' }, opts ) );
		rise.onfinish = function () {
			x.remove();
			done();
			if ( dock ) {
				dock.querySelector( '.kesto-bagdock__label' ).textContent = 'Bag (' + count + ')';
				dock.classList.remove( 'is-bumped' );
				void dock.offsetWidth;
				dock.classList.add( 'is-bumped' );
				dock._t = window.setTimeout( function () { dock.classList.remove( 'is-shown' ); }, 1600 );
			}
		};
	}

	/* ---------- Bag count ---------- */
	var nonce = data.nonce || '';

	function setCount( n ) {
		document.querySelectorAll( '[data-kesto-bag]' ).forEach( function ( bag ) {
			var c = bag.querySelector( '[data-kesto-bag-count]' );
			var sr = bag.querySelector( '[data-kesto-bag-sr]' );
			if ( c && String( c.textContent ) !== String( n ) ) {
				c.textContent = n;
				bag.classList.remove( 'is-bumped' );
				void bag.offsetWidth; // restart animation
				bag.classList.add( 'is-bumped' );
			}
			if ( sr ) {
				sr.textContent = n === 1 ? '1 item' : n + ' items';
			}
		} );
	}

	function api( path, opts ) {
		opts = opts || {};
		opts.headers = Object.assign( { 'Content-Type': 'application/json' }, opts.headers || {} );
		if ( nonce ) {
			opts.headers.Nonce = nonce;
		}
		opts.credentials = 'same-origin';
		return fetch( data.storeApi + path, opts ).then( function ( res ) {
			var fresh = res.headers.get( 'Nonce' );
			if ( fresh ) {
				nonce = fresh;
			}
			return res.json().then( function ( body ) {
				return { ok: res.ok, status: res.status, body: body };
			} );
		} );
	}

	function refreshCount() {
		if ( ! data.storeApi || ! document.querySelector( '[data-kesto-bag]' ) ) {
			return Promise.resolve();
		}
		return api( 'cart' ).then( function ( r ) {
			if ( r.ok && r.body ) {
				setCount( r.body.items_count || 0 );
			}
		} ).catch( function () {} );
	}

	/* ---------- Quick add-to-bag ---------- */
	function announce( msg ) {
		var live = document.querySelector( '[data-kesto-live]' );
		if ( live ) {
			live.textContent = '';
			window.setTimeout( function () { live.textContent = msg; }, 50 );
		}
	}

	function quickAdd( link ) {
		var id = parseInt( link.getAttribute( 'data-kesto-add' ), 10 );
		var text = link.querySelector( '.kesto-add__text' );
		var original = text ? text.textContent : '';
		var i18n = data.i18n || {};
		link.setAttribute( 'aria-busy', 'true' );
		link.classList.remove( 'is-added', 'is-error' );
		if ( text ) {
			text.textContent = i18n.adding || 'Adding…';
		}

		function attempt( retry ) {
			return api( 'cart/add-item', { method: 'POST', body: JSON.stringify( { id: id, quantity: 1 } ) } ).then( function ( r ) {
				if ( ! r.ok && retry && ( r.status === 401 || r.status === 403 ) ) {
					return api( 'cart' ).then( function () { return attempt( false ); } );
				}
				return r;
			} );
		}

		attempt( true ).then( function ( r ) {
			link.removeAttribute( 'aria-busy' );
			if ( r.ok ) {
				var count = r.body.items_count || 0;
				flyToBag( link, count, function () { setCount( count ); } );
				link.classList.add( 'is-added' );
				if ( text ) {
					text.textContent = i18n.added || 'Added to bag';
				}
				var name = link.closest( '.kesto-product' );
				name = name ? name.querySelector( '.kesto-product__name' ).textContent.trim() : '';
				announce( ( name ? name + ' — ' : '' ) + ( i18n.added || 'Added to bag' ) + '. ' + ( r.body.items_count || 0 ) + ' in your bag.' );
				window.setTimeout( function () {
					link.classList.remove( 'is-added' );
					if ( text ) {
						text.textContent = original;
					}
				}, 2600 );
			} else {
				var msg = ( r.body && r.body.message ) ? r.body.message.replace( /<[^>]+>/g, '' ) : ( i18n.error || 'Couldn’t add that.' );
				link.classList.add( 'is-error' );
				if ( text ) {
					text.textContent = original;
				}
				announce( msg );
			}
		} ).catch( function () {
			// Network/API unavailable: fall back to WooCommerce's classic add-to-cart URL.
			window.location.href = link.href;
		} );
	}

	document.addEventListener( 'click', function ( e ) {
		var link = e.target.closest && e.target.closest( '[data-kesto-add]' );
		if ( ! link || ! data.storeApi || ! window.fetch ) {
			return;
		}
		e.preventDefault();
		if ( link.getAttribute( 'aria-busy' ) !== 'true' ) {
			quickAdd( link );
		}
	} );

	// Keep the count in step with WooCommerce blocks (cart page, mini-cart, product pages).
	[ 'wc-blocks_added_to_cart', 'wc-blocks_removed_from_cart' ].forEach( function ( evt ) {
		document.body.addEventListener( evt, refreshCount );
	} );
	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'added_to_cart removed_from_cart updated_cart_totals', refreshCount );
	}
	window.addEventListener( 'pageshow', function ( e ) {
		if ( e.persisted ) {
			refreshCount();
		}
	} );

	function followCartStore() {
		var wpd = window.wp && window.wp.data;
		if ( ! wpd || ! wpd.select || ! wpd.select( 'wc/store/cart' ) ) {
			return;
		}
		var last = null;
		wpd.subscribe( function () {
			var cart = wpd.select( 'wc/store/cart' ).getCartData();
			var n = cart && typeof cart.itemsCount === 'number' ? cart.itemsCount : null;
			if ( n !== null && n !== last ) {
				last = n;
				setCount( n );
			}
		} );
	}

	function init() {
		observeInView();
		scrollScenes();
		tilt();
		viewTransitions();
		// Refresh only when WooCommerce says a cart exists, so empty visits cost no request.
		if ( document.cookie.indexOf( 'woocommerce_items_in_cart' ) !== -1 ) {
			refreshCount();
		}
		// Cart / checkout blocks keep their cart in the wc/store/cart data store; follow it
		// once all footer scripts (including WooCommerce's) have run.
		window.addEventListener( 'load', followCartStore );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
