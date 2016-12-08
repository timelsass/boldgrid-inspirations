<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Survey
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Inspirations Survey class.
 *
 * @since 1.3.1
 */
class Boldgrid_Inspirations_Survey {
	/**
	 * Add hooks.
	 *
	 * @since 1.3.4
	 */
	public function add_hooks() {
		add_filter( 'boldgrid_theme_framework_config', array( $this, 'bgtfw_config' ), 15 );
	}

	/**
	 * Filter bgtfw configs.
	 *
	 * This allows Inspirations to change widgets before the bgtfw can create them.
	 *
	 * @since 1.3.4
	 *
	 * @param  array $configs Bgtfw configs.
	 * @return array $configs.
	 */
	public function bgtfw_config( $configs ) {
		// If we don't have any widget instances, abort.
		if( empty( $configs['widget']['widget_instances'] ) ) {
			return $configs;
		}

		$widget_instances = $configs['widget']['widget_instances'];

		foreach( $widget_instances as $widget_area => $widgets ) {
			foreach( $widgets as $widget_key => $widget ) {
				// Update the widget.
				$updated_widget = $this->update_widget( $widget );

				// If the 'delete' key is set, delete the widget. Otherwise, update it.
				if( isset( $updated_widget['delete'] ) ) {
					unset( $configs['widget']['widget_instances'][$widget_area][$widget_key] );
				} else {
					$configs['widget']['widget_instances'][$widget_area][$widget_key] = $updated_widget;
				}
			}
		}

		return $configs;
	}

	/**
	 * Get survey data.
	 *
	 * @since 1.3.4
	 *
	 * @return array
	 */
	public function get() {
		return get_option( 'boldgrid_survey', array() );
	}

	/**
	 * Update a widget based upon our survey data.
	 *
	 * @since 1.3.4
	 *
	 * @param  array $widget An array of widget data.
	 * @return array $widget.
	 */
	public function update_widget( $widget ) {
		$dom = new DOMDocument;
		$dom->loadHTML( $widget['text'] );
		$finder = new DomXPath( $dom );

		$survey = $this->get();

		$phone = ( ! empty( $survey['phone']['value'] ) ? $survey['phone']['value'] : null );
		$display_phone = ! isset( $survey['phone']['do-not-display'] );

		// If we have a phone number and the user wants to display it, update the phone number.
		if( ! empty( $phone ) ) {
			$phone_numbers = $finder->query("//*[contains(@class,'phone-number')]");

			foreach( $phone_numbers as $phone_number ) {
				if( $display_phone ) {
					$phone_number->nodeValue = $phone;
				} else {
					// Get the parent.
					$parent = $phone_number->parentNode;

					// Remove the phone number.
					$parent->removeChild( $phone_number );

					$if_removed = $phone_number->getAttribute( 'data-if-removed' );
					switch( $if_removed ) {
						case 'widget':
							$widget['delete'] = true;
							break;
					}
				}
			}
		}

		/*
		 * Remove doctype, html, and body tags.
		 *
		 * When using $dom->loadHTML, doctype/html/body tags are automatically added.
		 * @see http://fr.php.net/manual/en/domdocument.savehtml.php#85165
		 *
		 * As of PHP 5.4 and Libxml 2.6, there are option parameters to pass to $dom->loadHTML
		 * which will not add the doctype/html/body tags.
		 * @see http://stackoverflow.com/questions/4879946/how-to-savehtml-of-domdocument-without-html-wrapper
		 *
		 * @todo Update this section of code when PHP standards are changed.
		 */
		$widget['text'] = preg_replace(
			'/^<!DOCTYPE.+?>/',
			'',
			str_replace(
				array('<html>', '</html>', '<body>', '</body>'),
				array('', '', '', ''),
				$dom->saveHTML()
			)
		);

		return $widget;
	}
}
?>