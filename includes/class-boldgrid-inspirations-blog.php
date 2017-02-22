<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Blog
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * BoldGrid Blog class.
 *
 * @since 1.4
 */
class Boldgrid_Inspirations_Blog {

	/**
	 * The Blog category id.
	 *
	 * @since 1.4
	 * @var   int
	 */
	public $category_id;

	/**
	 * Create the blog category.
	 *
	 * @since 1.4
	 */
	public function create_category() {
		$category = get_category_by_slug( __( 'Blog', 'boldgrid-inspirations' ) );

		if( $category ) {
			$this->category_id = $category->term_id;
		} else {
			$this->category_id = wp_create_category( __( 'Blog', 'boldgrid-inspirations' ) );
		}
	}

	/**
	 * Create the blog menu item.
	 *
	 * @since 1.4
	 *
	 * @param int $menu_id
	 * @param int $menu_order
	 */
	public function create_menu_item( $menu_id, $menu_order ) {
		$data = array(
			'menu-item-title' => __( 'Blog', 'boldgrid-inspirations' ),
			'menu-item-object-id' => $this->category_id,
			'menu-item-db-id' => 0,
			'menu-item-object' => 'category',
			'menu-item-parent-id' => 0,
			'menu-item-type' => 'taxonomy',
			'menu-item-url' => get_category_link( $this->category_id ),
			'menu-item-status' => 'publish',
			'menu-item-position' => $menu_order,
		);

		return wp_update_nav_menu_item( $menu_id, 0, $data );
	}
}
