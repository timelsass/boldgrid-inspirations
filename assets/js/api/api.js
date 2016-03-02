var IMHWPB = IMHWPB || {};

IMHWPB.Api = function(configs) {
	var self = this;
	$c_zakn = jQuery( '#container_boldgrid_api_key_notice' );

	/**
	 * Bind events -
	 * When the submit button is pressed:
	 */
	jQuery("#boldgrid-api-form").submit(function(e){
		e.preventDefault();
	});
	jQuery( '#boldgrid-api-loading', $c_zakn ).hide();
	jQuery('#submit_api_key', $c_zakn).on('click', function() {
		var api_key = jQuery('#boldgrid_api_key', $c_zakn).val()
			.replace(/[^a-z0-9]/gi,'')
			.substr(0, 32)
			.replace(/(.{8})/g,"$1\-")
			.slice(0, - 1);
		self.set(api_key);
		// hide the button
		jQuery( this ).hide();
		// show the loading graphic
		jQuery('#boldgrid-api-loading', $c_zakn).show();
	});

	/**
	 * Function declaraions
	 */
	this.set = function(api_key) {
		var data = {
			'action'  : 'set_api_key',
			'api_key' :  api_key
		};

		jQuery.post( ajaxurl, data, function(response) {
			// if the key was saved successfully
			if ('true' == response) {
				// change the notice from red to green
				$c_zakn.toggleClass('error').toggleClass(
						'updated');
				// then update the message
				jQuery('#boldgrid_api_key_notice_message', $c_zakn)
					.html( 'Your api key has been saved successfully! <a onClick="window.location.reload(true)" style="cursor:pointer;"> Dismiss Notification</a>');
				// remove the loading graphic since success
				jQuery('#boldgrid-api-loading', $c_zakn).fadeOut();
				// and finally hide the input elements as we
				// don't need them anymore.
				jQuery('#boldgrid_api_key', $c_zakn).fadeOut();
				// reload page after 3 sec
				setTimeout( function(){
					window.location.reload();
				}, 3000 );
	
			} else if ('error saving key' == response) {
				// hide loading
				jQuery('#boldgrid-api-loading', $c_zakn).hide();
				// show button
				jQuery('#submit_api_key', $c_zakn).show();
				jQuery('#boldgrid_api_key_notice_message', $c_zakn)
					.html( 'There was an error saving your key.<br />Please try entering your BoldGrid Connect Key again.');
			} else {
				// hide loading
				jQuery('#boldgrid-api-loading', $c_zakn).hide();
				// show button
				jQuery('#submit_api_key', $c_zakn).show();
				jQuery('#boldgrid_api_key_notice_message', $c_zakn)
					.html( 'Your API key appears to be invalid!<br />Please try to enter your BoldGrid Connect Key again.');

			}
		});
	}

}

new IMHWPB.Api();
