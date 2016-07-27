<?php

/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Customizer
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

// Prevent direct calls
if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

// BoldGrid Dashboard Class
class Boldgrid_Inspirations_Customizer extends Boldgrid_Inspirations {
	public function __construct( $pluginPath ) {
		$this->pluginPath = $pluginPath;
		parent::__construct( $pluginPath );
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		if ( is_customize_preview() ) {
			// if in admin add CSS and JS to dashboard for widget and styling
			add_action( 'customize_controls_print_styles', array( $this, 'remove_change_themes' ), 999 );
		}
	}

	/**
	 * This function adds some styles to the WordPress Customizer
	 */
	public function remove_change_themes() { ?>
		<style>
			.button.change-theme {
				display: none;
			}
		</style>
		<?php
	}
}
