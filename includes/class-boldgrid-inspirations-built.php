<?php

/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Built
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
 * BoldGrid Inspiration Built class
 */
class Boldgrid_Inspirations_Built {

	/**
	 * An instance of Boldgrid_Inspirations_Inspiration
	 *
	 * @var Inspiration
	 */
	protected $inspiration;

	/**
	 * The data gathered about the users scenario
	 *
	 * @var array
	 */
	protected $mode_data;

	/**
	 * The users installation settings
	 *
	 * @var array
	 */
	protected $install_options;

	/**
	 * Bool that checks if staging plugin is active
	 *
	 * @var Bool
	 */
	protected $staging_plugin_active = false;

	/**
	 * Array of theme names
	 *
	 * @var array
	 */
	protected $current_theme_names;

	/**
	 * Tke in the main plugin as a param
	 *
	 * @param Inspiration $inspiration
	 */
	public function __construct( $inspiration ) {
		$this->inspiration = $inspiration;
	}

	/**
	 * The Steps that are shown for both journeys
	 *
	 * @var array
	 */
	private $nav_steps = array (
		'standard' => array (
			'step-1' => array (
				'title' => 'Step 1: Category',
				'content' => 'includes/inspiration_category_select.php'
			),
			'step-2' => array (
				'title' => 'Step 2: Base Website',
				'content' => 'includes/base_website.php'
			),
			'step-3' => array (
				'title' => 'Step 3: Pages',
				'content' => 'includes/page_set_selection.php'
			)
		),
		'inspired' => array (
			'step-1' => array (
				'title' => 'Themes',
				'content' => 'includes/theme_selection.php'
			)
		)
	);

	/**
	 * Add actions/hooks
	 */
	public function add_hooks() {

		// Find the users individual scenario and set up the menu
		add_action( 'admin_menu', array (
			$this,
			'admin_menu'
		) );

		// Add the needed styles
		add_action( 'admin_enqueue_scripts', array (
			$this,
			'enqueue_scripts'
		) );

		// Should the user be starting with inspirations? If so, give them a notice at the top of
		// the page.
		add_action( 'admin_notices',
			array (
				$this,
				'you_should_start_with_inspirations'
			) );
	}

	/**
	 * Generate the scenario data and add the users menu items.
	 */
	public function admin_menu() {
		$this->staging_plugin_active = $this->check_staging_plugin();
		$this->mode_data = $this->generate_scenarios();

		global $boldgrid_inspiration_menu_items;
		$boldgrid_inspiration_menu_items = $this->mode_data['menu'];

		$this->add_top_menu_item( 'boldgrid-inspirations' );

		self::add_sub_menu_items( $boldgrid_inspiration_menu_items, 'boldgrid-inspirations' );
	}

	/**
	 * Checks to see if the staging plugin is active
	 *
	 * @return boolean
	 */
	public function check_staging_plugin() {
		$staging_plugin_active = $this->inspiration->get_external_plugin_helper()
			->plugin_is_active( 'staging' );

		return ( $staging_plugin_active && class_exists( 'Boldgrid_Staging_Page_And_Post_Staging' ) );
	}

	/**
	 * Returns the name of a theme if and only if the theme is a boldgrid theme
	 *
	 * @param WP_Theme $wp_theme
	 *
	 * @return string
	 */
	public static function get_boldgrid_theme_name( $wp_theme ) {
		$current_boldgrid_theme = '';

		$current_theme = $wp_theme;

		if ( is_a( $current_theme, 'WP_Theme' ) &&
			 strtolower( $current_theme->get( 'TextDomain' ) ) == 'boldgrid' ) {
			$current_boldgrid_theme = $current_theme->get( 'Name' );
		}

		return $current_boldgrid_theme;
	}

	/**
	 * Get all pages by status
	 *
	 * @param string $post_status
	 *
	 * @return array
	 */
	public static function get_installed_pages( $post_status ) {
		$all_pages = get_pages(
			array (
				'post_status' => array (
					$post_status,
					'draft'
				)
			) );

		if ( false == is_array( $all_pages ) ) {
			$all_pages = array ();
		}

		$boldgrid_pages = array ();

		foreach ( $all_pages as $page ) {
			$post_meta = get_post_meta( $page->ID );
			if ( isset( $post_meta['boldgrid_page_id'] ) ) {
				$boldgrid_pages[] = $post_meta['boldgrid_page_id'][0];
			}
		}

		return $boldgrid_pages;
	}

