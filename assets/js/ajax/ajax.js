var IMHWPB = IMHWPB || {};

IMHWPB.Ajax = function( configs ) {
	var self = this;

	this.configs = configs;
	this.api_url = this.configs.asset_server;
	this.api_key = this.configs.api_key;
	this.site_hash = this.configs.site_hash;

	/**
	 * Execute an AJAX call.
	 *
	 * @param array    data           Data to sent via POST.
	 * @param string   requestUrlKey  Configuration index for the URL.
	 * @param function successAction  The success/done callback function.
	 * @param function errorAction    The errorAction/fail callback function.
	 * @param function completeAction The completeAction/always callback function.
	 */
	this.ajaxCall = function( data, requestUrlKey, successAction, errorAction, completeAction ) {
		self.data = data || {};
		self.requestUrlKey = requestUrlKey;

		if ( 'function' !== typeof errorAction ) {
			errorAction = self.errorAction;
		}
		if ( 'function' !== typeof completeAction ) {
			completeAction = function() {
				/** No Default * */
			};
		}

		data.key = self.api_key;
		data.site_hash = self.site_hash;

		jQuery.ajax( {
			type : 'POST',
			url : self.api_url + self.configs.ajax_calls[requestUrlKey],
			data : data,
			timeout : 120000,
			requestHeaders : {
				Accept : 'application/json'
			},
			dataType : 'json',
		} )
			.done( successAction )
			.fail ( [
				errorAction,
				self.errorLogAction
			] )
			.always( completeAction );
	};

	/**
	 * Error handling.
	 */
	this.errorAction = function( jqXHR, textStatus, errorThrown ) {
		var message, $wpbody;

		switch ( textStatus ) {
			case 'timeout':
				message = 'Ajax error: timeout. Please try your request again.';
				break;

			case 'parsererror':
				message = 'Ajax error: Unexpected return. In some cases, trying your request again may help.';
				break;

			case 'error':
				break;

			default:
				break;
		}

		if ( window.parent.jQuery( '#wpbody-content' ).length ) {
			$wpbody = window.parent.jQuery( '#wpbody-content' );
		} else {
			$wpbody = jQuery( '#wpbody-content' );
		}

		// Provide a friendly error for comm failure, if notice is not already displayed.
		if ( ! window.parent.jQuery( '#container_boldgrid_connection_notice' ).length  &&
			! jQuery( '#container_boldgrid_connection_notice' ).length ) {
				if ( message ) {
					message += '<br />';
				} else {
					message = '';
				}

				$wpbody
					.hide()
					.before(
						'<div id="container_boldgrid_connection_notice" class="error"><h2 class="dashicons-before dashicons-admin-network">BoldGrid Connection Issue</h2><p>There was an issue reaching the BoldGrid Connect server. Some BoldGrid features may be temporarily unavailable. Please try again in a moment.</p><p>If the issue persists, then please feel free to check our <a target="_blank" href="https://www.boldgrid.com/">BoldGrid Status</a> page.</p><p>' +
						message + '<button class="button button-primary" onclick="location.reload();">Try Again</button></p></div>'
					);
		}
	};

	/**
	 * Error log handling.
	 */
	this.errorLogAction = function( jqXHR, textStatus, errorThrown ) {
		var jqxhr2,
			data = {
				action : 'check_asset_server'
			};

		data.request = {
			data : self.data,
			requestUrlKey : self.requestUrlKey
		}

		data.response = {
			textStatus : textStatus
		}

		jQuery.post( ajaxurl, data, null, 'json' );
	};
};
