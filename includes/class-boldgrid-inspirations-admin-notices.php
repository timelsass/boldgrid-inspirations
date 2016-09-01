<?php
/**
 * BoldGrid Source Code
 *
 * @package BoldGrid_Inspirations_Admin_Notices
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Inspirations Admin Notices class .
 */
class Boldgrid_Inspirations_Admin_Notices {

	/*
	 * Current user id.
	 *
	 * @since 1.2.5
	 * @access public
	 * @var int $current_user_id
	 */
	public $current_user_id = 0;

	/**
	 * Constructor.
	 *
	 * @since 1.2.5
	 */
	public function __construct() {
		$this->current_user_id = get_current_user_id();
	}

	/**
	 * Add hooks.
	 */
	public function add_hooks() {
		if ( is_admin() ) {
			// Allow BoldGrid Admin Notices to be dismissed and remembered.
			add_action( 'wp_ajax_dismiss_boldgrid_admin_notice',
				array (
					$this,
					'dismiss_boldgrid_admin_notice_callback',
				)
			);

			// Add the javascript that dismissed admin notices via ajax.
			add_action( 'admin_enqueue_scripts',
				array(
					$this,
					'admin_enqueue_scripts',
				)
			);
		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_enqueue_scripts() {
		// Add the javascript that dismissed admin notices via ajax.
		wp_enqueue_script(
			'boldgrid-admin-notices',
			plugins_url(
				'assets/js/boldgrid-admin-notices.js',
				BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php'
			),
			array(),
			BOLDGRID_INSPIRATIONS_VERSION,
			true
		);
	}

	/**
	 * Allow BoldGrid Admin Notices to be dismissed and remembered.
	 *
	 * @param int $_POST['id'] The admin notice id.
	 */
	public function dismiss_boldgrid_admin_notice_callback() {
		global $wpdb;

		// Abort if we did not pass in an admin notice id.
		if ( ! isset( $_POST['id'] ) ) {
			echo 'false';
			wp_die();
		}

		// Sanitize the data key.
		$id = sanitize_key( $_POST['id'] );

		// If we have not dismissed this notice before, add it to the array and save the option.
		if ( ! $this->has_been_dismissed( $id ) ) {
			$time = time();

			// Get our array of dismissed notices.
			$boldgrid_dismissed_admin_notices = get_option( 'boldgrid_dismissed_admin_notices' );

			// Add the notice to the array.
			$boldgrid_dismissed_admin_notices[ $this->current_user_id ][$time] = $id;

			// Update the WP option.
			update_option( 'boldgrid_dismissed_admin_notices', $boldgrid_dismissed_admin_notices );
		}

		wp_die( 'true' );
	}

	/**
	 * Return wheather or not an admin notice has been dismissed.
	 *
	 * @param string $id An admin notice id.
	 * @return bool
	 */
	public function has_been_dismissed( $id ) {
		$boldgrid_dismissed_admin_notices = get_option( 'boldgrid_dismissed_admin_notices' );

		// If we have no dismissed notices, return false.
		if( false === $boldgrid_dismissed_admin_notices ) {
			return false;
		}

		// If this user has never dismissed anything, return false.
		if( ! isset( $boldgrid_dismissed_admin_notices[ $this->current_user_id ] ) ) {
			return false;
		}

		/*
		 * Dismissed notices can be stored in two ways:
		 * # $dismissed[user_id][timestamp] = notice_id;
		 * # $dismissed[user_id][notice_id] = timestamp;
		 *
		 * If either of the above is set, the user has dimissed the notice, so return true. Otherwise,
		 * return false.
		 */
		$format_1_dismissed = in_array( $id, $boldgrid_dismissed_admin_notices[ $this->current_user_id ], true );
		$format_2_dismissed = array_key_exists( $id, $boldgrid_dismissed_admin_notices[ $this->current_user_id ] );

		if( $format_1_dismissed || $format_2_dismissed ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Return boolean for BoldGrid connection issue.
	 *
	 * @return bool
	 */
	public function boldgrid_connection_issue_exists() {
		return ! get_site_transient( 'boldgrid_available' );
	}
}
