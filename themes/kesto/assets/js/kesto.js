/**
 * KESTO front-end: entrance choreography, product-layer depth, live bag count,
 * quick add-to-bag via the WooCommerce Store API. No dependencies.
 * Every feature degrades to plain links / visible content without this file.
 */
( function () {
	'use strict';

	var root = document.documentElement;
	var data = window.kestoData || {};
	var motion = root.classList.contains( 'kesto-motion' );

	/* ---------- Entrances: mark elements once as they enter ---------- */
	function observeInView() {
		var targets = document.querySelectorAll( '.kesto-arrow, .kesto-story, .kesto-products' );
		if ( ! motion || ! ( 'IntersectionObserver' in window ) ) {
			targets.forEach( function ( t ) { t.classList.add( 'is-inview' ); } );
			return;
		}
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( e ) {
				if ( e.isIntersecting ) {
					e.target.classList.add( 'is-inview' );
					io.unobserve( e.target );
				}
			} );
		}, { rootMargin: '0px 0px -12% 0px', threshold: 0.15 } );
		targets.forEach( function ( t ) { io.observe( t ); } );
	}

	/* ---------- Depth: pouches drift a few px against the scroll ---------- */
	function depth() {
		if ( ! motion ) {
			return;
		}
		var layers = Array.prototype.slice.call( document.querySelectorAll( '.kesto-depth' ) );
		if ( ! layers.length ) {
			return;
		}
		var ticking = false;
		function update() {
			var vh = window.innerHeight;
			layers.forEach( function ( el, i ) {
				var r = el.getBoundingClientRect();
				if ( r.bottom < -100 || r.top > vh + 100 ) {
					return;
				}
				var centre = ( r.top + r.height / 2 - vh / 2 ) / vh; // -1..1
				var factor = i % 2 ? 22 : 14;
				el.style.setProperty( '--depth', ( -centre * factor ).toFixed( 1 ) );
			} );
			ticking = false;
		}
		window.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( update );
			}
		}, { passive: true } );
		update();
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
				setCount( r.body.items_count || 0 );
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
		depth();
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
