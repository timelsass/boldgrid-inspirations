<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Milestones
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Inspirations Milestones class.
 *
 * @since 1.3.1
 */
class Boldgrid_Inspirations_Milestones {

	/**
	 * An array of options to watch.
	 *
	 * @since 1.3.1
	 * @var array
	 */
	public $options = array();

	/**
	 * Constructor.
	 *
	 * @since 1.3.1
	 */
	public function __construct() {
		$this->set_options();
	}

	/**
	 * Add hooks.
	 *
	 * @since 1.3.1
	 */
	public function add_hooks() {
		add_action( 'update_option_blogname', array( $this, 'option_changed' ), 10, 3 );
	}

	/**
	 * Log that a milestone has been reached.
	 *
	 * @since 1.3.1
	 *
	 * @param string $metaname  A metaname key to identify the type of feedback.
	 * @param mixed  $metavalue A metavalue, which can vary in type.
	 */
	public static function log( $name, $value ) {

		/*
		 * Prevent duplicate nav-menus.php entries.
		 *
		 * When you "Save Menu" from within dashboard/nav-menus.php, everything is submitted via
		 * $_POST and updated, even if it hasn't acutally been updated. If we're editing the social
		 * media menu via nav-menus.php, prevent duplicate milestone entries by ensuring it's only
		 * in the payload once.
		 */
		$allow_duplicates = ( ( 'social_media' === $name && 'nav-menus.php' === $value ) ? false : true );

		Boldgrid_Inspirations_Feedback::add_feedback( 'milestone_' . $name, $value, $allow_duplicates );
	}

	/**
	 * Log when certain options have changed.
	 *
	 * @since 1.3.1
	 *
     * @param mixed  $old_value The old option value.
     * @param mixed  $value     The new option value.
     * @param string $option    Name of the updated option.
	 */
	public function option_changed( $old_value, $value, $option = null ) {
		// If true, log the option's new value. Else, log the value set.
		if( true === $this->options[ $option ] ) {
			$this->log( $option, $value );
		} else {
			$this->log( $option, $this->options[ $option ] );
		}
	}

	/**
	 * Set options.
	 *
	 * If true, log the option's new value. Else, log the value set.
	 *
	 * @since 1.3.1
	 */
	public function set_options() {
		$this->options = array(
			'blogname' => true,
		);
	}
}
