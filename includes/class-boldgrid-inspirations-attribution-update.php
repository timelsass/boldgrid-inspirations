<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Attribution_Update
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Attribution Page class.
 */
class Boldgrid_Inspirations_Attribution_Update {

	public function add_hooks() {
		$this->upgrade_to_cpt();

		add_filter( 'pre_option_boldgrid_staging_boldgrid_attribution', array( $this, 'pre_option_boldgrid_attribution' ), 20 );
		add_filter( 'pre_option_boldgrid_attribution', array( $this, 'pre_option_boldgrid_attribution' ), 20 );
	}

	/**
	 * This is for backwards compatibility.
	 */
	public function pre_option_boldgrid_attribution() {
		$return = array();

		$attribution_page = Boldgrid_Inspirations_Attribution_Page::get();

		$return['page']['id'] = $attribution_page->ID;

		return $return;
	}


	/**
	 *
	 */
	public function upgrade_to_cpt() {
 		if( false !== get_option( 'boldgrid_attribution_upgraded_to_cpt' ) ) {
 			return;
 		}

		$slugs = array( 'attribution', 'attribution-staging' );

		foreach( $slugs as $slug ) {
			$attribution_page = get_page_by_path( $slug  );

			if( is_object( $attribution_page ) && isset( $attribution_page->ID ) ) {

				wp_delete_post( $attribution_page->ID, true );
			}
		}

		update_option( 'boldgrid_attribution_upgraded_to_cpt', true );
	}
}