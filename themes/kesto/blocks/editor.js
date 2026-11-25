/* KESTO block editor UI. Plain ES5-compatible JS on WordPress globals; no build step. */
( function ( wp ) {
	if ( ! wp || ! wp.blocks ) {
		return;
	}
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var be = wp.blockEditor;
	var c = wp.components;
	var SSR = wp.serverSideRender;
	var __ = wp.i18n.__;

	function ssr( name ) {
		return function ( props ) {
			return el( 'div', be.useBlockProps(), el( SSR, { block: name, attributes: props.attributes } ) );
		};
	}

	function withPanel( name, controls ) {
		return function ( props ) {
			return el(
				Fragment,
				null,
				el( be.InspectorControls, null, el( c.PanelBody, { title: __( 'Settings', 'kesto' ) }, controls( props ) ) ),
				ssr( name )( props )
			);
		};
	}

	/* Build a srcset from a media-library attachment object. */
	function srcsetFrom( media ) {
		var sizes = ( media && media.sizes ) || {};
		return Object.keys( sizes )
			.map( function ( k ) {
				return sizes[ k ].url + ' ' + sizes[ k ].width + 'w';
			} )
			.join( ', ' );
	}

	function mediaButton( label, onSelect, value ) {
		return el( be.MediaUploadCheck, null,
			el( be.MediaUpload, {
				allowedTypes: [ 'image' ],
				onSelect: onSelect,
				render: function ( o ) {
					return el( c.Button, { variant: 'secondary', onClick: o.open, style: { marginBottom: '8px' } }, value ? label + ' — ' + __( 'replace', 'kesto' ) : label );
				},
			} )
		);
	}

	wp.blocks.registerBlockType( 'kesto/picture', {
		edit: function ( props ) {
			var a = props.attributes;
			var set = props.setAttributes;
			return el(
				Fragment,
				null,
				el(
					be.InspectorControls,
					null,
					el(
						c.PanelBody,
						{ title: __( 'Image sources', 'kesto' ) },
						mediaButton( __( 'Desktop image', 'kesto' ), function ( m ) {
							set( { desktopUrl: m.url, desktopSrcset: srcsetFrom( m ), desktopWidth: m.width, desktopHeight: m.height, alt: a.alt || m.alt || '' } );
						}, a.desktopUrl ),
						mediaButton( __( 'Mobile crop (optional)', 'kesto' ), function ( m ) {
							set( { mobileUrl: m.url, mobileSrcset: srcsetFrom( m ), mobileWidth: m.width, mobileHeight: m.height } );
						}, a.mobileUrl ),
						a.mobileUrl && el( c.Button, { variant: 'link', isDestructive: true, onClick: function () { set( { mobileUrl: '', mobileSrcset: '' } ); } }, __( 'Remove mobile crop', 'kesto' ) ),
						el( c.TextareaControl, { label: __( 'Alternative text', 'kesto' ), help: __( 'Leave empty only if the image is purely decorative.', 'kesto' ), value: a.alt, onChange: function ( v ) { set( { alt: v } ); } } ),
						el( c.TextControl, { label: __( 'Focal point (CSS object-position)', 'kesto' ), value: a.focal, onChange: function ( v ) { set( { focal: v } ); } } ),
						el( c.TextControl, { label: __( 'sizes attribute', 'kesto' ), value: a.sizes, onChange: function ( v ) { set( { sizes: v } ); } } ),
						el( c.ToggleControl, { label: __( 'Load with high priority (first screen only)', 'kesto' ), checked: a.priority, onChange: function ( v ) { set( { priority: v } ); } } )
					)
				),
				el( 'picture', be.useBlockProps( { className: 'kesto-picture' } ),
					a.desktopUrl
						? el( 'img', { src: a.desktopUrl, srcSet: a.desktopSrcset || undefined, alt: a.alt, style: { objectPosition: a.focal } } )
						: el( c.Placeholder, { label: __( 'Art-directed picture', 'kesto' ) }, mediaButton( __( 'Choose image', 'kesto' ), function ( m ) {
							set( { desktopUrl: m.url, desktopSrcset: srcsetFrom( m ), desktopWidth: m.width, desktopHeight: m.height, alt: m.alt || '' } );
						} ) )
				)
			);
		},
		save: function () {
			return null;
		},
	} );

	wp.blocks.registerBlockType( 'kesto/arrow', {
		edit: withPanel( 'kesto/arrow', function ( props ) {
			var a = props.attributes;
			return [
				el( c.SelectControl, {
					key: 'v',
					label: __( 'Shape', 'kesto' ),
					value: a.variant,
					options: [ 'hook', 'loop', 'swirl', 'swoop', 'curve-down', 'scribble', 'scribble-long' ].map( function ( v ) {
						return { label: v, value: v };
					} ),
					onChange: function ( v ) { props.setAttributes( { variant: v } ); },
				} ),
				el( c.SelectControl, {
					key: 'c',
					label: __( 'Colour', 'kesto' ),
					value: a.color,
					options: [ 'tomato', 'cream', 'mango', 'ink' ].map( function ( v ) {
						return { label: v, value: v };
					} ),
					onChange: function ( v ) { props.setAttributes( { color: v } ); },
				} ),
				el( c.ToggleControl, { key: 'f', label: __( 'Mirror', 'kesto' ), checked: a.flip, onChange: function ( v ) { props.setAttributes( { flip: v } ); } } ),
			];
		} ),
		save: function () {
			return null;
		},
	} );

	wp.blocks.registerBlockType( 'kesto/featured-products', {
		edit: withPanel( 'kesto/featured-products', function ( props ) {
			return [
				el( c.RangeControl, { key: 'n', label: __( 'Number of products', 'kesto' ), min: 1, max: 6, value: props.attributes.count, onChange: function ( v ) { props.setAttributes( { count: v } ); } } ),
				el( c.ToggleControl, { key: 'f', label: __( 'Show claims / demo-price footnote', 'kesto' ), checked: props.attributes.showFootnote, onChange: function ( v ) { props.setAttributes( { showFootnote: v } ); } } ),
				el( 'p', { key: 'h' }, __( 'Products come from WooCommerce: mark them as Featured. Order follows the product “Menu order”.', 'kesto' ) ),
			];
		} ),
		save: function () {
			return null;
		},
	} );

	wp.blocks.registerBlockType( 'kesto/concept-note', {
		edit: withPanel( 'kesto/concept-note', function ( props ) {
			return [ el( c.TextareaControl, { key: 't', label: __( 'Disclaimer text', 'kesto' ), value: props.attributes.text, onChange: function ( v ) { props.setAttributes( { text: v } ); } } ) ];
		} ),
		save: function () {
			return null;
		},
	} );

	wp.blocks.registerBlockType( 'kesto/bag', {
		edit: withPanel( 'kesto/bag', function ( props ) {
			return [ el( c.TextControl, { key: 'l', label: __( 'Label', 'kesto' ), value: props.attributes.label, onChange: function ( v ) { props.setAttributes( { label: v } ); } } ) ];
		} ),
		save: function () {
			return null;
		},
	} );

	[ 'kesto/logo', 'kesto/benefits', 'kesto/social', 'kesto/product-notes' ].forEach( function ( name ) {
		wp.blocks.registerBlockType( name, { edit: ssr( name ), save: function () { return null; } } );
	} );
} )( window.wp );
