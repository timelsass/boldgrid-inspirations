<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Design_First
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * BoldGrid Inspirations Design First
 *
 * Install a site using Inspirations, choosing from a design first.
 *
 * @since xxx
 */
class Boldgrid_Inspirations_Design_First {

	/**
	 * Add hooks.
	 *
	 * @since xxx
	 */
	public function add_hooks() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Add menu.
	 *
	 * @since xxx
	 */
	public function add_menu() {
		add_submenu_page(
			'boldgrid-inspirations',
			'Design First',
			'Design First',
			'manage_options',
			'admin.php?page=boldgrid-inspirations-design-first',
			array( $this, 'admin_page' )
		);
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since xxx
	 * @param string $hook Hook for page.
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( 'inspirations_page_admin?page=boldgrid-inspirations-design-first' !== $hook ) {
			return;
		}

		// Add active color palette css.
		add_action('admin_head',
			array(
				$this,
				'admin_colors',
			)
		);

		// Css.
		wp_register_style(
			'boldgrid-inspirations-design-first',
			plugins_url( '/assets/css/boldgrid-inspirations-design-first.css', BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ),
			array(),
			BOLDGRID_INSPIRATIONS_VERSION
		);
		wp_enqueue_style( 'boldgrid-inspirations-design-first' );

		wp_enqueue_style( 'dashicons' );

		// Js.
		wp_enqueue_script( 'boldgrid-inspirations-design-first',
			plugins_url( 'assets/js/boldgrid-inspirations-design-first.js', BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ),
			array(),
			BOLDGRID_INSPIRATIONS_VERSION,
			true
		);

		// Js.
		wp_enqueue_script( 'boldgrid-lazyload',
			plugins_url( 'assets/js/lazyload.js', BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ),
			array( 'jquery' ),
			BOLDGRID_INSPIRATIONS_VERSION,
			true
		);
	}

	/**
	 * Adds inline CSS to admin_head using the active color palette.
	 *
	 * @since xxx
	 */
	public function admin_colors() {
		global $_wp_admin_css_colors;
		$this->admin_colors = $_wp_admin_css_colors;
		$user = get_user_option( 'admin_color' );
		echo '<style>
			.pageset-option.active,.coin-option.active,.sub-category.active,.pageset-option.blue,.coin-option.blue,.blue { background-color:' . $this->admin_colors[ $user ]->colors[3] . ' !important; }
			.devices button:focus { border-bottom-color: ' . $this->admin_colors[ $user ]->colors[3] . '; }
			</style>';
	}

	/**
	 * Admin page.
	 *
	 * @since xxx
	 */
	public function admin_page() {
		// Underscores Templates.
		include BOLDGRID_BASE_DIR . '/pages/templates/boldgrid-inspirations-design-first.php';

		// Page template.
		include BOLDGRID_BASE_DIR . '/pages/boldgrid-inspirations-design-first.php';
	}
}
