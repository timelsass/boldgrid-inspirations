<?php

/**
 * BoldGrid Source Code
 *
 * @package BoldGrid_Inspirations_Admin_Notices
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * BoldGrid Inspirations Admin Notices
 */


class Boldgrid_Inspirations_Theme_Builder_Random {



	public function create_random_site() {
		return get_site_url() . '?activate-random=1&id=' . md5( rand() );
	}

	public function randomize_ajax() {

		$results = array();
		$count = ! empty( $_POST[ 'count' ] ) ? (int) $_POST[ 'count' ] : 0;

		$randomSites = array();
		for ( $i = 0; $i < $count; $i++ ) {
			$randomSites[] = array(
				'url' => $this->create_random_site()
			);
		}

		$results['sites'] = $randomSites;

		print json_encode( array(
			'success' => true,
			'results' => $results,
		 ) );

		wp_die();
	}



}
