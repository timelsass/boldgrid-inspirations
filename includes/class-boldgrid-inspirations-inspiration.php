<?php

/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Inspiration
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
 * BoldGrid Inspiration class
 */
class Boldgrid_Inspirations_Inspiration extends Boldgrid_Inspirations {

	/**
	 * Class property $external_plugin_helper
	 */
	protected $external_plugin_helper;

	/**
	 * Class property $boldgrid_layout_helper
	 */
	protected $boldgrid_layout_helper;

	/**
	 * Accessor for $external_plugin_helper
	 */
	public function get_external_plugin_helper() {
		return $this->external_plugin_helper;
	}

	/**
	 * Constructor
	 *
	 * @param unknown $pluginPath
	 */
	public function __construct( $pluginPath ) {
		$this->pluginPath = $pluginPath;
		parent::__construct( $pluginPath );

		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-utility.php';
	}

	/**
	 * Add pre-init hooks
	 */
	public function add_pre_init_hooks() {
		// Update user metadata for last login:
		add_action( 'wp_login', array (
			$this,
			'update_last_login'
		) );

		// Ensure there is reseller info, if available:
		if ( false === get_option( 'boldgrid_reseller' ) ) {

			// Include the update class:
			require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-update.php';

			// Call the update_api_data method to get the latest data and set the reseller option:
			Boldgrid_Inspirations_Update::update_api_data();
		}

		// Branding
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-branding.php';
		$branding = new Boldgrid_Inspirations_Branding();
		$branding->add_hooks();

		// After plugins have been loaded, load the textdomain:
		add_action( 'plugins_loaded', array (
			$this,
			'boldgrid_load_textdomain'
		) );

		// This class is instantiated in later hook.
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-theme-install.php';
		// Apply BoldGrid theme config modifications
		Boldgrid_Inspirations_Theme_Install::universal_framework_configs();

		// If not on a network admin page, load stuff.
		if ( false === is_network_admin() ) {
			// This class is instantiated in later hook
			require_once BOLDGRID_BASE_DIR .
				 '/includes/class-boldgrid-inspirations-theme-install.php';

			if ( $this->is_preview_server ) {
				Boldgrid_Inspirations_Theme_Install::apply_theme_framework_configs();
			}
		}

		// When this plugin is activated, trigger additional operations.
		register_activation_hook( BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php',
			array (
				$this,
				'boldgrid_activate'
			) );
	}

