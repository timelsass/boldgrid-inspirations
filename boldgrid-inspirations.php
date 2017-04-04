<?php
/**
 * Plugin Name: BoldGrid Inspirations
 * Plugin URI: http://www.boldgrid.com
 * Version: 1.4.2
 * Author: BoldGrid.com <wpb@boldgrid.com>
 * Author URI: http://www.boldgrid.com
 * Description: Find inspiration, customize, and launch! BoldGrid Inspirations includes FREE WordPress themes and is the easiest way to launch a new WordPress site complete with custom content.
 * Text Domain: boldgrid-inspirations
 * Domain Path: /languages
 * License: GPL
 */

// Define version.
if ( ! defined( 'BOLDGRID_INSPIRATIONS_VERSION' ) ) {
	define( 'BOLDGRID_INSPIRATIONS_VERSION', implode( get_file_data( __FILE__, array( 'Version' ), 'plugin' ) ) );
}

// Used for this and other BoldGrid plugins to locate the core plugin directory.
if ( ! defined( 'BOLDGRID_BASE_DIR' ) ) {
	define( 'BOLDGRID_BASE_DIR', dirname( __FILE__ ) );
}

// Prevent direct calls.
require BOLDGRID_BASE_DIR . '/pages/templates/restrict-direct-access.php';

// If our class is not loaded, then require it.
if ( ! class_exists( 'Boldgrid_Inspirations' ) ) {
	require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations.php';
}

// If PHP is compatible, then load the rest.
if ( Boldgrid_Inspirations::is_php_compatible() ) {
	// Load the inspiration class.
	require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-inspiration.php';

	// Instantiate the inspiration class (also loads the parent class Boldgrid_Inspirations).
	$boldgrid_inspirations_inspiration = new Boldgrid_Inspirations_Inspiration();

	// Add action to call pre_add_hooks after init.
	add_action( 'init',
		array(
			$boldgrid_inspirations_inspiration,
			'pre_add_hooks',
		)
	);

	// Inspirations survey. Needs to load ASAP in order to filter bgtfw configs.
	require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-survey.php';
	$survey = new BoldGrid_Inspirations_Survey();
	$survey->add_hooks();
} else {
	// If PHP is not compatible, deactivate and die if activating from an admin page, or do nothing.
	add_action( 'admin_init', 'Boldgrid_Inspirations::check_php_wp_version' );
}

register_deactivation_hook( __FILE__, array( 'Boldgrid_Inspirations_Attribution_Page', 'on_deactivate' ) );
