/* Progressive enhancement for kesto/enquiry-form: submit via REST, keep the page, report honestly. */
( function () {
	document.querySelectorAll( '[data-kesto-form]' ).forEach( function ( form ) {
		var status = form.querySelector( '[data-kesto-status]' );
		var button = form.querySelector( 'button[type="submit"]' );
		var label = button.textContent;

		function setStatus( kind, msg ) {
			status.className = 'kesto-form__status' + ( kind ? ' is-' + kind : '' );
			status.textContent = msg || '';
		}
		function clearErrors() {
			form.querySelectorAll( '[aria-invalid]' ).forEach( function ( f ) { f.removeAttribute( 'aria-invalid' ); } );
			form.querySelectorAll( '[data-error-for]' ).forEach( function ( p ) { p.textContent = ''; } );
		}

		form.addEventListener( 'submit', function ( e ) {
			if ( ! window.fetch || ! window.FormData ) {
				return; // native POST fallback
			}
			e.preventDefault();
			clearErrors();
			setStatus( '', '' );
			button.setAttribute( 'aria-busy', 'true' );
			button.disabled = true;
			button.textContent = 'Sending…';
			var body = {};
			new FormData( form ).forEach( function ( v, k ) { body[ k ] = v; } );

			fetch( form.getAttribute( 'data-endpoint' ), {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify( body ),
				credentials: 'same-origin',
			} ).then( function ( res ) {
				return res.json().then( function ( json ) { return { ok: res.ok, json: json }; } );
			} ).then( function ( r ) {
				if ( r.ok && r.json.ok ) {
					form.reset();
					setStatus( 'success', r.json.message );
					status.focus();
					return;
				}
				var errors = ( r.json && r.json.errors ) || {};
				var first = null;
				Object.keys( errors ).forEach( function ( name ) {
					var input = form.querySelector( '[name="' + name + '"]' );
					var p = form.querySelector( '[data-error-for="' + name + '"]' );
					if ( input ) {
						input.setAttribute( 'aria-invalid', 'true' );
						first = first || input;
					}
					if ( p ) {
						p.textContent = errors[ name ];
					}
				} );
				setStatus( 'error', ( r.json && r.json.message ) || 'Something went wrong. Please try again.' );
				( first || status ).focus();
			} ).catch( function () {
				setStatus( 'error', 'We couldn’t reach the server. Check your connection and try again — your message has not been sent.' );
				status.focus();
			} ).then( function () {
				button.removeAttribute( 'aria-busy' );
				button.disabled = false;
				button.textContent = label;
			} );
		} );
	} );
} )();
