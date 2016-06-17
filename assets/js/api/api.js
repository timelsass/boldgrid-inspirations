var IMHWPB = IMHWPB || {};

IMHWPB.Api = function( configs ) {
	( function( $ ) {
		var self = this;

		/**
		 * Set key if parameter is set.
		 */
		$( function() {
			var $activateKey = self.GetURLParameter( 'activateKey' ),
			    container = $( 'container_boldgrid_api_key_notice' );
			if ( $activateKey ) {
				document.getElementById( 'boldgrid_api_key' ).value=$activateKey;
				//$( '#boldgrid_api_key' ).val( $ac)
				$( '#submit_api_key' ).click();
			}
		});

		/**
		 * Get parameter from URL
		 *
		 * @link http://www.jquerybyexample.net/2012/06/get-url-parameters-using-jquery.html
		 */
		this.GetURLParameter = function(sParam) {
			var sPageURL = window.location.search.substring( 1 );
			var sURLVariables = sPageURL.split( '&' );
			for ( var i = 0; i < sURLVariables.length; i++ ) {
				var sParameterName = sURLVariables[i].split( '=' );
				if ( sParameterName[0] == sParam ) {
					return sParameterName[1];
				}
			}
		};

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
				$link = $form.find( '#siteUrl' ).val(),
				$alertBox = $( '.error-alerts' );
				$('.error-color').removeClass( 'error-color' );
			// basic js checks before serverside verification.
			if ( ! $firstName ) {
				$alertBox.text( 'First name is required.' );
				$form.find( '#firstName' ).prev().addClass( 'error-color' );
				return false;
			}
			if ( ! $lastName ) {
				$alertBox.text( 'Last name is required.' );
				$form.find( '#lastName' ).prev().addClass( 'error-color' );
				return false;
			}
			if ( ! ( $email.indexOf( '@' ) > -1 && $email.indexOf( '.' ) > -1 ) ) {
				$alertBox.text( 'Please enter a valid e-mail address.' );
				$form.find( '#emailAddr' ).prev().addClass( 'error-color' );
				return false;
			}
			var posting = $.post( IMHWPB.configs.asset_server + IMHWPB.configs.ajax_calls.generate_api_key,
				{
					first: $firstName,
					last: $lastName,
					email: $email,
					link: $link,
				}
			);
			posting.done( function( response ) {
				if ( 400 === response.status ) {
					if ( response.message.indexOf( 'name' ) >= 0 ) {
						$form.find( '#firstName, #lastName' ).prev().addClass( 'error-color' );
					}
					if ( response.message.indexOf( 'e-mail' ) >= 0 ) {
						$form.find( '#emailAddr' ).prev().addClass( 'error-color' );
					}
					$alertBox.text( response.message );
				}
				if ( 200 === response.status ) {
					$( '.key-request-content' ).text( response.message );
				}
			});
		});



		/**
		 * Bind events -
		 * When the submit button is pressed:
		 */
		$( '#boldgrid-api-form' ).submit( function( e ){
			e.preventDefault();
		});

		$( '#boldgrid-api-loading', $c_zakn ).hide();
		$( '#submit_api_key', $c_zakn ).on('click', function() {
			if ( ! $( '#tos-box:checked').length  ) {
				$( '#boldgrid_api_key_notice_message', $c_zakn )
					.html( 'You must agree to the Terms of Service before continuing.' );
				return false;
			}
			var api_key = $( '#boldgrid_api_key', $c_zakn ).val()
				.replace( /[^a-z0-9]/gi,'' )
				.substr( 0, 32 )
				.replace( /(.{8})/g,"$1\-" )
				.slice( 0, - 1 );
			self.set( api_key );
			// hide the button
			$( this ).hide();
			// show the loading graphic
			$( '#boldgrid-api-loading', $c_zakn ).show();
		});

		/**
		 * Function declaraions
		 */
		this.set = function( api_key ) {
			var data = {
				'action'  : 'set_api_key',
				'api_key' :  api_key
			};

			$.post( ajaxurl, data, function( response ) {
				// if the key was saved successfully
				if ( 'true' == response ) {
					// change the notice from red to green
					$c_zakn.toggleClass( 'error' ).toggleClass( 'updated' );
					// then update the message
					$( '#boldgrid_api_key_notice_message', $c_zakn )
						.html( 'Your api key has been saved successfully! <a onClick="window.location.reload(true)" style="cursor:pointer;"> Dismiss Notification</a>' );
					// remove the loading graphic since success
					$( '#boldgrid-api-loading', $c_zakn ).fadeOut();
					// and finally hide the input elements as we
					// don't need them anymore.
					$( '#boldgrid_api_key', $c_zakn ).fadeOut();
					// reload page after 3 sec
					setTimeout( function() {
						window.location.reload();
					}, 3000 );

				} else if ( 'error saving key' == response ) {
					// hide loading
					$( '#boldgrid-api-loading', $c_zakn ).hide();
					// show button
					$( '#submit_api_key', $c_zakn ).show();
					$( '#boldgrid_api_key_notice_message', $c_zakn )
						.html( 'There was an error saving your key.<br />Please try entering your BoldGrid Connect Key again.' );
				} else {
					// hide loading
					$( '#boldgrid-api-loading', $c_zakn ).hide();
					// show button
					$( '#submit_api_key', $c_zakn ).show();
					$( '#boldgrid_api_key_notice_message', $c_zakn )
						.html( 'Your API key appears to be invalid!<br />Please try to enter your BoldGrid Connect Key again.');

				}
			});
		};
	})( jQuery );
};

new IMHWPB.Api();
