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
		add_filter( 'bgtfw_widget_data', array( $this, 'bgtfw_widget_data' ) );
	}

	/**
	 * Filter widget data.
	 *
	 * This allows Inspirations to change widgets before the bgtfw can create them.
	 *
	 * @since 1.3.4
	 *
	 * @param  array $widget Widget data.
	 * @return array $widget
	 */
	public function bgtfw_widget_data( $widget ) {
		$dom = new DOMDocument;
		$dom->loadHTML( $widget['text'] );
		$finder = new DomXPath( $dom );

		// Update phone numbers.
		$phone_numbers = $finder->query("//*[contains(@class,'phone-number')]");
		foreach( $phone_numbers as $phone_number ) {
			// This phone number is hard coded, this will be fixed in a later commit.
			$phone_number->nodeValue = '888-321-4678';
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