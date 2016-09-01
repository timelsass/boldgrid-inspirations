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

		// Abort if we did not pass in an admin notice id.
		if ( ! isset( $_POST['id'] ) ) {
			echo 'false';
			wp_die();
		}

		// Sanitize the data key.
		$id = sanitize_key( $_POST['id'] );

		// Add user meta to log that this user dismissed this notice.
		add_user_meta( get_current_user_id(), 'boldgrid_dismissed_admin_notices', $id );

		wp_die( 'true' );
	}

	/**
	 * Return whether or not an admin notice has been dismissed.
	 *
	 * This method checks for dismissed notices in the initial way we stored the data, in an option
	 * named 'boldgrid_dismissed_admin_notices'. We now store dismissed notice data in user meta.
	 *
	 * @since 1.2.5
	 *
	 * @param string $id An admin notice id.
	 * @return bool
	 */
	public function dismissed_in_deprecated( $id ) {
		$boldgrid_dismissed_admin_notices = get_option( 'boldgrid_dismissed_admin_notices' );

		// If nothing has ever been dismissed, then obviously the user has not dismissed this notice.
		if( false === $boldgrid_dismissed_admin_notices || ! is_array( $boldgrid_dismissed_admin_notices ) ) {
			return false;
		}

		$id = sanitize_key( $id );

		/*
		 * Dismissed notices can be stored in two ways:
		 * # $dismissed[user_id][timestamp] = notice_id;
		 * # $dismissed[user_id][notice_id] = timestamp;
		 *
		 * If either of the above is set, the user has dimissed the notice, so return true. Otherwise,
		 * return false.
		 */
		$format_1_dismissed = in_array( $id, $boldgrid_dismissed_admin_notices, true );
		$format_2_dismissed = array_key_exists( $id, $boldgrid_dismissed_admin_notices );

		if( $format_1_dismissed || $format_2_dismissed ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Return whether or not an admin notice has been dismissed.
	 *
	 * The initial version of this class was setup in a way that only envisioned one user, and they
	 * were an admin. That causes problems. If user 1 dismissed "notice #5", then user 2 and so on
	 * would never see that notice.
	 *
	 * We are changing things so that each user dismisses their own notices. They are no longer
	 * global notices, they are a per user notice.
	 *
	 * @param string $id An admin notice id.
	 * @return bool
	 */
	public function has_been_dismissed( $id ) {
		$id = sanitize_key( $id );

		/*
		 * Backwards compatibility.
		 *
		 * Currently there are only 2 notices:
		 * # BoldGrid image search.
		 * # Feedback x weeks after inspirations.
		 *
		 * If a notice is dismissed in the old version, then it stays dismissed fooooreeeeer.
		 * @link https://www.youtube.com/watch?v=H-Q7b-vHY3Q
		 * Only new notices will be dismissable per user.
		 */
		if( $this->dismissed_in_deprecated( $id ) ) {
			return true;
		}

		$dismissed_notices = get_user_meta( get_current_user_id(), 'boldgrid_dismissed_admin_notices' );

		return in_array( $id, $dismissed_notices );
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
