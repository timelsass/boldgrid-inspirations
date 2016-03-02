<?php

/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_GridBlock_Sets_Preview_Page
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
 * BoldGrid Inspirations GridBlock Sets Preview Page.
 *
 * @since 1.0.10
 */
class Boldgrid_Inspirations_GridBlock_Sets_Preview_Page {
	/**
	 * If we need to create a new "preview page", us this array to create the page.
	 *
	 * @since 1.0.10
	 * @access public
	 * @var array $page_data.
	 */
	public $page_data = array (
		'post_title' => "BoldGrid - GridBlock Set - Preview Page",
		'post_status' => 'draft',
		'post_type' => 'page',
		'comment_status' => 'closed' 
	);
	
	/**
	 * Add hooks.
	 *
	 * @since 1.0.10
	 */
	public function add_hooks() {
		add_filter( 'parse_query', 
			array (
				$this,
				'remove_preview_page_from_all_pages' 
			) );
		
		add_filter( 'wp_count_posts', 
			array (
				$this,
				'remove_preview_page_from_page_count' 
			), 10, 2 );
		
		add_filter( 'boldgrid_staging_pre_force_staged_pages_only', 
			array (
				$this,
				'boldgrid_staging_pre_force_staged_pages_only' 
			) );
	}
	
	/**
	 * Should BoldGrid Staging force staged pages?
	 *
	 * Essentially, if we're looking at our preview page, tell the staging site that it's ok to show
	 * this page. Generally it would show only 'staging' pages, but we'll allow this 'draft'.
	 *
	 * @since 1.0.10
	 *       
	 * @see comments preceding call to apply this filter,
	 *      boldgrid_staging_pre_force_staged_pages_only.
	 *     
	 * @param bool $show_only_staged_content
	 *        	Should we show only staged pages?
	 */
	public function boldgrid_staging_pre_force_staged_pages_only( $show_only_staged_content ) {
		// The GridBlock Set preview page is previewed using the page's id directly, as here:
		// ?page_id=53545
		// If that $_GET var does not exist, abort.
		if ( empty( $_GET['page_id'] ) ) {
			return $show_only_staged_content;
		} else {
			$page_id = $_GET['page_id'];
		}
		
		// Get our preview page.
		$preview_page = $this->get();
		
		// If we're looking at our preview page, return false.
		if ( $page_id == $preview_page->ID ) {
			return false;
		} else {
			return $show_only_staged_content;
		}
	}
	
	/**
	 * Get our preview page.
	 *
	 * @since 1.0.10
	 *       
	 * @return WordPress post object.
	 */
	public function get() {
		// See if the preview page already exists.
		$this->preview_page = get_page_by_title( $this->page_data['post_title'] );
		
		// If it doesn't exist, create it.
		if ( null == $this->preview_page ) {
			$id = wp_insert_post( $this->page_data );
			$this->preview_page = get_post( $id );
		}
		
		// If our preview page has been trashed, set it back to 'draft'.
		// If it is trashed, it cannot be used to preview GridBlock Sets.
		if ( 'trash' == $this->preview_page->post_status ) {
			$this->preview_page->post_status = $this->page_data['post_status'];
			wp_update_post( $this->preview_page );
		}
		
		return $this->preview_page;
	}
	
	/**
	 * Remove our preview page from 'All Pages'.
	 *
	 * @since 1.0.10
	 *       
	 * @param $query The
	 *        	query object that parsed the query. @link
	 *        	https://codex.wordpress.org/Plugin_API/Action_Reference/parse_query.
	 */
	public function remove_preview_page_from_all_pages( $query ) {
		// Get the current page filename:
		global $pagenow;
		
		// Abort if necessary.
		if ( ! ( 'edit.php' == $pagenow && isset( $_GET['post_type'] ) &&
			 'page' == $_GET['post_type'] ) ) {
			return;
		}
		
		$preview_page = get_page_by_title( $this->page_data['post_title'] );
		
		// If the page doesn't exist, abort.
		if ( null == $preview_page ) {
			return;
		}
		
		// Other plugins may set 'post__not_in' as well, and override our setting below.
		// We'll use array_merge and $query->get so to play nice with other plugins.
		$query->set( 'post__not_in', 
			array_merge( array (
				$preview_page->ID 
			), $query->get( 'post__not_in' ) ) );
	}
	
	/**
	 * Remove our preview page from the page count on 'All Pages'.
	 *
	 * @since 1.0.10
	 *       
	 * @param object $counts
	 *        	(http://pastebin.com/WW9ZksMR)
	 * @param string $type        	
	 * @return object $counts
	 */
	public function remove_preview_page_from_page_count( $counts, $type ) {
		// We're only running this on pages.
		if ( 'page' != $type ) {
			return $counts;
		}
		
		$preview_page = get_page_by_title( $this->page_data['post_title'] );
		
		// If the page doesn't exist, abort.
		if ( null == $preview_page ) {
			return $counts;
		}
		
		$post_status = $preview_page->post_status;
		
		if ( isset( $counts->$post_status ) ) {
			$counts->$post_status --;
		}
		
		return $counts;
	}
}