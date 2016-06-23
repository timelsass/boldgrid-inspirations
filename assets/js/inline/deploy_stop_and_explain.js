// Move 'stop and explain' to the top of the page.
var stop_and_explain = jQuery( 'div#stop_and_explain' );
jQuery( stop_and_explain ).insertBefore( jQuery( 'div#deploy_status' ) )
	.slideToggle( 1000 );

// Remove the loading graphic.
jQuery( '#deploy_status .boldgrid-loading' ).slideUp();

// Update the title of the page, which also removes the spinner.
jQuery( '#deploy_status h1' ).html( 'Installation complete!' )
	.prepend( '<span class="dashicons dashicons-yes"></span>' );

// Remove the installation text, the one that updates on the fly.
jQuery( '#deploy_text' ).addClass( 'hidden' );
jQuery( '.spinner' ).remove();

// Scroll the user to the top of the page.
jQuery( 'html, body' ).animate({
	scrollTop : 0
}, 'slow' );

//Reach out and hit the front end of the site to make sure all after theme switch hooks are fired
jQuery.ajax({
	url: IMHWPB.configs.site_url,
});

jQuery( function() {
	// After the page is done loading, update the cart total.
	if ( typeof boldgrid_deploy_cost != 'undefined' ) {
		var BaseAdmin = new IMHWPB.BaseAdmin();
		BaseAdmin.update_header_cart( boldgrid_deploy_cost );
	}
});
