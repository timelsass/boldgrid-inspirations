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
 */
class Boldgrid_Inspirations_Installed {
	/**
	 *
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
	 *
	 */
	public function get_page_ids( $as_is = true ) {
		$page_ids = get_option( 'boldgrid_installed_page_ids', array() );

		return $as_is ? $page_ids : array_values( $page_ids );
	}

	/**
	 *
	 */
	public function get_all_posts() {
		$args = array(
			'post_type' => 'post',
		);

		$posts = $this->get_all_pages( $args );

		return $posts;
	}

	/**
	 *
	 */
	public function has_installed_posts() {
		$posts = $this->get_all_posts();

		return ! empty( $posts );
	}
}
