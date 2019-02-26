// When the user clicks one of the support boxes, navigate to the url of that box's button.
jQuery( '.support-boxes li' ).on( 'click', function() {
	window.location.href = jQuery( this ).find( 'a' ).attr( 'href' );
});