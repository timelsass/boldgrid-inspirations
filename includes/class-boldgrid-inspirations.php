<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-config.php';

/**
 * BoldGrid Inspirations class.
 */
class Boldgrid_Inspirations {

	/**
	 * Array of BoldGrid specific configs.
	 *
	 * @access protected
	 * @var array
	 */
	protected $configs = null;

	/**
	 * Class property for $is_preview_server.
	 *
	 * @var bool
	 */
	public $is_preview_server = false;

	/**
	 * Set the required PHP version.
	 *
	 * @access private
	 * @var string
	 * @static
	 */
	private static $required_php_version = '5.3';

	/**
	 * Set the required WordPress version.
	 *
	 * @access private
	 * @var string
	 * @static
	 */
	private static $required_wp_version = '4.2';

	/**
	 * The api class object.
	 *
	 * @since 1.2.2
	 * @access public
	 * @var Boldgrid_Inspirations_Api
	 */
	public $api;

	/**
	 * Constructor.
	 *
	 * @see Boldgrid_Inspirations_Api::verify_api_key().
	 * @see Boldgrid_Inspirations_Api::set_is_asset_server_available().
	 */
	public function __construct() {
		// Include the utility class.
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-utility.php';

		// Include the api class.
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-api.php';

		// Instantiate Boldgrid_Inspirations_Api.
		$this->api = new Boldgrid_Inspirations_Api( $this );

		// Get configs and set in a class property.
		$this->configs = Boldgrid_Inspirations_Config::get_format_configs();

		// Add a filter for html.
		add_filter( 'wp_kses_allowed_html', array (
			$this,
			'filter_allowed_html'
		), 10, 2 );

		// Set some class properties.
		$this->set_is_preview_server();
		$this->set_asset_user_id();
		$this->api->set_api_key_hash();

		// Initialize $is_asset_server_available; set class property from transient.
		$is_asset_server_available = ( bool ) ( is_multisite() ? get_site_transient(
			'boldgrid_available' ) : get_transient( 'boldgrid_available' ) );

		// If we had communication issues, then check now; it may be better.
		if ( false === $is_asset_server_available ) {
			// Verify API key, which connects to the asset server and sets the status.
			$this->api->verify_api_key();
		} else {
			Boldgrid_Inspirations_Api::set_is_asset_server_available( true );

			// Ensure all activation data was sent.
			if ( true === function_exists( 'wp_get_current_user' ) &&
				 false !== ( $current_user = wp_get_current_user() ) ) {
				$first_login_ts = strtotime(
					get_user_meta( $current_user->ID, 'first_login', true ) );

				// If the first login was made in the last 30 seconds, then verify activation.
				if ( $first_login_ts + 30 > time() ) {
					$_GET['force-check'] = 1;
					$this->api->verify_api_key();
				}
			}
		}
	}

	/**
	 * Get configuration settings.
	 *
	 * @return array Configuration array.
	 */
	public function get_configs() {
		return $this->configs;
	}

	/**
	 * Set configuration settings.
	 *
	 * @param array $configs Configuration array.
	 *
	 * @return bool
	 */
	public function set_configs( $configs ) {
		$this->configs = $configs;

		return true;
	}

	/**
	 * Get the asset server grid file styles url
	 *
	 * @return string
	 */
	public function get_grid() {
		$configs = $this->get_configs();
		return $configs['asset_server'] . '/static/grid.css';
	}

	/**
	 * Enqueue grid css style
	 */
	public function enqueue_site_grid() {
		wp_register_style( 'grid-system-imhwpb', $this->get_grid(), array (),
			BOLDGRID_INSPIRATIONS_VERSION );
		wp_enqueue_style( 'grid-system-imhwpb' );
	}

	/**
	 * Set asset user id
	 */
	public function set_asset_user_id() {
		$this->asset_user_id = ( isset( $_POST['asset_user_id'] ) ? intval(
			$_POST['asset_user_id'] ) : null );
	}

