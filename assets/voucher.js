const FORM = document.getElementById( 'voucher_form' );

FORM.addEventListener( 'submit', e => {
	e.preventDefault();
	// Prepare data to send post request.
	const data = {
		method: 'POST',
		// Create body as url string from current form data.
		body: new URLSearchParams( [ ...( new FormData( e.target ) ) ] ),
		credentials: 'same-origin',
		headers: {
			// Match header with body param.
			'Content-Type': 'application/x-www-form-urlencoded'
		},
	};

	fetch( wooVoucher.ajaxUrl, data )
		.then( response => response.json() )
		.then( processData );

} )

/**
 * Process form data, showing errors to user or redirecting further.
 * @param  {Object} response json response form fetch request
 */
function processData( response ) {
	if ( response.success ) {
		document.location = response.data.return_url;
	} else {
		const errorHolder = document.getElementById( 'js-error-holder' );
		errorHolder.classList.add( 'has-error' );
		console.log( response.data )
		response.data.forEach( error => {
			let errorMessage = document.createElement("p")
			let message = document.createTextNode( error )
			errorMessage.appendChild( message )
			errorHolder.appendChild( errorMessage )
		})
	}
}
