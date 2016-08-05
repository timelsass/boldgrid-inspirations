<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Api
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * BoldGrid Inspiration Api class.
 *
 * @since 1.2.2
 */
class Boldgrid_Inspirations_Api {
	/**
	 * The core BoldGrid Inspirations class object.
	 *
	 * @since 1.2.2
	 * @access public
	 * @var Boldgrid_Inspirations
	 */
	public $core;

	/**
	 * Class property for the API key hash.
	 *
	 * @since 1.2.2
	 * @access private
	 * @var bool
	 */
	private $api_key_hash = false;

	/**
	 * Class property for asset server availability.
	 *
	 * @since 1.2.2
	 * @access private
	 * @var bool
	 * @staticvar
	 */
	private static $is_asset_server_available = false;

	/**
	 * Boolean that identifies whether or not the use has passed api key validation.
	 *
	 * @since 1.2.2
	 * @access private
	 * @var bool
	 */
	private $passed_key_validation = false;

	/**
	 * Static class property $have_enqueued_api_key_prompt.
	 *
	 * @since 1.2.2
	 * @access private
	 * @var bool
	 * @staticvar
	 */
	private static $have_enqueued_api_key_prompt = false;

	/**
	 * Constructor.
	 *
	 * @since 1.2.2
	 *
	 * @param Boldgrid_Inspirations $core BoldGrid Inspirations class object.
	 */
	public function __construct( $core ) {
		// Save the Boldgrid_Inspirations object as a class property.
		$this->core = $core;
	}

	/**
	 * Get the value of the class property $is_asset_server_available.
	 *
	 * @since 1.2.2
	 *
	 * @return bool
	 */
	public static function get_is_asset_server_available() {
		return self::$is_asset_server_available;
	}

	/**
	 * Set the value of the class property $is_asset_server_available.
	 *
	 * @since 1.2.2
	 *
	 * @param bool $is_asset_server_available Whether or not the asset server is available.
	 * @return bool
	 */
	public static function set_is_asset_server_available( $is_asset_server_available ) {
		// Validate $is_asset_server_available.
		$is_asset_server_available = (bool) $is_asset_server_available;

		// Set the property.
		self::$is_asset_server_available = $is_asset_server_available;

		// Save the WP Option.
		if ( true === is_multisite() ) {
			set_site_transient( 'boldgrid_available', $is_asset_server_available, HOUR_IN_SECONDS );
		} else {
			set_transient( 'boldgrid_available', $is_asset_server_available, HOUR_IN_SECONDS );
		}

		return true;
	}

	/**
	 * Check the connection to the asset server.
	 *
	 * @since 1.2.2
	 * @see Boldgrid_Inspirations_Api::verify_api_key().
	 * @see Boldgrid_Inspirations_Api::get_is_asset_server_available().
	 */
	public function check_asset_server_callback() {
		// Verify API key, which connects to the asset server and sets the status.
		$this->verify_api_key();

		// Return status.
		return self::get_is_asset_server_available();
	}

	/**
	 * Accessor for $this->passed_key_validation.
	 *
	 * @since 1.2.2
	 *
	 * @return bool
	 */
	public function get_passed_key_validation() {
		return $this->passed_key_validation;
	}

	/**
	 * Setter for $this->passed_key_validation.
	 *
	 * @since 1.2.2
	 * @access private
	 *
	 * @param bool $passed Whether or not the key passed validation.
	 * @return bool
	 */
	private function set_passed_key_validation( $passed = false ) {
		$this->passed_key_validation = $passed;

		return true;
	}

