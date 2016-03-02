<?php

/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Utility
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

// Prevent direct calls
if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * BoldGrid Inspiration Utility class.
 *
 * @since 1.0.10
 */
class Boldgrid_Inspirations_Utility {
	
	/**
	 * Does $haystack end with $needle?
	 *
	 * @return boolean
	 */
	public static function endsWith( $haystack, $needle ) {
		$length = strlen( $needle );
		
		if ( $length == 0 ) {
			return true;
		}
		
		return ( substr( $haystack, - $length ) === $needle );
	}
	
	/**
	 * This function allows you to easily include an inline js file.
	 *
	 * All js files must be located within the assets/js/inline folder.
	 */
	public static function inline_js_file( $filename ) {
		$full_path_to_js = plugins_url( '/assets/js/inline/' . $filename, 
			BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' );
		
		echo '<script type="text/javascript" src="' . $full_path_to_js;
		
		if ( defined( 'BOLDGRID_INSPIRATIONS_VERSION' ) ) {
			echo '?ver=' . BOLDGRID_INSPIRATIONS_VERSION;
		}
		
		echo '"></script>';
	}
	
	/**
	 * Similar to inline_js_file(), except this allows you to run oneliners when a file cannot be used.
	 */
	public static function inline_js_oneliner( $oneliner ) {
		echo '
		<script type="text/javascript">
			' . $oneliner . '
		</script>
	';
	}
	
	/**
	 * Does $haystack start with $needle?
	 *
	 * @return boolean
	 */
	public static function startsWith( $haystack, $needle ) {
		$length = strlen( $needle );
		
		return ( substr( $haystack, 0, $length ) === $needle );
	}
}
