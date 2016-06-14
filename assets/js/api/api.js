var IMHWPB = IMHWPB || {};

IMHWPB.Api = function(configs) {
	( function( $ ) {
		var self = this;
		$c_zakn = $( '#container_boldgrid_api_key_notice' );

		/** Toggle the forms around **/
		$( '.boldgridApiKeyLink', $c_zakn ).on( 'click', function() {
			$( '.api-notice', $c_zakn ).hide();
			$( '.new-api-key', $c_zakn ).fadeIn( 'slow' );
		});
		$( '.enterKeyLink', $c_zakn ).on( 'click', function() {
			$( '.new-api-key', $c_zakn ).hide();
			$( '.api-notice', $c_zakn ).fadeIn( 'slow' );
		});

		/** submit action **/
		$( "#requestKeyForm" ).submit( function( event ) {
			event.preventDefault();
			var $form = $( this ),
				$firstName = $form.find( '#firstName' ).val(),
				$lastName = $form.find( '#lastName' ).val(),
				$email = $form.find( '#emailAddr' ).val(),
				$alertBox = $( '.error-alerts' );
			// basic js checks before serverside verification.
			if ( ! $firstName ) {
				$alertBox.text( 'First name is required.' );
				return false;
			}
			if ( ! $lastName ) {
				$alertBox.text( 'Last name is required.' );
				return false;
			}
			if ( ! ( $email.indexOf( '@' ) > -1 && $email.indexOf( '.' ) > -1 ) ) {
				$alertBox.text( 'Please enter a valid e-mail address.' );
				return false;
			}
			var posting = $.post( IMHWPB.configs.asset_server + IMHWPB.configs.ajax_calls.generate_api_key,
				{
					first: $firstName,
					last: $lastName,
					email: $email,
				}
			);
			posting.done( function( response ) {
				if ( 400 === response.status ) {
					$alertBox.text( response.message );
				}
				if ( 200 === response.status ) {
					$( '.key-request-content' ).text( response.message );
				}
			});
		});

		//$.post( IMHWPB.configs.ajax_calls.generate_api_key)


		/**
		 * Bind events -
		 * When the submit button is pressed:
		 */
		$("#boldgrid-api-form").submit(function(e){
			e.preventDefault();
		});
		$( '#boldgrid-api-loading', $c_zakn ).hide();
		$('#submit_api_key', $c_zakn).on('click', function() {
			var api_key = $('#boldgrid_api_key', $c_zakn).val()
				.replace(/[^a-z0-9]/gi,'')
				.substr(0, 32)
				.replace(/(.{8})/g,"$1\-")
				.slice(0, - 1);
			self.set(api_key);
			// hide the button
			$( this ).hide();
			// show the loading graphic
			$('#boldgrid-api-loading', $c_zakn).show();
		});

		/**
		 * Function declaraions
		 */
		this.set = function(api_key) {
			var data = {
				'action'  : 'set_api_key',
				'api_key' :  api_key
			};

			$.post( ajaxurl, data, function(response) {
				// if the key was saved successfully
				if ('true' == response) {
					// change the notice from red to green
					$c_zakn.toggleClass('error').toggleClass(
							'updated');
					// then update the message
					$('#boldgrid_api_key_notice_message', $c_zakn)
						.html( 'Your api key has been saved successfully! <a onClick="window.location.reload(true)" style="cursor:pointer;"> Dismiss Notification</a>');
					// remove the loading graphic since success
					$('#boldgrid-api-loading', $c_zakn).fadeOut();
					// and finally hide the input elements as we
					// don't need them anymore.
					$('#boldgrid_api_key', $c_zakn).fadeOut();
					// reload page after 3 sec
					setTimeout( function(){
						window.location.reload();
					}, 3000 );

				} else if ('error saving key' == response) {
					// hide loading
					$('#boldgrid-api-loading', $c_zakn).hide();
					// show button
					$('#submit_api_key', $c_zakn).show();
					$('#boldgrid_api_key_notice_message', $c_zakn)
						.html( 'There was an error saving your key.<br />Please try entering your BoldGrid Connect Key again.');
				} else {
					// hide loading
					$('#boldgrid-api-loading', $c_zakn).hide();
					// show button
					$('#submit_api_key', $c_zakn).show();
					$('#boldgrid_api_key_notice_message', $c_zakn)
						.html( 'Your API key appears to be invalid!<br />Please try to enter your BoldGrid Connect Key again.');

				}
			});
		};
	})( jQuery );
};

new IMHWPB.Api();
