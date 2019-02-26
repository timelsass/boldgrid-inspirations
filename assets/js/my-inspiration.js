jQuery( '.support-boxes li' ).on( 'click', function() {
	window.location.href = jQuery( this ).find( 'a' ).attr( 'href' );
});