	/**
	 * Find the users installation data
	 *
	 * @return array
	 */
	public static function find_all_install_options() {
		// Get Installed Settings:
		( $active_install_options = get_option( 'boldgrid_install_options' ) ) ||
			 ( $active_install_options = array () );

		$active_install_options['installed_pages'] = self::get_installed_pages( 'publish' );
		$active_install_options['theme_stylesheet'] = get_stylesheet();
		$active_install_options['theme_name'] = self::get_boldgrid_theme_name( wp_get_theme() );

		$install_options['active_options'] = $active_install_options;

		( $staging_install_options = get_option( 'boldgrid_staging_boldgrid_install_options' ) ) ||
			 ( $staging_install_options = array () );

		$staging_install_options['installed_pages'] = self::get_installed_pages( 'staging' );
		$staging_install_options['theme_name'] = self::get_boldgrid_theme_name(
			$staging_theme = self::get_staging_theme() );

		if ( $staging_theme ) {
			$staging_install_options['theme_stylesheet'] = $staging_theme->get_stylesheet();
		}

		$install_options['boldgrid_staging_options'] = $staging_install_options;
		$install_options['theme_release_channel'] = Boldgrid_Inspirations_Theme_Install::fetch_theme_channel();

		return $install_options;
	}

	/**
	 * Check to see if the user has a staged site
	 *
	 * @return boolean
	 */
	public function has_staged_site() {
		$has_staged_site = false;

		if ( $this->staging_plugin_active ) {
			$staged_pages = Boldgrid_Staging_Page_And_Post_Staging::get_all_staged_pages();
			$staged_pages = is_array( $staged_pages ) ? $staged_pages : array ();
			$has_staged_site = ( bool ) count( $staged_pages );
		}

		return $has_staged_site;
	}

	/**
	 * Check to see if the user has an active site
	 *
	 * @return boolean
	 */
	public static function has_active_site() {
		// Get all pages:
		$pages = get_pages();

		// If there are no pages, then return false:
		if ( empty( $pages ) ) {
			return false;
		}

		// Get default, attribution, and coming soon pages:
		$default_page = get_page_by_title( 'Sample Page' );
		$attribution_page = get_page_by_title( 'Attribution' );
		$coming_soon_page = get_page_by_title( 'WEBSITE COMING SOON' );

		// Initialize $ids_to_remove:
		$ids_to_filter = array ();

		// Get the boldgrid_attribution option data:
		$attribution = get_option( 'boldgrid_attribution' );

		// If there is attribution data, then add the page id to $ids_to_filter:
		if ( ! empty( $attribution ) && isset( $attribution['page']['id'] ) ) {
			$ids_to_filter[] = $attribution['page']['id'];
		}

		// Add the page ids of the default, attribution, and coming soon pages from title match, to the array:
		foreach ( array (
			$default_page,
			$attribution_page,
			$coming_soon_page
		) as $page ) {
			if ( null !== $page ) {
				$ids_to_filter[] = $page->ID;
			}
		}

		// Build an array of page objects that do not match page ids in $ids_to_filter:
		$active_pages = array ();

		foreach ( $pages as $page ) {
			if ( false === in_array( $page->ID, $ids_to_filter ) ) {
				$active_pages[] = $page;
			}
		}

		// Return whether or not we have any pages in the array:
		return ! empty( $active_pages );
	}

	/**
	 * Get the staging theme name from the staging plugin
	 *
	 * @return WP_Theme | null
	 */
	public static function get_staging_theme() {
		return class_exists( 'Boldgrid_Staging_Theme' ) ? Boldgrid_Staging_Theme::get_staging_theme() : null;
	}

	/**
	 * Get the menu slug needed to make sure that the first item has the same slug as the primary
	 *
	 * @param string $top_level
	 *
	 * @return string
	 */
	public static function get_menu_slug( &$top_level ) {
		if ( $top_level ) {
			$slug = $top_level;
			$top_level = '';
		} else {
			$slug = '';
		}

		return $slug;
	}

