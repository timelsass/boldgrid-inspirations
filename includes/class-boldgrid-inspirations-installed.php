<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Installed
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Inspiration Installed class.
 *
 * This class is responsible for checking on the content that Inspirations has installed.
 *
 * @since 1.7.0
 */
class Boldgrid_Inspirations_Installed {
	/**
	 * Get all pages installed by Inspirations.
	 *
	 * @since 1.7.0
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_posts/
	 *
	 * @return array An array of post objects or post IDs.
	 */
	public function get_all_pages( $args = array() ) {
		$page_ids = $this->get_page_ids( false );

		$defaults = array(
			'include'   => $page_ids,
			'post_type' => 'any',
		);

		$args = wp_parse_args( $args, $defaults );

		$pages = get_posts( $args );

		return $pages;
	}

	/**
	 * Get the page id's installed via Inspirations.
	 *
	 * They are stored as an array in the following format:
	 * # KEY - The page id on the API server.
	 * # VALUE - The local WordPress page id.
	 *
	 * Example: https://pastebin.com/drmnU0VC
	 *
	 * @since 1.7.0
	 *
	 * @param bool $as_is True to return the raw option value, false to return only the local page ids.
	 */
	public function get_page_ids( $as_is = true ) {
		$page_ids = get_option( 'boldgrid_installed_page_ids', array() );

		return $as_is ? $page_ids : array_values( $page_ids );
	}

	/**
	 * Get all posts that Inspirations installed.
	 *
	 * This can be used to check whether or not the Inspirations process installed a blog or any
	 * other posts.
	 *
	 * @return array An array of post objects or post IDs.
	 */
	public function get_all_posts() {
		$args = array(
			'post_type' => 'post',
		);

		return $this->get_all_pages( $args );
	}

	/**
	 * Determine whether or not we have deployed a site with Inspirations.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public static function has_built_site() {
		return 'yes' === get_option( 'boldgrid_has_built_site', 'yes' );
	}

	/**
	 * Check whether or not Inspirations has installed any posts.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function has_installed_posts() {
		$posts = $this->get_all_posts();

		return ! empty( $posts );
	}
}