	/**
	 * API key requirement check.
	 *
	 * If required, verify the stored API key with the asset server.
	 *
	 * @since 1.2.2
	 * @see Boldgrid_Inspirations_Api::set_passed_key_validation().
	 * @see Boldgrid_Inspirations_Config::get_format_configs().
	 * @see Boldgrid_Inspirations_Api::boldgrid_api_call().
	 *
	 * @param bool $api_key_required Whether or not the API key is required to pass the check.
	 * @param bool $is_boldgrid_api_data_new Whether or not the API data was just retrieved.
	 * @return bool
	 */
	public function passes_api_check( $api_key_required = false, $is_boldgrid_api_data_new = false ) {
		// If key is not required, then mark as validated and return true.
		if ( false === $api_key_required ) {
			$this->set_passed_key_validation( true );

			return true;
		}

		// Get the BoldGrid configuration array.
		$configs = Boldgrid_Inspirations_Config::get_format_configs();

		// Check for api data transient.
		$boldgrid_api_data = get_transient( 'boldgrid_api_data' );

		// If there is no transient data, then retrieve it from the asset server.
		if ( true === empty( $boldgrid_api_data ) ) {
			$boldgrid_api_data = self::boldgrid_api_call( $configs['ajax_calls']['get_version'] );

			$is_boldgrid_api_data_new = true;
		}

		// Check if we have valid API data.
		if ( true === isset( $boldgrid_api_data->status ) && 200 === $boldgrid_api_data->status ) {
			// If we did not have a site hash, but got one in the return, then set the WP Option.
			if ( true === empty( $configs['site_hash'] ) &&
			false === empty( $boldgrid_api_data->result->data->site_hash ) ) {
				update_option( 'boldgrid_site_hash', $boldgrid_api_data->result->data->site_hash );
			}

			// If we just retrieved new data, then update reseller option.
			if ( true === $is_boldgrid_api_data_new || true === isset( $_REQUEST['force-check'] ) ) {
				$boldgrid_reseller_array = array();

				foreach ( $boldgrid_api_data->result->data as $key => $value ) {
					if ( 1 === preg_match( '/^reseller_/', $key ) ) {
						$boldgrid_reseller_array[ $key ] = $boldgrid_api_data->result->data->$key;
					}
				}

				// Set the reseller option from api data, or delete if no reseller data.
				if ( count( $boldgrid_reseller_array ) ) {
					update_option( 'boldgrid_reseller', $boldgrid_reseller_array );
				} else {
					delete_option( 'boldgrid_reseller' );
				}
			}

			// Mark as validated and return true.
			$this->set_passed_key_validation( true );

			return true;
		} else {
			// API key did not verify or received a bad response, so fail.
			$this->set_passed_key_validation( false );

			return false;
		}
	}

