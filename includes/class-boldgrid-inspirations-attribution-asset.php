<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Attribution_Asset
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Attribution Asset class.
 */
class Boldgrid_Inspirations_Attribution_Asset {

	/**
	 *
	 */
	public function get_names( $asset, $asset_type ) {


		// First, build a list of possible filenames for the asset.

		// We will create an array of names this asset could have.
		// For example, the same asset might have been resized into several different files /
		// thumbnails, and we need to check for all of them.
		$array_file_names_to_query = array();

		// Get _wp_attachment_metadata.
		$wp_attachment_metadata = get_post_meta( $asset['attachment_id'], '_wp_attachment_metadata', true );

		if ( ! empty( $wp_attachment_metadata ) ) {
			if ( ! empty( $wp_attachment_metadata['sizes'] ) ) {
				foreach ( $wp_attachment_metadata['sizes'] as $image_size ) {
					$array_file_names_to_query[] = $image_size['file'];
				}
			}
		}

		// Get _wp_attached_file.
		$wp_attached_file = get_post_meta( $asset['attachment_id'], '_wp_attached_file', true );

		if ( ! empty( $wp_attached_file ) ) {
			$array_file_names_to_query[] = $wp_attached_file;
		}

		return $array_file_names_to_query;
	}

	public function is_in_gallery( $asset, $post_status_to_search) {
		global $wpdb;
		/**
		 * ********************************************************************
		 * Is this an image used within a gallery shortcode?
		 *
		 * Example gallery call:
		 * [gallery targetsize="full" captions="hide" bottomspace="none" gutterwidth="0" link="file"
		 * columns="4" size="full" ids="29215,29216,29217,29218,29219,29220,29221,29222"
		 * data-imhwpb-assets='51737,51738,51739,51740,51741,51742,51743,51744' ]
		 * ********************************************************************
		 */
		// @todo Use a regular expression to find a match, rather than this excessive LIKE
		// statement.
		$gallery_like_statement = '%[gallery%ids%' . $wpdb->esc_like( $asset['attachment_id'] ) .
		'%data-imhwpb-assets%' . $wpdb->esc_like( $asset['asset_id'] ) . '%]%';

		$asset_in_gallery = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `post_title`
				FROM $wpdb->posts
				WHERE `post_content` LIKE %s AND
				`post_type` IN ('page','post') AND
				`post_status` IN ($post_status_to_search)
				", $gallery_like_statement  ) );

		// If we found results, then the image is being used in a page/post.
		return ( ! empty( $asset_in_gallery ) );
	}

	/**
	 *
	 */
	public function is_in_page( $file_name, $post_status ) {
		global $wpdb;

		// SELECT post_title where post_content like
		// '%2015/02/google-maps-int-1410976385-pi.jpg%'.
		$asset_in_page = $wpdb->get_var(
			$wpdb->prepare( "
				SELECT	`post_title`
				FROM	$wpdb->posts
				WHERE	`post_content`	LIKE %s AND
						`post_type`		IN ('page','post') AND
						`post_status`	IN ($post_status)
				", '%' . $wpdb->esc_like( $file_name ) . '%'
		));

		// If we found results, then the image is being used in a page/post.
		return ( ! empty( $asset_in_page ) );
	}

	/**
	 *
	 */
	public function is_theme_mod( $array_file_names_to_query ) {
		$theme_mods = get_theme_mods();
		// If we have theme mods.
		if ( false != $theme_mods ) {
			// Loop through each mod.
			foreach ( $theme_mods as $mod_key => $mod_value ) {
				// If there is a value for the mod.
				// If the value is a string.
				// If the value is a url (begins with http).
				if ( isset( $mod_value ) &&  is_string( $mod_value ) &&
					'http' === substr( $mod_value, 0, 4 ) ) {
					// Loop through each possible filename.
				foreach ( $array_file_names_to_query as $file_name_to_query ) {
					// If the mod_value ends in the filename, return true.
					$length_of_filename = strlen( $file_name_to_query );

					if ( $file_name_to_query === substr( $mod_value, - 1 * $length_of_filename ) ) {
						return true;
					}
				}
				}
			}
		}

		return false;
	}

	/**
	 *
	 */
	public function is_featured_image( $asset, $post_status ) {
		global $wpdb;

		if ( ! ( empty( $asset['attachment_id'] ) || empty( $this->attribution_page->ID ) ) ) {
			$is_featured_image = $wpdb->get_var(
				$wpdb->prepare("
					SELECT `post_id`
					FROM	$wpdb->postmeta,
							$wpdb->posts
					WHERE	$wpdb->postmeta.meta_key = '_thumbnail_id' AND
							$wpdb->postmeta.meta_value = %s AND
							$wpdb->postmeta.post_id = $wpdb->posts.ID AND
							$wpdb->posts.post_status IN ( $post_status ) AND
							$wpdb->posts.post_type IN ('page','post')
					", $asset['attachment_id']
				));
		}

		// If we found results, then the image is being used in a page/post.
		return ( ! empty( $is_featured_image ) );
	}

	/**
	 * Determine if a passed in asset needs attribution.
	 *
	 * We'll do this by checking to see if the asset is used within a page/post, or,
	 * it is set as a featured image.
	 */
	public function needs_attribution( $asset, $asset_type ) {


		// If there's no attribution_license, we can't attribute the asset; return false.
		if ( empty( $asset['attribution'] ) ) {
			return false;
		}

		/*
		 * By default, when looking through pages and posts for images, look for those with a status
		 * of 'publish'. We don't want to attribute images that are not published. We want to allow
		 * other plugins to change this too however, such as the BoldGrid staging plugin.
		 */
		$post_status_to_search = "'publish'";

		$post_status_to_search = apply_filters( 'boldgrid_attribution_post_status_to_search', $post_status_to_search );

		if( true === $this->is_featured_image( $asset, $post_status_to_search ) ) {
			return true;
		}

		$array_file_names_to_query = $this->get_names( $asset, $asset_type );


		// Is this asset used in a page?
		if ( 'image' == $asset_type && ! ( empty( $array_file_names_to_query ) ) ) {
			foreach ( $array_file_names_to_query as $file_name_to_query ) {
				if( true === $this->is_in_page( $file_name_to_query, $post_status_to_search ) ) {
					return true;
				}
			}
		}

		// Is this asset a theme mod?
		if( true === $this->is_theme_mod( $array_file_names_to_query ) ) {
			return true;
		}

		// Is this asset used within a gallery?
		if( true === $this->is_in_gallery( $asset, $post_status_to_search ) ) {
			return true;
		}

		/*
		 * If we weren't able to find the asset being used in a page/post or as a featured image,
		 * then return false for asset_needs_attribution.
		 */
		return false;
	}
}