	/**
	 * Add the styles and the scripts
	 */
	public function enqueue_scripts() {
		$current_screen = get_current_screen();

		if ( 'toplevel_page_boldgrid-inspirations' == $current_screen->base ) {

			// Enqueue Scripts
			if ( ! isset( $_REQUEST['task'] ) ) {
				wp_enqueue_script( 'inspiration-js',
					plugins_url( 'assets/js/inspiration.js',
						BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ), array (),
					BOLDGRID_INSPIRATIONS_VERSION, true );

				wp_enqueue_script( 'jquery-ui-autocomplete' );

				wp_register_script( 'boldgrid-inspiration-built',
					plugins_url( 'assets/js/inspiration-built.js',
						BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ),
					array (
						'inspiration-js'
					), BOLDGRID_INSPIRATIONS_VERSION, true );

				wp_localize_script( 'boldgrid-inspiration-built', 'Inspiration',
					array (
						'build_status' => $this->mode_data['mode'],
						'install_options' => $this->install_options,
						'page_selection' => $this->mode_data['page_selection'],
						'mode_data' => $this->mode_data
					) );

				wp_enqueue_script( 'boldgrid-inspiration-built' );
			}
		}
	}

	/**
	 * Add the top level menui item "Inspirations"
	 *
	 * @param unknown $top_level
	 */
	public function add_top_menu_item( $top_level ) {
		add_menu_page( 'Inspirations', 'Inspirations', 'manage_options', $top_level,
			array (
				$this,
				'inspiration_page'
			), 'dashicons-lightbulb', '21.36' );
	}

	/**
	 * Add Layouts Menu Item after pages.
	 */
	public static function add_sub_menu_items( $menu_items, $top_level ) {
		$static_top_level = $top_level;

		// Add the themes submenu if needed.
		if ( false !== array_search( 'Themes', $menu_items ) ) {

			$slug = self::get_menu_slug( $top_level );
			add_submenu_page( $static_top_level, 'Install New Themes', 'Install New Themes',
				'manage_options',
				$slug ? $slug : 'admin.php?page=boldgrid-inspirations&boldgrid-tab=themes' );
		}

		// Add the Install submenu if needed.
		if ( false !== array_search( 'Install', $menu_items ) ) {

			$slug = self::get_menu_slug( $top_level );
			add_submenu_page( $static_top_level, 'Install New Site', 'Install New Site',
				'manage_options',
				$slug ? $slug : 'admin.php?page=boldgrid-inspirations&boldgrid-tab=install' );
		}
	}

	/**
	 * Callback that will render the Boldgrid Inspiration phase.
	 */
	public function inspiration_page() {
		$boldgrid_configs = Boldgrid_Inspirations_Config::get_format_configs();

		$api_call_results = Boldgrid_Inspirations::boldgrid_api_call(
			$boldgrid_configs['ajax_calls']['get_version'] );

		if ( is_null( $api_call_results ) ) {
			error_log( __METHOD__ . ': Error getting BoldGrid version.' );

			wp_die( $this->inspiration->notify_connection_issue() );
		}

		// If the users task is deploy, include the deploy files.
		if ( isset( $_POST['task'] ) && 'deploy' == $_POST['task'] ) {
			// Check nonce:
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'deploy' ) ) {
				// Could not validate nonce.
				wp_die( 'You must deploy a website from BoldGrid Inspirations Step 3!' );
			} else {
				// Clear to deploy.
				$this->inspiration->deploy_script();
			}

