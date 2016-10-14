<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Attribution_Page
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Attribution Page class.
 */
class Boldgrid_Inspirations_Attribution_Page {

	public function add_hooks() {
		add_filter( 'post_type_link', array($this,'na_remove_slug'), 10, 3 );

		add_action( 'pre_get_posts', array($this,'na_parse_request') );

		add_action( 'template_redirect', array( $this, 'rebuild' ) );

		add_action( 'template_redirect', array( $this, 'prevent_contamination' ) );
	}

	/**
	 * Get and return the attribution page.
	 */
	public static function get() {
// 		$attribution_page = get_page_by_title( 'Attribution', OBJECT, 'bg_attribution' );
// 		if( is_object( $attribution_page ) ) {
// 			wp_delete_post( $attribution_page->ID );
// 		}

		$defaults = array(
			'post_title' => 'Attribution',
			'post_content' => 'Coming soon.',
			'post_type' => 'bg_attribution',
			'post_name' => 'attribution',
			'post_status' => 'publish',
			'page_template' => 'default',
			'comment_status' => 'closed',
		);

		$defaults = apply_filters( 'boldgrid_deployment_pre_insert_post', $defaults );

		$attribution_page = get_page_by_path( $defaults['post_name'], OBJECT, 'bg_attribution' );

		if( null === $attribution_page ) {


			$id = wp_insert_post( $defaults );

			if( $id === 0 ) {
				return false;
			}

			$attribution_page = get_page( $id );
		}

		if( null === $attribution_page ) {
			return false;
		} else {


			return $attribution_page;
		}
	}

	/**
	 * @see http://wordpress.stackexchange.com/questions/203951/remove-slug-from-custom-post-type-post-urls
	 */
	public function na_parse_request( $query ) {
		if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
			return;
		}

		if ( ! empty( $query->query['name'] ) ) {
			$query->set( 'post_type', array( 'post', 'bg_attribution', 'page' ) );
		}
	}

	/**
	 * @see http://wordpress.stackexchange.com/questions/203951/remove-slug-from-custom-post-type-post-urls
	 */
	public function na_remove_slug( $post_link, $post, $leavename ) {
		if ( 'bg_attribution' != $post->post_type ||
			( 'publish' != $post->post_status && 'staging' != $post->post_status ) ) {
			return $post_link;
		}

		$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

		return $post_link;
	}

	/**
	 *
	 */
	public function prevent_contamination() {
		global $post;

		// If we don't have a post status, return.
		if( empty ( $post->post_status ) ) {
			return;
		}

		$is_contaminated = false;

		$is_contaminated = apply_filters( 'boldgrid_staging_is_contaminated', $post->post_status );

		if( true === $is_contaminated ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
		}
	}

	/**
	 *
	 */
	public function rebuild() {
		if( false !== get_option( 'boldgrid_build_attribution_page' ) ) {

			$attribution_page = Boldgrid_Inspirations_Attribution_Page::get();

			if( get_the_ID() === $attribution_page->ID ) {

				$attribution = new Boldgrid_Inspirations_Attribution();
				$attribution->build_attribution_page();

				delete_option( 'boldgrid_build_attribution_page' );

				header('Location: '.$_SERVER['REQUEST_URI']);
				die();
			}

		}

	}

	/**
	 * Set the html of the attribution page.
	 */
	public static function set_html( $html ) {
		$attribution_page = $this->get();

		$attribution_page->post_content = $html;
	}

}