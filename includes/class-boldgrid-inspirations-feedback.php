<?php

/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Feedback
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * BoldGrid Feedback class.
 *
 * This class should only load if the user did not opt-out of feedback.
 *
 * @since 1.0.9
 */
class Boldgrid_Inspirations_Feedback {
	/**
	 * Constructor.
	 *
	 * Add hooks.
	 *
	 * @since 1.0.9
	 */
	public function __construct() {
		// Add an action to run when using the Customizer.
		add_action( 'customize_register', array (
			$this,
			'customizer_start'
		) );

		// Add an action to run when a theme is activated.
		add_action( 'after_switch_theme', array (
			$this,
			'theme_activation'
		) );

		// Add an action to record when a page is created using a GridBlock.
		add_action( 'boldgrid_inspirations_post_gridblock_set_create_page_callback',
			array (
				$this,
				'gridblock_add_page'
			) );

		// Add an action to check the payload.
		add_action( 'admin_init', array (
			$this,
			'check_payload'
		) );
	}

	/**
	 * Customizer start.
	 *
	 * Record the first use of the customizer.
	 *
	 * @since 1.0.9
	 */
	public function customizer_start() {
		// Get the current timestamp.
		$timestamp = date( 'Y-m-d H:i:s' );

		// On the very first use.
		if ( false === get_option( 'boldgrid_customizer_first_use' ) ) {
			// Save timestamp of event.
			update_option( 'boldgrid_customizer_first_use', $timestamp );

			// Insert new data.
			self::add_feedback( 'customizer_start' );
		}
	}

	/**
	 * Theme activation.
	 *
	 * Record the theme name on activation.
	 *
	 * @since 1.0.11
	 */
	public function theme_activation() {
		// Get the current active theme.
		$theme_name = wp_get_theme()->get( 'Name' );

		// Insert new data.
		self::add_feedback( 'theme_activation', $theme_name );
	}

	/**
	 * GridBlock page insertion.
	 *
	 * Record when a page is created from a GridBlock.
	 *
	 * @since 1.0.12
	 *
	 * @param array $args
	 *        	{
	 *        	An argument array passed from do_action().
	 *
	 *        	@type int $page_id The client WordPress page id.
	 *        	@type int $boldgrid_page_id BoldGrid page id.
	 *        	}
	 * @return null
	 */
	public function gridblock_add_page( $args = null ) {
		// Get the BoldGrid page id.
		$boldgrid_page_id = isset( $args['boldgrid_page_id'] ) ? $args['boldgrid_page_id'] : null;

		// Insert new data.
		self::add_feedback( 'gridblock_add_page', $boldgrid_page_id );

		return;
	}

	/**
	 * Check payload.
	 *
	 * Check feedback payload to see if data needs to be delivered.
	 *
	 * @since 1.0.9
	 *
	 * @return null
	 */
	public function check_payload() {
		// Initialize $success.
		$success = false;

		// Get the current feedback data.
		$feedback_data = get_option( 'boldgrid_feedback' );

		// If there is data, then send it.
		if ( false === empty( $feedback_data ) ) {
			// Deliver the data:
			$success = $this->deliver_payload( $feedback_data );
		}

		// If successful, then clear boldgrid_feedback.
		if ( true === $success ) {
			delete_option( 'boldgrid_feedback' );
		}

		return;
	}

	/**
	 * Deliver payload.
	 *
	 * Deliver the feedback payload to the asset server.
	 *
	 * @since 1.0.9
	 *
	 * @param $data Feedback
	 *        	data array (from the WP Option "boldgrid_feedback").
	 *
	 * @return bool
	 */
	private function deliver_payload( $data = null ) {
		// Check data.
		if ( empty( $data ) || false === is_array( $data ) ) {
			return false;
		}

		// json_encode the data.
		$feedback_data['boldgrid_feedback'] = json_encode( $data );

		// Send the data.
		$response = Boldgrid_Inspirations::boldgrid_api_call( '/api/feedback/process', false,
			$feedback_data, 'POST' );

		// Check response.
		if ( ! empty( $response ) && 'Data accepted' == $response->message ) {
			// Success.
			return true;
		} else {
			// Failure.
			return false;
		}
	}

	/**
	 * Add feedback.
	 *
	 * Add feedback data.
	 *
	 * @since 1.0.9
	 *
	 * @return bool
	 */
	public static function add_feedback( $metaname = null, $metavalue = null ) {
		// Validate input.
		if ( empty( $metaname ) ) {
			return false;
		}

		// Get the current timestamp.
		$timestamp = date( 'Y-m-d H:i:s' );

		// Get the current feedback data.
		$feedback_data = get_option( 'boldgrid_feedback' );

		// Insert new data.
		$feedback_data[] = array (
			'type' => $metaname,
			'timestamp' => $timestamp,
			'value' => $metavalue
		);

		// Save data.
		update_option( 'boldgrid_feedback', $feedback_data );

		return true;
	}
}
