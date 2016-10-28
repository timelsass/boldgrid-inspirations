<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Milestones_Social
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Inspirations Milestones Social class.
 *
 * @since 1.3.1
 */
class Boldgrid_Inspirations_Milestones_Social {

	/**
	 * Add hooks.
	 *
	 * @since 1.3.1
	 */
	public function add_hooks() {
		add_action( 'wp_update_nav_menu_item', array( $this, 'nav_menu_item_edited' ), 10, 3 );
		add_action( 'wp_add_nav_menu_item',    array( $this, 'nav_menu_item_edited' ), 10, 3 );
		add_action( 'before_delete_post',      array( $this, 'nav_menu_item_deleted' ) );
		add_action( 'edited_nav_menu',         array( $this, 'nav_menu_edited' ), 10, 2 );
		add_action( 'pre_delete_term',         array( $this, 'nav_menu_deleted' ), 10, 2 );
		add_action( 'updated_option',          array( $this, 'theme_mod_edited' ), 10, 3 );
		add_filter( 'boldgrid_add_feedback',   array( $this, 'filter_feedback' ), 10, 3);
	}

	/**
	 * Get the id of the Social Media menu.
	 *
	 * This is dependent on the BoldGrid Theme Framework running version 1.3.1.
	 *
	 * @since 1.3.1
	 */
	public function get_social_id() {
		$menus_created = get_option( 'boldgrid_menus_created', array() );

		// If no menus were created, abort.
		if( empty( $menus_created ) ) {
			return false;
		}

		/*
		 * This only works with version 2 of the boldgrid_menus_created option. If this is not
		 * version 2, abort.
		 */
		if( ! isset( $menus_created[ 'option_version' ] ) || 2 !== $menus_created[ 'option_version' ] ) {
			return false;
		} else {
			unset( $menus_created[ 'option_version' ] );
		}

		// Search for and return the 'social' menu.
		foreach( $menus_created as $menu_id => $menu_key ) {
			if( 'social' === $menu_key ) {
				return $menu_id;
			}
		}

		/*
		 * If we found the social menu somewhere above, we would have returned it's id. Since we're
		 * here, return false because we havn't found the social menu.
		 */
		return false;
	}

	/**
	 * Log that a social media menu nav item has been deleted.
	 *
	 * @since 1.3.1
	 *
	 * @param int    $term     Term ID.
     * @param string $taxonomy Taxonomy Name.
	 */
	public function nav_menu_deleted( $term, $taxonomy ) {
		// If this is not a menu, abort.
		if( 'nav_menu' !== $taxonomy ) {
			return;
		}

		$social_menu = wp_get_nav_menu_object( $this->get_social_id() );

		if( false === $social_menu ){
			return;
		}

		if( $social_menu->term_id == $term ) {
			Boldgrid_Inspirations_Milestones::log( 'social_media', 'menu_deleted' );
		}
	}

	/**
	 * Log that a social media menu has been edited.
	 *
	 * @since 1.3.1
	 *
	 * @param int $term_id Term ID.
     * @param int $tt_id   Term taxonomy ID.
	 */
	public function nav_menu_edited( $term_id, $tt_id ) {
		global $pagenow;

		$term = get_term( $term_id, 'nav_menu', ARRAY_A );

		// If we don't have a valid $term, abort.
		if( ! is_array( $term ) ) {
			return;
		}

		// If this is not our social media menu, abort.
		if( $term[ 'term_id' ] !== $this->get_social_id() ) {
			return;
		}

		$action = ( 'nav-menus.php' === $pagenow ? 'nav-menus.php' : 'menu_edited' );

		Boldgrid_Inspirations_Milestones::log( 'social_media', $action );
	}

	/**
	 * Log that a social media menu nav item has been deleted.
	 *
	 * @since 1.3.1
	 *
	 * @param int   $menu_id         ID of the updated menu.
     * @param int   $menu_item_db_id ID of the updated menu item.
     * @param array $args            An array of arguments used to update a menu item.
	 */
	public function nav_menu_item_edited( $menu_id, $menu_item_db_id, $args ) {
		global $pagenow;

		// If this menu item does not belong to the social media menu, abort.
		if( $menu_id !== $this->get_social_id() ) {
			return;
		}

		// Determine our $action.
		if( 'nav-menus.php' === $pagenow ) {
			$action = 'nav-menus.php';
		}elseif( 'wp_add_nav_menu_item' === current_filter() ) {
			$action = 'menu_item_added';
		}else {
			$action = 'menu_item_edited';
		}

		Boldgrid_Inspirations_Milestones::log( 'social_media', $action );
	}

	/**
	 * Log that a social media nav menu item has been deleted.
	 *
	 * @since 1.3.1
	 *
	 * @param int $postid.
	 */
	public function nav_menu_item_deleted( $postid ) {
		$post = get_post( $postid );

		// If we couldn't get a valid post, abort.
		if( ! is_object( $post ) ) {
			return;
		}

		// If this is not a nav menu item, abort.
		if( 'nav_menu_item' !== $post->post_type ) {
			return;
		}

		$social_menu_items = wp_get_nav_menu_items( $this->get_social_id() );

		// If we could not get the menu items belonging to Social Media, abort.
		if( false === $social_menu_items ) {
			return;
		}

		/*
		 * Loop through all the menu items in our Social Media menu. If the post we just deleted
		 * belongs to the menu, log.
		 */
		foreach( $social_menu_items as $menu_item ) {
			if( $menu_item->ID === $post->ID ) {
				Boldgrid_Inspirations_Milestones::log( 'social_media', 'menu_item_deleted' );
				return;
			}
		}
	}

	/**
	 * Log that a social media menu's location within a theme has been edited.
	 *
	 * @since 1.3.1
	 *
	 * @param string $option    Name of the updated option.
     * @param mixed  $old_value The old option value.
     * @param mixed  $value     The new option value.
	 */
	public function theme_mod_edited( $option, $old_value, $value ) {
		$updating_theme_mod = substr( $option, 0, 11 ) === 'theme_mods_';
		$updating_staging_theme_mod = substr( $option, 0, 28 ) === 'boldgrid_staging_theme_mods_';

		// If we are not updating theme mods, abort.
		if( ! $updating_theme_mod && ! $updating_staging_theme_mod  ) {
			return;
		}

		$social_menu_id = $this->get_social_id();

		// If we don't have a social media menu, abort.
		if( false === $social_menu_id ){
			return;
		}

		$old_locations = $old_value[ 'nav_menu_locations' ];
		$new_locations = $value[ 'nav_menu_locations' ];

		/*
		 * Loop through each of the old menu locations. If one of the values has changed, and it is
		 * the menu location of the social menu, then we can say that the social media menu location
		 * has been modified.
		 *
		 * This small foreach appears below twice. The first set compares the old to the new, and
		 * the second compares the new to the old.
		 */
		foreach( $old_locations as $location => $menu_id ) {
			if( isset( $new_locations[$location] ) && $menu_id !== $new_locations[$location] && $menu_id === $social_menu_id ) {
				Boldgrid_Inspirations_Milestones::log( 'social_media', 'menu_location_edited' );
				return;
			}
		}

		foreach( $new_locations as $location => $menu_id ) {
			if( isset( $old_locations[$location] ) && $menu_id !== $old_locations[$location] && $menu_id === $social_menu_id ) {
				Boldgrid_Inspirations_Milestones::log( 'social_media', 'menu_location_edited' );
				return;
			}
		}
	}
}
