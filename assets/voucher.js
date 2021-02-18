const FORM = document.getElementById( 'voucher_form' );

FORM.addEventListener( 'submit', e => {
	e.preventDefault();
	// Prepare data to send post request.
	const data = {
		method: 'POST',
		// Create body as url string from current form data.
		body: new URLSearchParams( [ ...( new FormData( e.currentTarget ) ) ] ),
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
		response.data.forEach( error => {
			FORM.innerHTML += `<h2>${error}</h2>`
		})
	}
}
