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

/*
 * Reach out and hit the front end of the site to make sure all after theme switch hooks are fired.
 *
 * For this call, we do not want to fire any crons, this may trigger the framework resetting twice.
 * # We are sending this via POST because wp-cron.php aborts if $_POST has data.
 * # We are sending doing_wp_cron because the cron will not fire if that $_GET var exists.
 */
jQuery.ajax({
	type: "POST",
	url: IMHWPB.configs.site_url + "?doing_wp_cron=fire-after-theme-switch-hooks",
	data: { dummy_post_data: "Dummy post data" },
});

jQuery( function() {
	// After the page is done loading, update the cart total.
	if ( typeof boldgrid_deploy_cost != 'undefined' ) {
		var BaseAdmin = new IMHWPB.BaseAdmin();
		BaseAdmin.update_header_cart( boldgrid_deploy_cost );
	}

	/*
	 * Prevent Customizer from returning to Inspirations.
	 *
	 * In you visit the Customizer directly after installing an Inspiration, the Customizer's
	 * return= value will be that of Inspirations. We need to change this value so that after the
	 * user exits the Customizer, they visit their dashboard.
	 *
	 * @since 1.3.7
	 *
	 * @link  http://stackoverflow.com/questions/5413899/search-and-replace-specific-query-string-parameter-value-in-javascript
	 */
	jQuery( 'a[href*="customize.php"]' ).each( function() {
		var $link = jQuery( this ),
			currentUrl = $link.attr( 'href' ),
			newReturn = BoldGridAdmin.dashboardUrl,
			newUrl = currentUrl.replace( /(return=)[^\&]+/, '$1' + newReturn );

		$link.attr( 'href', newUrl );
	});
});
