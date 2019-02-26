<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Redirect
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Inspiration Redirect class.
 *
 * This class is responsible for redirecting the user to the Inspirations process.
 */
class Boldgrid_Inspirations_Redirect {

	/**
	 * The minimum Inspirations version required in order to auto redirect the user to the Inspirations
	 * process.
	 *
	 * @since 1.7.0
	 * @var string
	 * @access private
	 */
	private $minimum_version = '1.7.0';

	/**
	 *
	 */
	private $option_name = 'boldgrid_inspirations_redirect';

	/**
	 * Add Admin hooks.
	 *
	 * This method is called via the Boldgrid_Inspirations_Inspiration::add_hooks method, specifically
	 * within the is_admin conditional.
	 *
	 * @since x.x.x
	 */
	public function add_admin_hooks() {
		add_action( 'current_screen', array( $this, 'redirect' ) );
	}

	/**
	 *
	 */
	private function get_option() {
		return get_option( $this->option_name );
	}

	/**
	 *
	 */
	private function maybe_redirect() {
		// DEBUG, REMOVE THE BELOW LINE.
		$this->minimum_version = '1.6.5';

		// If we're already on the Inspirations page, don't redirect.
		if ( Boldgrid_Inspirations_Built::is_inspirations() ) {
			/*
			 * Flag that we've already done the redirect so that after the deployment it will
			 * properly redirect to My Inspiration rather than back to Inspirations.
			 */
			$this->set_option();

			return false;
		}

		// If we're not on the minimum required version, don't redirect.
		if ( ! version_compare( BOLDGRID_INSPIRATIONS_VERSION, $this->minimum_version, '>=' ) ) {
			return false;
		}

		// If the user doesn't have permission, don't redirect.
		if ( ! current_user_can( 'update_plugins' ) ) {
			return false;
		}

		// If we've already redirected the user before, don't do it again.
		$option_value = get_option( $this->option_name );
		if ( ! empty( $option_value ) ) {
			return false;
		}

		return true;
	}

	/**
	 *
	 */
	public function redirect() {
		if ( $this->maybe_redirect() ) {
			$this->set_option();

			wp_redirect( admin_url( 'admin.php?page=boldgrid-inspirations' ) );
			exit;
		}
	}

	/**
	 *
	 */
	public function set_option() {
		// Flag that we are redirecting the user.
		update_option( $this->option_name, time() );
	}
}
