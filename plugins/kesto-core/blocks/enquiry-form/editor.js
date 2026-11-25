( function ( wp ) {
	var el = wp.element.createElement;
	wp.blocks.registerBlockType( 'kesto/enquiry-form', {
		edit: function ( props ) {
			return el( wp.element.Fragment, null,
				el( wp.blockEditor.InspectorControls, null,
					el( wp.components.PanelBody, { title: 'Form' },
						el( wp.components.SelectControl, {
							label: 'Form type',
							value: props.attributes.type,
							options: [ { label: 'Contact', value: 'contact' }, { label: 'Wholesale', value: 'wholesale' } ],
							onChange: function ( v ) { props.setAttributes( { type: v } ); },
						} )
					)
				),
				el( 'div', wp.blockEditor.useBlockProps(), el( wp.serverSideRender, { block: 'kesto/enquiry-form', attributes: props.attributes } ) )
			);
		},
		save: function () { return null; },
	} );
} )( window.wp );