	/**
	 * Connects to the BoldGrid API and returns the response in an array.
	 *
	 * @since 1.2.2
	 * @see Boldgrid_Inspirations_Config::get_format_configs().
	 * @see Boldgrid_Inspirations_Api::hash_api_key().
	 * @see Boldgrid_Inspirations_Api::set_is_asset_server_available().
	 *
	 * @param string $api_path The API path to call.
	 * @param bool   $json_array The return format; object (default) or array.
	 * @param string $params_array An optional array of parameters to include.
	 * @param string $method The request method; GET (default) or POST.
	 * @return object|array|false
	 */
	public static function boldgrid_api_call( $api_path, $json_array = false, $params_array = array(), $method = 'GET' ) {
		// If this is a BoldGrid Inspirations plugin version check, then check if we already have recent data,
		// return it if so, and not a force-check.
		if ( '/api/plugin/check-version' === $api_path ) {
			// Get api data transient.
			if ( true === is_multisite() ) {
				$boldgrid_api_data = get_site_transient( 'boldgrid_api_data' );
			} else {
				$boldgrid_api_data = get_transient( 'boldgrid_api_data' );
			}

			// If the API data was just retrieved (last 5 seconds) and is ok, then just return it.
			if ( false === empty( $boldgrid_api_data ) &&
			false === (
				true === isset( $_GET['force-check'] ) &&
				true === isset( $boldgrid_api_data->updated ) &&
				$boldgrid_api_data->updated < time() - 5
			) ) {
				return $boldgrid_api_data;
			}
		}

		// Get the BoldGrid configuration array.
		$configs = Boldgrid_Inspirations_Config::get_format_configs();

		// Set the API key.
		if ( true === isset( $_POST['api_key'] ) ) {
			// On activation.
			$api_key_hash = self::hash_api_key( $_POST['api_key'] );
		} elseif ( true === isset( $_POST['key'] ) ) {
			// POST of the hash.
			$api_key_hash = sanitize_text_field( $_POST['key'] );
		} elseif ( true === isset( $_GET['key'] ) ) {
			// GET of the hash.
			$api_key_hash = sanitize_text_field( $_GET['key'] );
		} else {
			// From configs.
			$api_key_hash = ( true === isset( $configs['api_key'] ) ? $configs['api_key'] : null );
		}

		// Build the GET parameters.
		if ( false === empty( $api_key_hash ) ) {
			$params_array['key'] = $api_key_hash;
		}

		if ( false === empty( $configs['site_hash'] ) ) {
			$params_array['site_hash'] = $configs['site_hash'];
		}

		// If getting plugin version information, include other parameters.
		if ( 1 === preg_match( '/(check|get-plugin)-version/', $api_path ) ) {
			// Get BoldGrid settings.
			$options = get_option( 'boldgrid_settings' );

			// Include update release and theme channels.
			$params_array['channel'] = (
				false === empty( $options['release_channel'] ) ?
				$options['release_channel'] : 'stable'
			);

			$params_array['theme_channel'] = (
				false === empty( $options['theme_release_channel'] ) ?
				$options['theme_release_channel'] : 'stable'
			);

			// If get_plugin_data does not exist, then load it.
			if ( false === function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			// Get the installed plugin data.
			$plugin_data = get_plugin_data( BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php', false );

			$params_array['installed_core_version'] = $plugin_data['Version'];

			// Get the WordPress version.
			global $wp_version;

			$params_array['installed_wp_version'] = $wp_version;

			// Get PHP version.
			$params_array['installed_php_version'] = phpversion();

			// Include feedback opt-out setting.
			$params_array['feedback_optout'] = (
				true === isset( $options['boldgrid_feedback_optout'] ) ?
				$options['boldgrid_feedback_optout'] : '0'
			);

			// If allowed, then include feedback info.
			if ( true === empty( $params_array['feedback_optout'] ) ) {
				// Include activation/update information.
				if ( true === function_exists( 'wp_get_current_user' ) &&
				false !== ( $current_user = wp_get_current_user() ) ) {
					$params_array['first_login'] = get_user_meta( $current_user->ID, 'first_login',
						true
					);
					$params_array['last_login'] = get_user_meta( $current_user->ID, 'last_login',
						true
					);
					$params_array['user_login'] = $current_user->user_login;
					$params_array['user_email'] = $current_user->user_email;
				}

				// Mobile ratio.
				if ( true === is_multisite() ) {
					$mobile_ratio = get_site_option( 'boldgrid_mobile_ratio' );
				} else {
					$mobile_ratio = get_option( 'boldgrid_mobile_ratio' );
				}

				if ( false === empty( $mobile_ratio ) ) {
					$params_array['mobile_ratio'] = $mobile_ratio;
				}
			}
		}

		// Set the complete URL.
		$url = $configs['asset_server'] . $api_path;

		// Make a call to the asset server.
		if ( 'POST' === $method ) {
			$boldgrid_api_data = wp_remote_retrieve_body(
				wp_remote_post( $url,
					array(
						'body' => $params_array,
					)
				)
			);
		} else {
			// Convert the params array into a query string.
			if ( false === empty( $params_array ) ) {
				$params = http_build_query( $params_array );
			}

			// Append the params query string to the URL.
			$url .= '?' . $params;

			// Make the call.
			$boldgrid_api_data = wp_remote_retrieve_body( wp_remote_get( $url ) );
		}

		// Decode the JSON returned into an object.
		$boldgrid_api_data_object = json_decode( $boldgrid_api_data );

		// Check asset server availability.
		if ( true === isset( $boldgrid_api_data_object->status ) ) {
			Boldgrid_Inspirations_Api::set_is_asset_server_available( true );
		} else {
			Boldgrid_Inspirations_Api::set_is_asset_server_available( false );

			// Log.
			error_log( __METHOD__ . ': Asset server is unavailable.' );
		}

		// Decode the JSON data.
		$boldgrid_api_data = json_decode( $boldgrid_api_data, $json_array );

		// If this was a BoldGrid Inpirations plugin version check, then store only valid data.
		if ( '/api/plugin/check-version' === $api_path &&
		true === isset( $boldgrid_api_data->status ) &&
		200 === $boldgrid_api_data->status ) {
			// Add the current timestamp (in seconds).
			$boldgrid_api_data->updated = time();

			// Set api data transient, expired in 8 hours.
			if ( true === is_multisite() ) {
				delete_site_transient( 'boldgrid_api_data' );
				set_site_transient( 'boldgrid_api_data', $boldgrid_api_data, 8 * HOUR_IN_SECONDS );
			} else {
				delete_transient( 'boldgrid_api_data' );
				set_transient( 'boldgrid_api_data', $boldgrid_api_data, 8 * HOUR_IN_SECONDS );
			}
		}

		// Return the object.
		return $boldgrid_api_data;
	}

	/**
	 * Validates the API key and returns details on if it is valid as well as version.
	 *
	 * @since 1.2.2
	 * @see Boldgrid_Inspirations_Update::update_api_data().
	 * @see Boldgrid_Inspirations_Api::passes_api_check().
	 *
	 * @return object|string The BoldGrid API Data object or a message string on failure.
	 */
	public function verify_api_key() {
		// Include the update class.
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-update.php';

		// Make an API call for API data.
		$boldgrid_api_data = Boldgrid_Inspirations_Update::update_api_data();

		// Handle the response.
		if ( false !== $boldgrid_api_data ) {
			// Check response.
			if ( 200 === $boldgrid_api_data->status && 'OK' === $boldgrid_api_data->message ) {
				$boldgrid_api_data->license_status = true;

				// Process post api key verification checks.
				$this->passes_api_check( true, true );
			} elseif ( 'Unauthorized' === $boldgrid_api_data->message ) {
				$boldgrid_api_data->license_status = false;
			} else {
				// Log.
				error_log(
					__METHOD__ .
					': Error: Error when getting version information.  $boldgrid_api_data: ' .
					print_r( $boldgrid_api_data, true )
				);

				return 'Error when getting version info';
			}
		} else {
			return 'api call failed';
		}

		return $boldgrid_api_data;
	}

	/**
	 * Load the necessary resources in the admin section when prompting the user for an api key.
	 *
	 * @since 1.2.2
	 * @see Boldgrid_Inspirations_Api::get_is_asset_server_available().
	 * @see /assets/js/api/api.js
	 */
	public function add_hooks_to_prompt_for_api_key() {
		// At this point, we've decided that we need to ask the user for an api key.
		// Let's only ask them once! IE don't show two admin notices asking for a key.
		if ( true !== self::$have_enqueued_api_key_prompt ) {
			// If the asset server is available and there is no site hash, then ask for the api key, else notify.
			if ( true === self::get_is_asset_server_available() ) {
				// Add a message to the dashboard that asks for the api key.
				add_action( 'admin_notices',
					array(
						$this,
						'prompt_for_api_key',
					)
				);

				// Load javascript that handles ajax submission of api key.
				add_action( 'admin_enqueue_scripts',
					array(
						$this,
						'enqueue_js_api_submission',
					)
				);

				// Action to handle submission of key via ajax.
				add_action( 'wp_ajax_set_api_key',
					array(
						$this,
						'set_api_key_callback',
					)
				);

				// Remember that we already printed the notice.
				self::$have_enqueued_api_key_prompt = true;
			} else {
				// Notify that there is a connection issue.
				add_action( 'admin_notices',
					array(
						$this,
						'notify_connection_issue',
					)
				);
			}
		}
	}

	/**
	 * Print a notice asking the user to input their api_key.
	 *
	 * @since 1.2.2
	 */
	public function prompt_for_api_key() {
		// Get current user.
		$current_user = wp_get_current_user();

		// E-mail is always checked and is a required wp field for user.
		$email = $current_user->user_email;

		// First name if exists from user.
		$first_name = ( true === empty( $current_user->user_firstname ) ? '' : $current_user->user_firstname );

		// Last name if exists from user.
		$last_name = ( true === empty( $current_user->user_lastname ) ? '' : $current_user->user_lastname );

		// Display the notice.
		include dirname( __FILE__ ) . '/partial-page/boldgrid-inspirations-api-prompt.php';
	}

	/**
	 * Print a notice for connection issues.
	 *
	 * @since 1.2.2
	 */
	public function notify_connection_issue() {
		include BOLDGRID_BASE_DIR . '/pages/templates/boldgrid_connection_issue.php';
	}

	/**
	 * Load api.js script.
	 *
	 * @since 1.2.2
	 */
	public function enqueue_js_api_submission() {
		wp_enqueue_script( 'api-submission',
			plugins_url(
				'/assets/js/api/api.js',
				BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php'
			),
			array(), BOLDGRID_INSPIRATIONS_VERSION, true
		);
	}

	/**
	 * Store the user's api_key as wp_option.
	 *
	 * This function is called via ajax.
	 *
	 * @since 1.2.2
	 * @see Boldgrid_Inspirations_Api::hash_api_key().
	 * @see Boldgrid_Inspirations_Api::verify_api_key().
	 */
	public function set_api_key_callback() {
		// Set messages.
		$messages = array(
			'success' => esc_html__(
				'Your api key has been saved successfully.'
				, 'boldgrid-inspirations'
			),
			'invalid_key' => sprintf(
				esc_html__(
					'Your API key appears to be invalid!%sPlease try to enter your BoldGrid Connect Key again.'
					, 'boldgrid-inspirations'
				),
				'<br />'
			),
			'error_saving_key' => sprintf(
				esc_html__(
					'There was an error saving your key.%sPlease try entering your BoldGrid Connect Key again.'
					, 'boldgrid-inspirations'
				),
				'<br />'
			),
			'nonce_failed' => esc_html__(
				'Security violation (invalid nonce).'
				, 'boldgrid-inspirations'
			),
		);

		// Verify nonce.
		if ( false === isset( $_POST['set_key_auth'] ) ||
		1 !== check_ajax_referer( 'boldgrid_set_key', 'set_key_auth', false ) ) {
			echo $messages['nonce_failed'];

			wp_die();
		}

		// Check input API key.
		if ( true === empty( $_POST['api_key'] ) ) {
			// Failure.
			echo wp_json_encode(
				array(
					'success' => false,
					'error' => 'invalid_key',
					'message' => $messages['invalid_key'],
				)
			);

			wp_die();
		}

		$api_key_hash = self::hash_api_key( $_POST['api_key'] );

		if ( true === empty( $api_key_hash ) ) {
			// Failure.
			echo wp_json_encode(
				array(
					'success' => false,
					'error' => 'invalid_key',
					'message' => $messages['invalid_key'],
				)
			);

			wp_die();
		}

		// Delete the boldgrid_api_data transient.
		if ( true === is_multisite() ) {
			delete_site_transient( 'boldgrid_api_data' );
		} else {
			delete_transient( 'boldgrid_api_data' );
		}

		// Verify the key.
		$boldgrid_api_data = $this->verify_api_key();

		// Interpret result.
		if ( 'OK' === $boldgrid_api_data->message ) {
			// Success.
			echo wp_json_encode(
				array(
					'success' => true,
					'message' => $messages['success'],
				)
			);

			// Update the API key option.
			update_option( 'boldgrid_api_key', $api_key_hash );
		} elseif ( 'Unauthorized' === $boldgrid_api_data->message ) {
			// Failure.
			echo wp_json_encode(
				array(
					'success' => false,
					'error' => 'invalid_key',
					'message' => $messages['invalid_key'],
				)
			);
		} elseif ( 'api call failed' === $boldgrid_api_data ) {
			// Failure.
			echo wp_json_encode(
				array(
					'success' => false,
					'error' => 'invalid_key',
					'message' => $messages['invalid_key'],
				)
			);
		} elseif ( false === is_object( $boldgrid_api_data ) ) {
			// Log.
			error_log(
				__METHOD__ . ': Error: $boldgrid_api_data is not an object.  $boldgrid_api_data: ' .
				print_r( $boldgrid_api_data, true )
			);

			echo wp_json_encode(
				array(
					'success' => false,
					'error' => 'error_saving_key',
					'message' => $messages['error_saving_key'],
				)
			);
		} else {
			// Failure.
			echo wp_json_encode(
				array(
					'success' => false,
					'error' => 'error_saving_key',
					'message' => $messages['error_saving_key'],
				)
			);
		}

		wp_die();
	}

	/**
	 * Hash API Key.
	 *
	 * @param string $api_key A BoldGrid Connect Key to be hashed.
	 *
	 * @return string|bool MD5 hash representation of a BoldGrid Connect Key, or FALSE on error.
	 */
	public static function hash_api_key( $api_key = null ) {
		// Trim the input.
		$api_key = trim( $api_key );

		// Convert to lowercase.
		$api_key = strtolower( $api_key );

		// Remove dashes/hyphens from the input API Key.
		$api_key = preg_replace( '#-#', '', $api_key );

		// Check for the correct number of chars (32).
		if ( 32 !== strlen( $api_key ) ) {
			return false;
		}

		// Add dashes to the API Key.
		$api_key = rtrim( chunk_split( $api_key, 8, '-' ), '-' );

		// Hash the API Key.
		$api_key_hash = md5( $api_key );

		return $api_key_hash;
	}

	/**
	 * Set the BoldGrid Connect Key hash.
	 *
	 * @since 1.2.2
	 * @see Boldgrid_Inspirations_Config::get_format_configs().
	 */
	public function set_api_key_hash() {
		// Get the BoldGrid configuration array.
		$configs = Boldgrid_Inspirations_Config::get_format_configs();

		// REQUIRED - we need authorization.
		// Look in the config for the api_key.
		$this->api_key_hash = ( true === isset( $configs['api_key'] ) ? $configs['api_key'] : null );
		// If it's not there, check $_REQUEST['key'].
		$this->api_key_hash = (
			( true === empty( $this->api_key_hash ) && true === isset( $_REQUEST['key'] ) ) ?
			sanitize_text_field( $_REQUEST['key'] ) : $this->api_key_hash
		);
	}

	/**
	 * Get the BoldGrid Connect Key hash.
	 *
	 * @since 1.2.2
	 *
	 * @return string|false Hash representation of the BoldGrid Connect Key, or FALSE on error.
	 */
	public function get_api_key_hash() {
		// If an API key is stored, then return it.
		if ( false === empty( $this->api_key_hash ) ) {
			return $this->api_key_hash;
		}

		// Attempt to set the hash.
		$this->set_api_key_hash();

		// If an API key is now stored, then return it.
		if ( false === empty( $this->api_key_hash ) ) {
			return $this->api_key_hash;
		} else {
			return false;
		}
	}
}
