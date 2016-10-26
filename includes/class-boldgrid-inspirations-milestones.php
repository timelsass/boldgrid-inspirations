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
	public function log( $name, $value ) {
		Boldgrid_Inspirations_Feedback::add_feedback( $name, $value );
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
	public function option_changed( $old_value, $value, $option ) {
		$name = 'milestone_' . $option;

		// If true, log the option's new value. Else, log the value set.
		if( true === $this->options[ $option ] ) {
			$this->log( $name, $value );
		} else {
			$this->log( $name, $this->options[ $option ] );
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