	/**
	 * Pre-add hooks
	 */
	public function pre_add_hooks() {
		// If not an Administrator or is the preview server, then do not load this section.
		if ( ! current_user_can( 'manage_options' ) || $this->is_preview_server ) {
			return;
		}

		// If POST is an API key activation call, then handle the callback:
		if ( isset( $_POST['action'] ) && isset( $_POST['api_key'] ) &&
			 'set_api_key' == $_POST['action'] ) {
			$this->add_hooks_to_prompt_for_api_key();
		} else {
			// Get the configs:
			$configs = $this->get_configs();

			// Get the API hash from configs:
			$api_key_hash = isset( $configs['api_key'] ) ? $configs['api_key'] : null;

			// Verify API key and add hooks, or prompt for api key:
			$passes_api_check = false;

			if ( ! empty( $api_key_hash ) ) {
				$passes_api_check = $this->passes_api_check( true );
			}

			// Get the stored BoldGrid site hash:
			$boldgrid_site_hash = get_option( 'boldgrid_site_hash' );

			if ( $passes_api_check ) {
				// API key check passed, add hooks:
				$this->add_hooks();
			} elseif ( empty( $api_key_hash ) ) {
				// API key is no good; prompt to enter a valid key:
				$this->add_hooks_to_prompt_for_api_key();
			} elseif ( true !== parent::get_is_asset_server_available() &&
				 ! empty( $boldgrid_site_hash ) ) {
				// If the asset server is unavailable and we previously validated, then add hooks:
				$this->add_hooks();
			}
		}
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {

		// Post Theme Install Hooks
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-theme-install.php';
		$boldgrid_theme_install = new Boldgrid_Inspirations_Theme_Install( $this->configs );
		$boldgrid_theme_install->add_hooks();

		// Add hooks for admin section, or non-admin pages.
		if ( is_admin() ) {
			$configs = $this->get_configs();

			// Admin section

			// Check PHP and WordPress versions for compatibility.
			add_action( 'admin_init', array (
				$this,
				'check_php_wp_versions'
			) );

			// BoldGrid help link in the WordPress Help context tab.
			add_action( 'admin_bar_menu',
				array (
					$this,
					'add_boldgrid_help_context_tab_link'
				) );

			// If is a network admin page (other than update-core.php), just return.
			if ( is_network_admin() ) {
				global $pagenow;

				// If on the update-core.php or plugins.php pages, then load the update class.
				if ( 'update-core.php' == $pagenow || 'plugins.php' == $pagenow ) {
					require_once BOLDGRID_BASE_DIR .
						 '/includes/class-boldgrid-inspirations-update.php';
					$plugin_update = new Boldgrid_Inspirations_Update( $this );
				}

				return;
			}

			// Include all files needed by BoldGrid in the admin panel.
			$this->include_admin_files();

			// Helper to find active BG Plugins.
			$this->external_plugin_helper = new Boldgrid_Inspirations_External_Plugin( $configs );

			// Load Javascript and CSS.
			add_action( 'admin_enqueue_scripts', array (
				$this,
				'boldgrid_style'
			) );

			// Allow users to search through stock photos.
			$stock_photography = new Boldgrid_Inspirations_Stock_Photography( $this->pluginPath );
			$stock_photography->add_hooks();

			// Receipts.
			$boldgrid_receitps = new Boldgrid_Inspirations_Receipts();
			$boldgrid_receitps->add_hooks();

			// Purchase for publish.
			$purchase_for_publish = new Boldgrid_Inspirations_Purchase_For_Publish(
				$this->pluginPath );
			$purchase_for_publish->add_hooks();

			// Dashboard.
			$dashboard = new Boldgrid_Inspirations_Dashboard( $this->pluginPath );
			$dashboard->add_hooks();

			// BoldGrid Tutorials.
			$tutorials = new Boldgrid_Inspirations_Tutorials( $this->pluginPath );
			$tutorials->add_hooks();

			// Javascript files per screen.
			$screen = new Boldgrid_Inspirations_Screen();
			$screen->add_hooks();

			// Plugin updates.
			$plugin_update = new Boldgrid_Inspirations_Update( $this );

			// Plugin options.
			$plugin_options = new Boldgrid_Inspirations_Options();
			$plugin_options->add_hooks();

			// Boldgrid Layout section.
			$this->boldgrid_layout_helper = new Boldgrid_Inspirations_Built( $this );
			$this->boldgrid_layout_helper->add_hooks();

			// Dependency plugins.
			$dependency_plugins = new Boldgrid_Inspirations_Dependency_Plugins();
			$dependency_plugins->add_hooks();

			// Purchase Coins.
			$boldgrid_purchase_coins = new Boldgrid_Inspirations_Purchase_Coins();
			$boldgrid_purchase_coins->add_hooks();

			// Admin notices.
			$boldgrid_admin_notices = new Boldgrid_Inspirations_Admin_Notices();
			$boldgrid_admin_notices->add_hooks();

			// Easy Attachment Preview Size.
			$boldgrid_easy_attachment_preview_size = new Boldgrid_Inspirations_Easy_Attachment_Preview_Size();
			$boldgrid_easy_attachment_preview_size->add_hooks();

			// Asset Manager.
			$boldgrid_asset_manager = new Boldgrid_Inspirations_Asset_Manager();
			$boldgrid_asset_manager->add_hooks();

			// Pages And Posts.
			$boldgrid_pages_and_posts = new Boldgrid_Inspirations_Pages_And_Posts();
			$boldgrid_pages_and_posts->add_hooks();

			// IMHWPB.configs.
			add_action( 'admin_head',
				array (
					$this,
					'add_boldgrid_configs_to_header'
				) );

			// Check the connection to the asset server.
			add_action( 'wp_ajax_check_asset_server',
				array (
					$this,
					'check_asset_server_callback'
				) );

			// Include BoldGrid Inspirations Feedback:
			require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-feedback.php';
			$boldgrid_inspirations_feedback = new Boldgrid_Inspirations_Feedback();

			// GridBlock Sets - Admin Page.
			$gridblock_sets_admin = new Boldgrid_Inspirations_GridBlock_Sets_Admin( $this->configs );
			$gridblock_sets_admin->add_hooks();
		}

		/*
		 * Classes to add_hooks for, regardless of is_admin
		 */

		// Attribution:
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-attribution.php';
		$attribution = new Boldgrid_Inspirations_Attribution();
		$attribution->add_hooks();

		// Adding gridblock assets to relevant pages
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-gridblock.php';
		$boldgrid_inspirations_gridblock = new Boldgrid_Inspirations_Gridblock( $this->configs );
		$boldgrid_inspirations_gridblock->add_hooks();

		// GridBlock Sets - Preview Page.
		require_once BOLDGRID_BASE_DIR .
			 '/includes/class-boldgrid-inspirations-gridblock-sets-preview-page.php';
		$gridblock_sets_preview_page = new Boldgrid_Inspirations_GridBlock_Sets_Preview_Page();
		$gridblock_sets_preview_page->add_hooks();
	}

	/**
	 * Update last login in user metadata
	 *
	 * @param string $login
	 *        	WordPress login username passed by wp_login action.
	 */
	public function update_last_login( $login ) {
		$current_user = get_user_by( 'login', $login );
		$user_metadata = get_user_meta( $current_user->ID, 'last_login', true );
		if ( empty( $user_metadata ) ) {
			update_user_meta( $current_user->ID, 'first_login', current_time( 'mysql', true ) );
		}
		update_user_meta( $current_user->ID, 'last_login', current_time( 'mysql', true ) );

		// Update mobile login ratio.
		// Format of ratio: mobile:total logins.
		if ( is_multisite() ) {
			$mobile_ratio = get_site_option( 'boldgrid_mobile_ratio' );
		} else {
			$mobile_ratio = get_option( 'boldgrid_mobile_ratio' );
		}
		if ( false === empty( $mobile_ratio ) ) {
			$mobile_ratio_array = explode( ':', $mobile_ratio );
			$mobile_ratio_array[1] ++;
			if ( wp_is_mobile() ) {
				$mobile_ratio_array[0] ++;
			}
			$mobile_ratio = implode( ':', $mobile_ratio_array );
		} else {
			$mobile_ratio = ( wp_is_mobile() ? 1 : 0 ) . ':1';
		}
		if ( is_multisite() ) {
			$mobile_ratio = update_site_option( 'boldgrid_mobile_ratio', $mobile_ratio );
		} else {
			$mobile_ratio = update_option( 'boldgrid_mobile_ratio', $mobile_ratio );
		}
	}

	/**
	 *
	 * @param array $buttons
	 * @return array
	 */
	public function boldgrid_register_buttons( $buttons ) {
		array_push( $buttons, 'example' );
		return $buttons;
	}

	/**
	 * WPB Admin Styles - Scripts to enqueue on all pages
	 *
	 * Loads: style.css
	 * script.js
	 */
	public function boldgrid_style( $hook ) {
		// base-admin.js
		wp_enqueue_script( 'base-admin-js',
			plugins_url( '/assets/js/base-admin.js',
				BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ), array (), BOLDGRID_INSPIRATIONS_VERSION,
			true );

		// base-admin.css
		wp_register_style( 'base-admin-css',
			plugins_url( '/assets/css/base-admin.css',
				BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ), array (), BOLDGRID_INSPIRATIONS_VERSION );
		wp_enqueue_style( 'base-admin-css' );

		// ajax.js
		wp_enqueue_script( 'inspiration-ajax',
			plugins_url( '/assets/js/ajax/ajax.js',
				BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ), array (), BOLDGRID_INSPIRATIONS_VERSION,
			true );

		// handlebars
		wp_enqueue_script( 'inspiration-handle-bars',
			plugins_url( 'assets/js/handlebars/handlebars-v2.0.0.js',
				BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ), array (), BOLDGRID_INSPIRATIONS_VERSION,
			true );

		wp_enqueue_script( 'inspiration-handle-helper',
			plugins_url( 'assets/js/handlebars/handle-bar-helpers.js',
				BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ), array (), BOLDGRID_INSPIRATIONS_VERSION,
			true );

		/**
		 * Determine when to load our grid, grid.css.
		 */
		$hooks_to_load_grid = array (
			'toplevel_page_imh-wpb',
			'toplevel_page_boldgrid-inspirations',
			'transactions_page_boldgrid-cart',
			'settings_page_boldgrid-settings',
			'appearance_page_boldgrid-staging'
		);

		if ( in_array( $hook, $hooks_to_load_grid ) ) {
			// Thanks To https://github.com/zirafa/bootstrap-grid-only
			wp_register_style( 'boldgrid_admin',
				plugins_url( '/assets/css/grid.css',
					BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ), array (),
				BOLDGRID_INSPIRATIONS_VERSION );

			wp_enqueue_style( 'boldgrid_admin' );
		}

		/**
		 * Page: Inspiration
		 */
		if ( 'toplevel_page_boldgrid-inspirations' == $hook || 'toplevel_page_imh-wpb' == $hook ) {
			wp_register_style( 'boldgrid_inspiration_style',
				plugins_url( '/assets/css/style.css',
					BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ), array (),
				BOLDGRID_INSPIRATIONS_VERSION );

			wp_enqueue_style( 'boldgrid_inspiration_style' );
		}

		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

	/**
	 * Load plugin textdomain (translation files)
	 *
	 * @since 0.1
	 */
	public function boldgrid_load_textdomain() {
		load_plugin_textdomain( 'boldgrid-inspirations', false, BOLDGRID_BASE_DIR . '/languages/' );
	}

	/**
	 * Include all files needed by BoldGrid in the admin panel
	 */
	public function include_admin_files() {
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-external-plugin.php';
		require_once BOLDGRID_BASE_DIR .
			 '/includes/class-boldgrid-inspirations-stock-photography.php';
		require_once BOLDGRID_BASE_DIR .
			 '/includes/class-boldgrid-inspirations-purchase-for-publish.php';
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-dashboard.php';
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-tutorials.php';
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-screen.php';
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-update.php';
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-options.php';
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-built.php';
		require_once BOLDGRID_BASE_DIR .
			 '/includes/class-boldgrid-inspirations-dependency-plugins.php';
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-receipts.php';
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-purchase-coins.php';
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-admin-notices.php';
		require_once BOLDGRID_BASE_DIR .
			 '/includes/class-boldgrid-inspirations-easy-attachment-preview-size.php';
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-asset-manager.php';
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-pages-and-posts.php';
		require_once BOLDGRID_BASE_DIR .
			 '/includes/class-boldgrid-inspirations-gridblock-sets-admin.php';
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-analysis.php';
	}

	/**
	 * Run the deploy Script
	 */
	public function deploy_script() {
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-deploy.php';
		include BOLDGRID_BASE_DIR . '/pages/deploy.php';
	}

	/**
	 * Because many scripts will need our configs, let's go ahead and put them right in the header
	 */
	public function add_boldgrid_configs_to_header() {
		global $post;
		global $pagenow;

		$boldgrid_post_id = ( isset( $post->ID ) ? intval( $post->ID ) : "''" );

		// if we don't have a post id, try getting it from the URL
		if ( ! is_numeric( $boldgrid_post_id ) ) {
			$boldgrid_post_id = ( isset( $_GET['post_id'] ) ? intval( $_GET['post_id'] ) : "''" );
		}

		/* @formatter:off */
		$oneliner = '
			var IMHWPB = IMHWPB || {};
			IMHWPB.configs = ' . json_encode( $this->get_configs() ) . ';
			IMHWPB.post_id = ' . $boldgrid_post_id . ';
			IMHWPB.page_now = "' . $pagenow . '";
		';
		/* @formatter:on */
		Boldgrid_Inspirations_Utility::inline_js_oneliner( $oneliner );
	}

	/**
	 * On activation of BoldGrid, check Welcome Panel exists and make it show if not.
	 *
	 * @see register_activation_hook
	 * @param
	 *        	show_welcome_panel
	 * @since .11.13
	 */
	public function boldgrid_activate() {
		// If not on a network admin page, then reset the welcome panel and create an attribution page.
		if ( false === is_network_admin() ) {
			// Get the current user id.
			$user_id = get_current_user_id();

			// check to see if Welcome Panel is hidden, if it is show it.
			if ( 1 != get_user_meta( $user_id, 'show_welcome_panel', true ) ) {
				update_user_meta( $user_id, 'show_welcome_panel', 1 );
			}

			/*
			 * Create the Attribution page on activiation.
			 * BoldGrid themes include a link to the attribution page in the footer. If we haven't
			 * created an attribution page, then that link will generate a 404.
			 */
			if ( ! class_exists( 'Boldgrid_Inspirations_Attribution' ) ) {
				require_once BOLDGRID_BASE_DIR .
					 '/includes/class-boldgrid-inspirations-attribution.php';
			}
			$attribution = new Boldgrid_Inspirations_Attribution();
			$attribution->build_attribution_page();
		}

		// Get the current plugin version.
		$plugin_data = get_plugin_data( BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php', false );

		// Record the activated and current plugin version options.
		if ( is_multisite() ) {
			update_site_option( 'boldgrid_inspirations_activated_version', $plugin_data['Version'] );
			update_site_option( 'boldgrid_inspirations_current_version', $plugin_data['Version'] );
		} else {
			update_option( 'boldgrid_inspirations_activated_version', $plugin_data['Version'] );
			update_option( 'boldgrid_inspirations_current_version', $plugin_data['Version'] );
		}
	}

	/**
	 * Add BoldGrid help link in the WordPress Help context tab
	 */
	public function add_boldgrid_help_context_tab_link() {
		// Get the current screen:
		$screen = get_current_screen();

		// Variable to toggle BoldGrid help tabs: (true|false):
		$show_boldgrid_help_tabs = false;

		// Add new tab id screen is the dashboard, a boldgrid page, or editing a page or post:
		if ( preg_match( '/^(dashboard|page|post|.+boldgrid-.+|.+imh-wpb|transactions_page_.+)$/',
			$screen->id ) ) {
			if ( true === $show_boldgrid_help_tabs ) {
				// Select content for the BoldGrid help tab:
				switch ( $screen->id ) {
					case 'page' :
						$help_tab = array (
							'title' => 'BoldGrid Help',
							'content' => 'This is a BoldGrid help section for editing pages.  Feel free to visit <a target="_blank" href="http://www.boldgrid.com/">BoldGrid.com</a>'
						);
						break;

					case 'post' :
						$help_tab = array (
							'title' => 'BoldGrid Help',
							'content' => 'This is a BoldGrid help section for editing posts.  Feel free to visit <a target="_blank" href="http://www.boldgrid.com/">BoldGrid.com</a>'
						);
						break;

					case 'transactions_page_cart' :
						$help_tab = array (
							'title' => 'BoldGrid Help',
							'content' => 'This is a BoldGrid help section for cart/checkout.  Feel free to visit <a target="_blank" href="http://www.boldgrid.com/">BoldGrid.com</a>'
						);
						break;

					case 'transactions_page_boldgrid-receipts' :
						$help_tab = array (
							'title' => 'BoldGrid Help',
							'content' => 'This is a BoldGrid help section for receipts/transaction history.  Feel free to visit <a target="_blank" href="http://www.boldgrid.com/">BoldGrid.com</a>'
						);
						break;

					case 'transactions_page_boldgrid-purchase-coins' :
						$help_tab = array (
							'title' => 'BoldGrid Help',
							'content' => 'This is a BoldGrid help section for purchasing coins.  Feel free to visit <a target="_blank" href="http://www.boldgrid.com/">BoldGrid.com</a>'
						);
						break;

					default :
						$help_tab = array (
							'title' => 'BoldGrid Help',
							'content' => 'This is a BoldGrid help section.  Feel free to visit <a target="_blank" href="http://www.boldgrid.com/">BoldGrid.com</a>'
						);
						break;
				}

				// Add the link:
				$screen->add_help_tab(
					array (
						'id' => 'boldgrid-inspirations-help',
						'title' => __( $help_tab['title'] ),
						'content' => __( $help_tab['content'] )
					) );
			}

			// Get the help sidebar content:
			$help_sidebar_content = $screen->get_help_sidebar();

			// Add help sidebar content:
			$screen->set_help_sidebar(
				$help_sidebar_content . '<a target="_blank" href="http://www.boldgrid.com/">' .
					 'BoldGrid.com' . '</a>' );
		}
	}
}
