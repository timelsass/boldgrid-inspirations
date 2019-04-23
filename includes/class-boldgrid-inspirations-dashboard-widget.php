<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Dashboard_Widget
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Dashboard Widget class.
 *
 * @since 2.1.0
 */
class Boldgrid_Inspirations_Dashboard_Widget {
	/**
	 * Add admin hooks.
	 *
	 * @since 2.1.0
	 */
	public function add_admin_hooks() {
		add_filter( 'Boldgrid\Library\Notifications\DashboardWidget\displayWidget\plugin-boldgrid-inspirations', array( $this, 'filter_item' ) );
	}

	/**
	 * Filter the Inspirations item in the dashboard widget.
	 *
	 * @since 2.1.0
	 */
	public function filter_item( $item ) {
		$item['icon'] = '<img src="https://repo.boldgrid.com/assets/icon-boldgrid-inspirations-128x128.png" />';

		if ( ! Boldgrid_Inspirations_Installed::has_built_site() ) {
			$item['subItems'][] = '
				<p>
					<span class="dashicons dashicons-info"></span> ' .
					wp_kses(
						sprintf(
							// translators: 1 The opening anchor tag to the Inspirations page, 2 its closing tag.
							__( 'It looks like you havn\'t completed the Inspirations process yet. %1$sClick here to finish up and start designing%2$s.', 'boldgrid-inspirations' ),
							'<a href="' . esc_url( admin_url( 'admin.php?page=boldgrid-inspirations' ) ) . '">',
							'</a>'
						),
						array( 'a' => array( 'href' => array() ) )
					) . '
				</p>';
		}

		return $item;
	}
}