			// The user is installing a theme only.
			// Take to a confirmation page where the user can activate it.
		} else if ( isset( $_GET['task'] ) && 'theme-install-success' == $_GET['task'] ) {
			$stylesheet = strip_tags( $_GET['stylesheet'] );
			$staging = strip_tags( $_GET['staging'] );
			$error = false;
			$wp_theme = wp_get_theme( $stylesheet );

			if ( is_object( $wp_theme ) ) {
				$staging_url = '';

				if ( '1' == $staging ) {
					$staging_url = '&staging=1';
				}

				$theme_label = $wp_theme->Name . ' - ' . $wp_theme->Version;
				$theme_styelsheet = $stylesheet;

				$enable_theme_url = wp_nonce_url(
					get_admin_url() . 'themes.php?action=activate' . $staging_url . '&stylesheet=' .
						 $theme_styelsheet, 'switch-theme_' . $stylesheet );
			} else {
				$error = true;
			}

			include BOLDGRID_BASE_DIR . '/pages/theme-install-success.php';
		} else {
			// Set $nav_steps for the template "/pages/inspirations.php".
			$nav_steps = $this->nav_steps[$this->mode_data['mode']];

			// Get the boldgrid_api_data transient to get is_author.
			if ( is_multisite() ) {
				$boldgrid_api_data = get_site_transient( 'boldgrid_api_data' );
			} else {
				$boldgrid_api_data = get_transient( 'boldgrid_api_data' );
			}

			$is_author = ! empty( $boldgrid_api_data->result->data->is_author );
			$theme_channel = Boldgrid_Inspirations_Theme_Install::fetch_theme_channel();

			// Include the inspirations page.
			include BOLDGRID_BASE_DIR . '/pages/inspiration.php';

			// @todo: This variable can probably be removed in the future.
			// Set a javascript variable to flag this inspirations_type.
			wp_localize_script( 'boldgrid-inspiration-built', 'boldgrid_inspirations_type',
				$this->mode_data['mode'] );
		}
	}

	/**
	 * Determine the users Scenario.
	 *
	 * @return array
	 */
	public function generate_scenarios() {
		$this->install_options = self::find_all_install_options();

		// Does the user have Active or Staging Sites.
		$has_active_site = self::has_active_site();
		$has_staged_site = $this->has_staged_site();

		// Has the user built their staging and/or active site with BoldGrid?.
		$has_built_with_wpb = ( $has_active_site && 'yes' == get_option( 'boldgrid_has_built_site' ) );

		$has_built_with_wpb_staged = ( $has_staged_site && 'yes' == get_option(
			'boldgrid_staging_boldgrid_has_built_site' ) );

		// This is a fail safe if installing a website does not complete.
		// If the users has installed pages then the user must have built with BoldGrid.
		// If this is not true, it is because an installation has failed, override the active site
		// so that the user can reenter the process.
		if ( $this->install_options['active_options']['installed_pages'] && $has_active_site &&
			 false == $has_built_with_wpb ) {
			$has_active_site = false;
		}

		// This is the same for the above but for failed staged sites.
		if ( $this->install_options['boldgrid_staging_options']['installed_pages'] &&
			 $has_staged_site && false == $has_built_with_wpb_staged ) {
			$has_staged_site = false;
		}

		// The user has built either of their sites with boldgrid.
		$has_built_with_either = ( $has_built_with_wpb || $has_built_with_wpb_staged );

		/*
		 * Given the previous settings.
		 * - determine what will display in the menu.
		 * - whether we will install into stage, or active.
		 */
		// A site can only change pages, if its been built with boldgrid.
		$menu_options = array ();

		$site_install_destination = $inspired_install_destination = false;

		$inspiration_settings = array (
			$has_active_site,
			$has_staged_site,
			$this->staging_plugin_active,
			$has_built_with_either
		);

		switch ( $inspiration_settings ) {
			case array (
				false,
				false,
				false,
				false
			) :
				$menu_options = array (
					'Inspiration'
				);
				$site_install_destination = 'active';
				break;

			case array (
				false,
				false,
				true,
				false
			) :
				$menu_options = array (
					'Inspiration'
				);
				$site_install_destination = 'choice';
				break;

			case array (
				true,
				false,
				true,
				false
			) :
				$menu_options = array (
					'Themes',
					'Install'
				);
				$site_install_destination = 'stage';
				$inspired_install_destination = 'active';
				break;

			case array (
				false,
				true,
				true,
				false
			) :
				$menu_options = array (
					'Themes',
					'Install'
				);
				$site_install_destination = 'active';
				$inspired_install_destination = 'stage';
				break;

			case array (
				false,
				true,
				true,
				true
			) :
				$menu_options = array (
					'Themes',
					'Install'
				);
				$site_install_destination = 'active';
				$inspired_install_destination = 'stage';
				break;

			case array (
				true,
				false,
				false,
				false
			) :
				$menu_options = array (
					'Themes'
				);
				$inspired_install_destination = 'active';
				break;

			case array (
				true,
				false,
				false,
				true
			) :
				$menu_options = array (
					'Themes'
				);
				$inspired_install_destination = 'active';
				break;

			case array (
				true,
				false,
				true,
				true
			) :
				$menu_options = array (
					'Themes',
					'Install'
				);
				$site_install_destination = 'stage';
				$inspired_install_destination = 'active';
				break;

			case array (
				true,
				true,
				true,
				false
			) :
				$menu_options = array (
					'Themes'
				);
				$inspired_install_destination = 'choice';
				break;

			case array (
				true,
				true,
				true,
				true
			) :
				$menu_options = array (
					'Themes'
				);
				$inspired_install_destination = 'choice';
				break;

			default :
				$menu_options = array (
					'Inspiration'
				);
				break;
		}

		// Mode is either Standard or Inspired
		// Standard indicated that the user is building an entire site
		// Inspired indicates that the user is adding pages or themes
		$mode = 'standard';

		$inspired_settings = array (
			'themes'
		);

		$tab_request = isset( $_REQUEST['boldgrid-tab'] ) ? $_REQUEST['boldgrid-tab'] : null;

		if ( in_array( $tab_request, $inspired_settings ) ) {
			$mode = 'inspired';
		}

		// If the user did not request a tab, that the request will be the first option of the menu
		if ( ! $tab_request && in_array( strtolower( $menu_options[0] ), $inspired_settings ) ) {
			$mode = 'inspired';
		}

		//
		if ( ( 'inspired' == $mode && $tab_request ) || 'install' == $tab_request ) {
			$page_selection = $tab_request;
		} else {
			$page_selection = strtolower( $menu_options[0] );
		}

		// We are inspired, install settings don't matter.
		if ( 'inspired' == $mode ) {
			$site_install_destination = false;
		}

		// We are going into install, inspired setting does not matter.
		if ( 'standard' == $mode ) {
			$inspired_install_destination = false;
		}

		$build_any = false;

		// Build any indicates that we dont have a subcategory, so the build process
		// will build any category.
		if ( 'inspired' == $mode && false == $has_built_with_either ) {
			$build_any = true;
		}

		// If either of these are set to stage, we will default to stage.

		// Create return array.
		$return = array (
			'menu' => $menu_options,
			'mode' => $mode,
			'page_selection' => $page_selection,
			'install_destination' => $site_install_destination,
			'inspired_install_destination' => $inspired_install_destination,
			'open-section' => ( ! empty( $_GET['force-section'] ) ) ? sanitize_text_field(
				$_GET['force-section'] ) : '',
			'staging_active' => $this->staging_plugin_active,
			'build_any' => $build_any,
			'url' => get_admin_url() . 'admin.php?page=boldgrid-inspirations',
			'has_active_site' => $has_active_site,
			'has_staged_site' => $has_staged_site,
			'has_built_with_either' => $has_built_with_either
		);

		// Return the array.
		return $return;
	}

	/**
	 * Should the user be starting with inspirations? If so, give them a notice at the top of the
	 * page.
	 */
	public function you_should_start_with_inspirations() {
		$pages_to_show_this_notice = array (
			'post-new.php',
			'theme-install.php'
		);

		global $pagenow;

		// Abort if necessary.
		if ( ! in_array( $pagenow, $pages_to_show_this_notice ) ) {
			return;
		}

		// Generate our scenario data.
		$scenarios = $this->generate_scenarios();

		// Should we display the message?
		$display_message = ( ! isset( $scenarios['has_built_with_either'] ) ||
			 false == $scenarios['has_built_with_either'] ) ? true : false;

		// Display our alert.
		if ( true == $display_message ) {
			?>
<div class="error notice is-dismissible">
	<p>
		We've recognized that you haven't installed an Active or Staging site
		with Inspirations. Before adding additional pages and themes, we
		recommend that you start with <a
			href="admin.php?page=boldgrid-inspirations&boldgrid-tab=install"
			class="dashicons-before dashicons-lightbulb"
			style="text-decoration: none;">Inspirations</a>.
	</p>
</div>
<?php
		}
	}
}