	/**
	 */
	public function set_is_preview_server() {
		$boldgrid_configs = $this->get_configs();
		$host = ! empty( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';

		$this->is_preview_server = ( $boldgrid_configs['preview_server'] == "https://" . $host ||
			 $boldgrid_configs['author_preview_server'] == "https://" . $host );
	}

	/**
	 * Add to list of allowed attributes
	 *
	 * @param array $allowed
	 * @param array $context
	 * @return array
	 */
	public function filter_allowed_html( $allowed, $context ) {
		if ( is_array( $context ) ) {
			return $allowed;
		}

		if ( 'post' === $context || 'page' === $context ) {
			// Example case
			$allowed['img']['data-imhwpb-asset-id'] = true;
			$allowed['img']['data-imhwpb-built-photo-search'] = true;
			$allowed['img']['data-image-provider-id'] = true;
			$allowed['img']['data-id-from-provider'] = true;
		}

		return $allowed;
	}

	/**
	 * Allow Filter data attibutes
	 *
	 * @param string $context
	 * @return mixed
	 */
	public function wp_kses_allowed_html( $context = '' ) {
		global $allowedposttags, $allowedtags, $allowedentitynames;

		if ( is_array( $context ) )
			return apply_filters( 'wp_kses_allowed_html', $context, 'explicit' );

		switch ( $context ) {
			case 'post' :
				return apply_filters( 'wp_kses_allowed_html', $allowedposttags, $context );
				break;

			case 'user_description' :

			case 'pre_user_description' :
				$tags = $allowedtags;
				$tags['a']['rel'] = true;
				return apply_filters( 'wp_kses_allowed_html', $tags, $context );
				break;

			case 'strip' :
				return apply_filters( 'wp_kses_allowed_html', array (), $context );
				break;

			case 'entities' :
				return apply_filters( 'wp_kses_allowed_html', $allowedentitynames, $context );
				break;

			case 'data' :
			default :

				return apply_filters( 'wp_kses_allowed_html', $allowedtags, $context );
		}
	}

	/**
	 * Check PHP version.
	 *
	 * @since 1.2.2
	 * @static
	 *
	 * @return bool Whether or not the current PHP version is supported.
	 */
	public static function is_php_compatible() {
		if ( version_compare( phpversion(), self::$required_php_version, '<' ) ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Check PHP and WordPress versions for compatibility
	 *
	 * @static
	 *
	 * @see self::is_php_compatible().
	 * @see $this->deactivate().
	 * @global string $wp_version The WordPress version string.
	 */
	public static function check_php_wp_version() {
		// Check that PHP is installed at our required version or deactivate and die.
		if ( true !== self::is_php_compatible() ) {
			self::deactivate(
				sprintf(
					esc_html__(
						'%sBoldGrid Inspirations%s requires PHP %s or greater.%s',
						'boldgrid-inspirations'
					),
					'<p><center><strong>',
					'</strong>',
					self::$required_php_version,
					'</center></p>'
				),
				esc_html__( 'Plugin Activation Error', 'boldgrid-inspirations' ),
				array (
					'response' => 200,
					'back_link' => TRUE
				)
			);
		}

		// Check to see if WordPress version is installed at our required minimum or deactivate and
		// die.
		global $wp_version;

		if ( version_compare( $wp_version, self::$required_wp_version, '<' ) ) {
			self::deactivate(
				sprintf(
					esc_html__(
						'%sBoldGrid Inspirations%s requires WordPress %s or higher.%s',
						'boldgrid-inspirations'
					),
					'<p><center><strong>',
					'</strong>',
					self::$required_wp_version,
					'</center></p>'
				),
				esc_html__( 'Plugin Activation Error', 'boldgrid-inspirations' ),
				array (
					'response' => 200,
					'back_link' => TRUE
				)
			);
		}
	}

	/**
	 * Deactivate and die.
	 *
	 * Used if PHP or WordPress version check fails.
	 *
	 * @since 1.2.2
	 * @access private
	 * @static
	 *
	 * @param string $message A message for wp_die to display.
	 * @param string $title A title for wp_die to display.
	 * @param array  $args A control array for wp_die.
	 */
	private static function deactivate( $message = '', $title = '', $args = array() ) {
		// Deactivate the plugin.
		deactivate_plugins( BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' );

		// If there is no message, then supply one.
		if ( true === empty( $message ) ) {
			$message = 'BoldGrid Inspirations ' . esc_html__(
				'has been deactivated.', 'boldgrid-inspirations'
			);
		}

		// If there is no title, then supply one.
		if ( true === empty( $title ) ) {
			$title = 'BoldGrid Inspirations ' . esc_html__(
				'Deactivated', 'boldgrid-inspirations'
			);
		}

		// If the array of arguments is empty, then create it.
		if ( true === empty( $args ) ) {
			$args = array (
				'response' => 200,
				'back_link' => TRUE
			);
		}

		wp_die( $message, $title, $args );
	}

	/**
	 * Is feedback opt-out.
	 *
	 * Check the BoldGrid Settings (a WP Option) to see if this site has opted-out for feedback.
	 *
	 * @since 1.0.9
	 *
	 * @return bool
	 */
	public static function is_feedback_optout() {
		// Get BoldGrid settings:
		$options = get_option( 'boldgrid_settings' );

		// Get feedback option:
		$boldgrid_feedback_optout = isset( $options['boldgrid_feedback_optout'] ) ? $options['boldgrid_feedback_optout'] : false;

		// Return the result:
		return ( bool ) $boldgrid_feedback_optout;
	}
}
