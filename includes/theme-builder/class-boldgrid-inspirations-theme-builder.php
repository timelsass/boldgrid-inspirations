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

include_once 'class-boldgrid-inspirations-theme-builder-admin.php';


class Boldgrid_Inspirations_Theme_Builder {


	public function init() {
		$this->bind_hooks();
	}

	public function bind_hooks() {
		$admin = new Boldgrid_Inspirations_Theme_Builder_Admin();

		add_action( 'admin_menu', array( $admin, 'menu_item_cb' ) );
		add_action( 'admin_footer', array( $admin, 'print_templates' ) );
		add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );
	}


}
