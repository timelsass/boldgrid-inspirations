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
include_once 'class-boldgrid-inspirations-theme-builder-random.php';

// Site Features.
include_once 'class-boldgrid-inspirations-theme-builder-background.php';
include_once 'class-boldgrid-inspirations-theme-builder-button.php';
include_once 'class-boldgrid-inspirations-theme-builder-color.php';
include_once 'class-boldgrid-inspirations-theme-builder-template.php';


class Boldgrid_Inspirations_Theme_Builder {


	public function init() {
		$this->bind_hooks();
	}

	public function bind_hooks() {
		$admin = new Boldgrid_Inspirations_Theme_Builder_Admin();
		$random = new Boldgrid_Inspirations_Theme_Builder_Random();

		$color = new Boldgrid_Inspirations_Theme_Builder_Color();
		$button = new Boldgrid_Inspirations_Theme_Builder_Button();
		$background = new Boldgrid_Inspirations_Theme_Builder_Background();
		$template = new Boldgrid_Inspirations_Theme_Builder_Template();

		// Setup Admin Display.
		add_action( 'admin_menu', array( $admin, 'menu_item_cb' ) );
		add_action( 'admin_footer', array( $admin, 'print_templates' ) );
		add_action( 'admin_enqueue_scripts', array( $admin, 'enqueue_scripts' ) );

		// Randomizer.
		add_action( 'wp_ajax_boldgrid_random_theme', array( $random, 'randomize_ajax' ) );

	}


}
