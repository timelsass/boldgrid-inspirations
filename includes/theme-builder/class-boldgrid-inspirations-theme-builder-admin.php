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
 * BoldGrid Inspirations Admin Notices
 */
class Boldgrid_Inspirations_Theme_Builder_Admin {

	public function menu_item_cb() {
		add_menu_page(
			'Theme Builder',
			'Theme Builder',
			'manage_options',
			'boldgrid-theme-builder',
			array( $this, 'render_admin_page' ),
			'dashicons-welcome-widgets-menus'
		);
	}

	public function render_admin_page() {
		print include 'view/admin.php';
	}

	public function print_templates() {
		//print include 'template/theme.php';
	}

	public function enqueue_scripts() {
		wp_enqueue_script(
			'boldgrid-theme-builder',
			plugins_url( 'includes/theme-builder/assets/js/admin.js' , __FILE__ ),
			array( 'jquery' ),
			'1.0',
			true
		);
	}

}