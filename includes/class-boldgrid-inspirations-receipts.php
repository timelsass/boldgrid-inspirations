<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Receipts
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * BoldGrid Receipts class
 */
class Boldgrid_Inspirations_Receipts extends Boldgrid_Inspirations {

	/**
	 * Contructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		if ( is_admin() ) {
			// Get BoldGrid settings from WP Option:
			$boldgrid_settings = get_option( 'boldgrid_settings' );

			if ( 1 == $boldgrid_settings['boldgrid_menu_option'] ) {
				// Load Javascript and CSS:
				add_action( 'admin_menu', array (
					$this,
					'menu_transactions'
				), 1001 );

				add_action( 'admin_enqueue_scripts',
					array (
						$this,
						'admin_enqueue_transaction_menu'
					) );
			} else {
				add_action( 'admin_menu', array (
					$this,
					'submenu_receipts'
				), 1201 );

				add_action( 'admin_enqueue_scripts',
					array (
						$this,
						'admin_enqueue_transaction_submenu'
					) );
			}
		}
	}

	/**
	 * Add transaction history script for toplevel page
	 *
	 * @param string $hook
	 */
	public function admin_enqueue_transaction_menu( $hook ) {
		if ( 'toplevel_page_boldgrid-transactions' !== $hook )
			return;

		wp_enqueue_script( 'transaction-history',
			plugins_url( '/assets/js/transaction_history.js',
				BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ), array (
				'jquery'
			), BOLDGRID_INSPIRATIONS_VERSION, true );
	}

	/**
	 * Add transaction history script for BoldGrid pages
	 *
	 * @param string $hook
	 */
	public function admin_enqueue_transaction_submenu( $hook ) {
		if ( 'boldgrid_page_boldgrid-transactions' !== $hook )
			return;

		wp_enqueue_script( 'transaction-history',
			plugins_url( '/assets/js/transaction_history.js',
				BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ), array (
				'jquery'
			), BOLDGRID_INSPIRATIONS_VERSION, true );
	}

	/**
	 * Add transactions menu item or submenu item based on user's preference in settings
	 */
	public function menu_transactions() {
		add_menu_page( 'Transactions', 'Transactions', 'manage_options', 'boldgrid-transactions',
			array (
				$this,
				'page_receipts'
			), 'none' );

		// submenu item receipts
		add_submenu_page( 'boldgrid-transactions', 'Receipts', 'Receipts', 'administrator',
			'boldgrid-transactions' );
	}

	/**
	 * Add submenu page for receipts
	 */
	public function submenu_receipts() {
		// submenu receipts
		add_submenu_page( 'boldgrid-inspirations', 'Receipts', 'Receipts', 'administrator',
			'boldgrid-transactions', array (
				$this,
				'page_receipts'
			) );
	}

	/**
	 * Menu callback for submenu page for receipts
	 */
	public function page_receipts() {
		include BOLDGRID_BASE_DIR . '/pages/transaction_history.php';
	}
}
