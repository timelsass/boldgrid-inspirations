<?php
/**
 * BoldGrid Source Code
 *
 * @package   Boldgrid_Inspirations_Deploy_Theme
 * @copyright BoldGrid.com
 * @version   $Id$
 * @author    BoldGrid <support@boldgrid.com>
 */

/**
 * BoldGrid Inspirations Deploy Theme class.
 *
 * @since 1.5.1
 */
class Boldgrid_Inspirations_Deploy_Theme {

	/**
	 * Option name signifying we've installed and switch_theme'd during an
	 * Inspirations install.
	 *
	 * @since 1.5.1
	 */
	public static $theme_deployed = 'boldgrid_inspirations_theme_deployed';

	/**
	 * Add hooks.
	 *
	 * @since 1.5.1
	 */
	public function add_hooks() {
		add_action( 'after_switch_theme', array( $this, 'wp_menus_changed' ), 9 );
	}

	/**
	 * Remove WordPress' _wp_menus_changed action after deployment.
	 *
	 * As of WordPress 4.9, WordPress tries to match up your old menu locations
	 * to your new menu locations after a theme switch. We do not need this to
	 * happen because we are handling the menu setup during Inspirations.
	 *
	 * If we didn't do this, menus assignments set during an Inspirations
	 * install would be overwritten by _wp_menus_changed.
	 *
	 * @since 1.5.1
	 *
	 * @link https://core.trac.wordpress.org/ticket/39692
	 * @link https://core.trac.wordpress.org/attachment/ticket/39692/39692.diff
	 */
	public function wp_menus_changed() {
		$theme_deployed = get_option( self::$theme_deployed );

		if( ! empty( $theme_deployed ) ) {
			remove_action( 'after_switch_theme', '_wp_menus_changed', 10 );
		}

		delete_option( self::$theme_deployed );
	}
}
