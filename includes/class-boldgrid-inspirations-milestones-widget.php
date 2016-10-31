<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Milestones_Widget
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Inspirations Milestones Widget class.
 *
 * @since 1.3.1
 */
class Boldgrid_Inspirations_Milestones_Widget {
	/**
	 * Add hooks.
	 *
	 * @since 1.3.1
	 */
	public function add_hooks() {
		add_action( 'update_option_widget_black-studio-tinymce', array( $this, 'updated_option' ), 10, 3 );
		add_action( 'update_option_sidebars_widgets',            array( $this, 'update_sidebars_widgets' ), 10, 3 );
	}

	/**
	 * Get the id's of our widgets created.
	 *
	 * @since 1.3.1
	 *
	 * @return array An array of INTs.
	 */
	public function get_ids() {
		$return = array();

		$widgets = get_option( 'boldgrid_widgets_created', array() );

		// These are the widgets that we want to know when are edited / removed.
		$to_track = array( 'contact_info', 'call_to_action' );

		foreach( $to_track as $key ) {
			if( isset( $widgets[$key] ) ) {
				$return[$key] = str_replace( 'black-studio-tinymce-', '', $widgets[$key] );
			}
		}

		return $return;
	}

	/**
	 * Determine if one of our widgets is being added or removed from a theme.
	 *
	 * @since 1.3.1
	 *
	 * @param mixed  $old_value The old option value.
     * @param mixed  $value     The new option value.
     * @param string $option    Option name.
	 */
	public function update_sidebars_widgets( $old_value, $value, $option ) {
		$widgets = get_option( 'boldgrid_widgets_created', array() );

		// If we don't have any created widgets, abort.
		if( empty( $widgets ) ) {
			return;
		}

		/*
		 * Remove wp_inactive_widgets from both arrays, to avoid false positives.
		 *
		 * If We remove a widget, it's "removed" from its original location and "added" to the
		 * inactve widgets.
		 */
		unset( $value['wp_inactive_widgets'] );
		unset( $old_value['wp_inactive_widgets'] );

		/*
		 * $widget_key  = contact_info
		 * $widget_id   = black-studio-tinymce-6
		 * $widget_aray = boldgrid-widget-2
		 */
		foreach( $widgets as $widget_key => $widget_id ) {
			foreach( $old_value as $widget_area => $widget_area_widgets ) {

				$in_old = ( is_array( $widget_area_widgets ) && in_array( $widget_id, $widget_area_widgets ) );
				$in_new = ( isset( $value[$widget_area] ) && is_array( $value[$widget_area] ) && in_array( $widget_id, $value[$widget_area] ) );

				if( $in_old && ! $in_new ) {
					Boldgrid_Inspirations_Milestones::log( $widget_key, 'removed' );
				} elseif( ! $in_old && $in_new ) {
					Boldgrid_Inspirations_Milestones::log( $widget_key, 'added' );
				}
			}
		}
	}

	/**
	 * Determine if any of our widgets have been updated.
	 *
	 * @since 1.3.1
	 *
	 * @param mixed  $old_value The old option value.
     * @param mixed  $value     The new option value.
     * @param string $option    Option name.
	 */
	public function updated_option( $old_value, $value, $option ) {

		$ids_to_check = $this->get_ids();

		foreach( $ids_to_check as $key => $id ) {
			// Configure $old and $exists_in_old.
			if( isset( $old_value[$id] ) ) {
				$exists_in_old = true;
				$old = json_encode( $old_value[$id] );
			} else {
				$exists_in_old = false;
				$old = null;
			}

			// Configure $new and $exists_in_new.
			if( isset( $value[$id] ) ) {
				$exists_in_new = true;
				$new = json_encode( $value[$id] );
			} else {
				$exists_in_new = false;
				$new = null;
			}

			if( $exists_in_old && ! $exists_in_new ) {
				Boldgrid_Inspirations_Milestones::log( $key, 'removed' );
			}elseif( ! $exists_in_old && $exists_in_new ) {
				Boldgrid_Inspirations_Milestones::log( $key, 'added' );
			}elseif( $new !== $old ) {
				Boldgrid_Inspirations_Milestones::log( $key, 'edited' );
			}
		}
	}
}
