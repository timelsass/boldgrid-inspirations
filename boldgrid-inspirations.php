<?php
/*
 * Plugin Name: BoldGrid Inspirations
 * Plugin URI: http://www.boldgrid.com
 * Version: 1.2.1
 * Author: BoldGrid.com <wpb@boldgrid.com>
 * Author URI: http://www.boldgrid.com
 * Description: Be inspired, be custom, be bold!
 * Text Domain: boldgrid-inspirations
 * Domain Path: /languages
 * License: GPL
 */

// Prevent direct calls.
if ( false === defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

// Define version.
if ( false === defined( 'BOLDGRID_INSPIRATIONS_VERSION' ) ) {
	define( 'BOLDGRID_INSPIRATIONS_VERSION', '1.2.1' );
}

// Used for other BoldGrid plugins to locate the core plugin directory.
if ( false === defined( 'BOLDGRID_BASE_DIR' ) ) {
	define( 'BOLDGRID_BASE_DIR', dirname( __FILE__ ) );
}

// If our class is not loaded, then include it.
if ( false === class_exists( 'Boldgrid_Inspirations' ) ) {
	require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations.php';
}

// If PHP is compatible, then load the rest.
if ( true === Boldgrid_Inspirations::is_php_compatible() ) {
	// Load the inspiration class.
	require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-inspiration.php';

	// Set our configuration directory.
	$settings = array (
		'configDir' => BOLDGRID_BASE_DIR . '/includes/config'
	);

	// Instantiate the inspiration class.
	$inspiration = new Boldgrid_Inspirations_Inspiration( $settings );

	// Add pre-init hooks.
	$inspiration->add_pre_init_hooks();

	// Add action to call pre_add_hooks after init.
	add_action( 'init', array (
		$inspiration,
		'pre_add_hooks'
	) );
}
