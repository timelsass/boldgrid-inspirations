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

		// If you are not at least an Editor, you may not dismiss notices.
		if( ! current_user_can( 'edit_pages' ) ) {
			echo 'false';
			wp_die();
		}

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
			$boldgrid_dismissed_admin_notices[$time] = $id;

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

		if ( ! $boldgrid_dismissed_admin_notices ||
		(
			! in_array( $id, $boldgrid_dismissed_admin_notices, true ) &&
			! array_key_exists( $id, $boldgrid_dismissed_admin_notices )
		) ) {
			return false;
		} else {
			return true;
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
