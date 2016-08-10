<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Options
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
 * BoldGrid Inspirations Start Over class
 */
class Boldgrid_Inspirations_Start_Over {
	/**
	 * Is the staging plugin installed?
	 *
	 * @var bool
	 */
	public $staging_installed = false;

	/**
	 * Does the user want to start over with their active site?
	 *
	 * @var bool
	 */
	public $start_over_active = false;

	/**
	 * Does the user want to start over with their staging site?
	 *
	 * @var bool
	 */
	public $start_over_staging = false;

	/**
	 *
	 */
	public $delete_forms = false;

	/**
	 *
	 */
	public $delete_pages = false;

	/**
	 *
	 */
	public $delete_themes = false;

	/**
	 * Get install options
	 *
	 * @return mixed
	 */
	public static function get_install_options() {
		$install_options = get_option( 'boldgrid_install_options' );

		return $install_options;
	}

	/**
	 * Search option table for option names begining with $search.
	 *
	 * @param string $search
	 * @return array|boolean
	 */
	public function get_option_names_starting_with( $search ) {
		global $wpdb;

		$search = $wpdb->esc_like( $search );

		$options = $wpdb->get_col(
			$wpdb->prepare(
				"
				SELECT      option_name
				FROM        {$wpdb->prefix}options
				WHERE       (`option_name` LIKE '%s')
				", $search . '%' ) );

		if ( is_array( $options ) and ! empty( $options ) ) {
			return $options;
		} else {
			return false;
		}
	}

	// Hooks
	public function add_hooks() {
		if ( is_admin() ) {
			// Javascript
			add_action( 'admin_enqueue_scripts',
				array (
					$this,
					'enqueue_boldgrid_options_js'
				) );

			// Options Submenu Node
			add_action( 'admin_menu',
				array (
					$this,
					'boldgrid_admin_add_options_submenu'
				) );

			// Options Page Init
			add_action( 'admin_init', array (
				$this,
				'boldgrid_admin_init'
			) );
		}
	}

	/**
	 * Enqueue the JS file that controls our agreement checkbox, and
	 * warns a user that they will be deleting stuff if they check
	 * stuff in this section.
	 *
	 * @since .21
	 */
	public function enqueue_boldgrid_options_js( $hook ) {
		if ( 'settings_page_boldgrid-settings' == $hook ) {
			wp_enqueue_script( 'boldgrid-options',
				plugins_url( '/assets/js/boldgrid-options.js',
					BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php' ), array (), '', true );
		}
	}

	/**
	 * Delete all pages and menus
	 */
	public function cleanup_pages_and_menus() {
		$post_types = array (
			'page',
			'post',
			'revision',
			'attachment'
		);

		$post_statuses = array (
			'publish',
			'staging',
			'draft'
		);

		$active_post_statuses = array (
			'publish',
			'draft'
		);

		/**
		 * ************************************************************
		 * Step 1: Get all page IDs that we'll need to delete.
		 * ************************************************************
		 */
		$page_ids = null;

		foreach ( $post_types as $post_type ) {
			foreach ( $post_statuses as $post_status ) {
				// If we're trying to get staging pages but the user does not want to start over
				// with staging, continue / abort.
				if ( 'staging' == $post_status && false == $this->start_over_staging ) {
					continue;
				}

				// If we're trying to get active pages (public / draft) but the user does not want
				// to start over with active, continue / abort.
				if ( in_array( $post_status, $active_post_statuses ) &&
					 false == $this->start_over_active ) {
					continue;
				}

				$pages = get_posts(
					array (
						'post_type' => $post_type,
						'post_status' => $post_status,
						'numberposts' => - 1
					) );

				if ( count( $pages ) && isset( $pages[0]->ID ) ) {
					foreach ( $pages as $page ) {
						$page_ids[] = $page->ID;
					}
				}
			}
		}

		// Add our attribution page to the pages list as well:

		$attribution = get_option( 'boldgrid_attribution' );

		if ( is_array( $attribution ) && ! empty( $attribution['page']['id'] ) ) {
			$page_ids[] = $attribution['page']['id'];
		}

		// Allow other plugins to modify the page id's that are deleted.
		$page_ids = apply_filters( 'boldgrid_inspirations_cleanup_page_ids', $page_ids );

		/**
		 * ************************************************************
		 * Step 2: Delete those pages.
		 * ************************************************************
		 */
		if ( null != $page_ids ) {
			foreach ( $page_ids as $page_id ) {
				wp_delete_post( $page_id, $this->delete_pages ); // 2nd param: false = trash, true = delete
			}
		}

		/*
		 * If the user is starting over with their staging site, set the BoldGrid Staging's session
		 * setting to point the user to their active site.
		 *
		 * If the user's last setting before starting over pointed them to staging, and they delete
		 * their staging site, then let's not send them to their staging site when they don't
		 * have one.
		 */
		if( session_id() && $this->start_over_staging ) {
			$_SESSION['wp_staging_view_version'] = 'production';
		}
	}

	/**
	 * Remove menus.
	 *
	 * Based on the user's request, delete either / both the active / staging menus.
	 */
	public function cleanup_nav_menus() {
		/**
		 * Active site.
		 *
		 * BoldGrid uses one menu, 'primary'. Let's delete that menu
		 */
		if ( true == $this->start_over_active ) {
			wp_delete_nav_menu( 'primary' );
		}

		/**
		 * Staging site.
		 */
		if ( true == $this->start_over_staging ) {
			do_action( 'boldgrid_options_cleanup_nav_menus' );
		}
	}

	/**
	 * Update / delete various wp_options
	 */
	public function cleanup_wp_options() {
		/**
		 * ********************************************************************
		 * DELETE options (both active / staging)
		 * ********************************************************************
		 */
		// Define a set of options to DELETE, and then delete them.
		$options_to_delete = array (
			'boldgrid_asset',
			'boldgrid_install_options',
			'boldgrid_attribution',
			'boldgrid_installed_page_ids',
			'boldgrid_installed_pages_metadata',
			'boldgrid_show_tip_start_editing',
			// Class: Boldgrid_Inspirations_GridBlock_Sets_Kitchen_Sink.
			'boldgrid_inspirations_fetching_kitchen_sink_status',
			'_transient_boldgrid_inspirations_kitchen_sink',
			'_transient_timeout_boldgrid_inspirations_kitchen_sink'
		);

		// Delete those options.
		foreach ( $options_to_delete as $option ) {
			// Active site
			if ( true == $this->start_over_active ) {
				delete_option( $option );
			}

			// Staging site.
			// Try to delete the staging version of the option as well.
			if ( true == $this->start_over_staging ) {
				delete_option( 'boldgrid_staging_' . $option );
			}
		}

		/**
		 * ********************************************************************
		 * UPDATE options (both active / staging)
		 * ********************************************************************
		 */
		// Update options:
		if ( true == $this->start_over_active ) {
			update_option( 'boldgrid_has_built_site', 'no' );
		}
		if ( true == $this->start_over_staging ) {
			update_option( 'boldgrid_staging_boldgrid_has_built_site', 'no' );
		}

		// Delete ALL "boldgrid_staging_%" options.
		if ( true == $this->start_over_staging ) {
			$staging_options = $this->get_option_names_starting_with( 'boldgrid_staging_' );

			if ( $staging_options ) {
				foreach ( $staging_options as $option_to_delete ) {
					delete_option( $option_to_delete );
				}
			}
		}
	}

	/**
	 * Function hook to add the BoldGrid Settings submenu
	 */
	public function boldgrid_admin_add_options_submenu() {
		add_submenu_page( 'options-general.php', 'BoldGrid Settings', 'BoldGrid', 'administrator',
			'boldgrid-settings', array (
				$this,
				'boldgrid_options_page'
			) );
	}

	/**
	 * Options page
	 */
	public function boldgrid_options_page() {
		// If the user wants to start over, go ahead and delete everything.
		if ( true == $this->user_wants_to_start_over() ) {
			$this->start_over();
		}

		// "Wrap" the page so that it has nice margins.
		echo '<div class="wrap">';

		$this->print_section_boldgrid_settings();

		$this->print_section_to_reset_pointers();

		$this->print_section_to_start_over();

		echo '</div>';
	}

	/**
	 * Admin init function for the options page
	 */
	public function boldgrid_admin_init() {
		register_setting( 'boldgrid_options', 'boldgrid_settings',
			array (
				$this,
				'boldgrid_options_validate'
			) );

		add_settings_section( 'boldgrid_options_main', 'Global Settings',
			array (
				$this,
				'boldgrid_options_global_text'
			), 'boldgrid-settings' );

		// Add the setting field for plugin update release channel.
		add_settings_field( 'boldgrid_select_release_channel', 'Plugin Update Channel<br />',
			array (
				$this,
				'plugin_channel_text'
			), 'boldgrid-settings', 'boldgrid_options_main' );

		// Add the setting field for plugin auto-updates.
		add_settings_field( 'boldgrid_auto_plugin_updates', 'Plugin Auto-Updates<br />',
			array (
				$this,
				'plugin_autoupdate_text'
			), 'boldgrid-settings', 'boldgrid_options_main' );

		// Add the setting field for theme update release channel.
		add_settings_field( 'boldgrid_select_theme_release_channel', 'Theme Update Channel<br />',
			array (
				$this,
				'theme_channel_text'
			), 'boldgrid-settings', 'boldgrid_options_main' );

		// Add the setting field for plugin auto-updates.
		add_settings_field( 'boldgrid_auto_theme_updates', 'Theme Auto-Updates<br />',
			array (
				$this,
				'theme_autoupdate_text'
			), 'boldgrid-settings', 'boldgrid_options_main' );

		// Add setting field for menu reordering switching
		add_settings_field( 'boldgrid_menu_option', 'Reorder Admin Menu',
			array (
				$this,
				'boldgrid_menu_callback'
			), 'boldgrid-settings', 'boldgrid_options_main' );

		/*
		 * // Add the setting field for feedback opt-out:
		 * add_settings_field( 'boldgrid_feedback_optout', 'Feedback Opt-out',
		 * array (
		 * $this,
		 * 'boldgrid_feedback_optout_callback'
		 * ), 'boldgrid-settings', 'boldgrid_options_main' );
		 */

		// Is the staging plugin installed?
		$this->set_staging_installed();
	}

	/**
	 * Display the options page body
	 */
	public function boldgrid_options_global_text() {
		?>
		Here you may change the BoldGrid plugin global settings.
<br />
<?php
	}

	/**
	 * Display the release channel options for plugins.
	 *
	 * @since 1.1.6
	 */
	public function plugin_channel_text() {
		$this->boldgrid_option_select_release_channel_text();
	}

	/**
	 * Display the release channel options for themes.
	 *
	 * @since 1.1.6
	 */
	public function theme_channel_text() {
		$this->boldgrid_option_select_release_channel_text( 'theme_' );
	}

	/**
	 * Display the options page setting for Update Channel.
	 */
	public function boldgrid_option_select_release_channel_text( $type = '' ) {
		// Retrieve the blog option boldgrid_settings.
		$options = get_option( 'boldgrid_settings' );

		// Should we show the candidate option?
		$show_all_channels = ( true === isset( $_GET['channels'] ) && 'all' === $_GET['channels'] );

		// Ensure there is a site option copied from the blog option boldgrid_settings.
		if ( false === empty( $options[ $type . 'release_channel' ] ) ) {
			update_option( 'boldgrid_settings', $options );
		}

		/*
		 * Print the radio buttons for stage, edge, and candidate (if applicable).
		 *
		 * 1: Create an array $channel_options of radio options.
		 * 2: Use implode to display the array of radio options.
		 */

		// STABLE.
		$stable_checked = ( false === isset( $options[ $type . 'release_channel' ] ) ||
			 ( isset( $options[ $type . 'release_channel'] ) && 'stable' == $options[ $type . 'release_channel'] ) ) ? 'checked' : '';

		$channel_options[] = '<input type="radio" id="' . $type .
			'release_channel_stable" name="boldgrid_settings[' . $type . 'release_channel]" value="stable" ' .
			 $stable_checked . ' /> Stable';

		// EDGE.
		$edge_checked = ( true === isset( $options[ $type . 'release_channel'] ) &&
			 $options[ $type . 'release_channel' ] == "edge" ) ? 'checked' : '';

		$channel_options[] = '<input type="radio" id="' . $type .
			'release_channel_edge" name="boldgrid_settings[' . $type . 'release_channel]" value="edge" ' .
			 $edge_checked . '/> Edge';

		// CANDIDATE.
		$candidate_checked = ( true === isset( $options[ $type . 'release_channel' ] ) &&
			 'candidate' === $options[ $type . 'release_channel' ] ) ? 'checked' : '';

		// Only display candidate if it is checked or true == $show_all_channels.
		if ( 'checked' === $candidate_checked || true === $show_all_channels ) {
			$channel_options[] = '<input type="radio" id="' . $type . 'release_channel_candidate" name="boldgrid_settings['
				. $type . 'release_channel]" value="candidate" ' .
				 $candidate_checked . ' /> Candidate';
		}

		echo implode( $channel_options, ' &nbsp; ' );
	}

	/**
	 * Display the options page setting for Plugin Auto-Updates.
	 *
	 * @since 1.1.8
	 *
	 * @return null
	 */
	public function plugin_autoupdate_text() {
		// If a multisite and not on blog id 1, then show a message with a link to go there.
		if ( true === is_multisite() && 1 !== get_current_blog_id() ) {
			?>
			This setting must be set in your primary blog.  Click
			<a href='<?php echo get_admin_url( 1, '/options-general.php?page=boldgrid-settings' ); ?>'>
			here</a> to go the BoldGrid Settings page in your primary blog.
			<?php

			return;
		}

		// Retrieve the WP option boldgrid_settings.
		$options = get_option( 'boldgrid_settings' );

		$enabled = ( false === empty( $options['plugin_autoupdate'] ) ? 'checked' : '' );

		$disabled = ( '' === $enabled ? 'checked' : '' );

		echo '<input type="radio" id="plugin_autoupdate_enabled"
		 	name="boldgrid_settings[plugin_autoupdate]" value="1"' . $enabled . ' /> Enabled &nbsp;
		 	<input type="radio" id="plugin_autoupdate_enabled"
		 	name="boldgrid_settings[plugin_autoupdate]" value="0"' . $disabled . ' /> Disabled';

		return;
	}

	/**
	 * Display the options page setting for Theme Auto-Updates.
	 *
	 * @since 1.1.8
	 *
	 * @return null
	 */
	public function theme_autoupdate_text() {
		// If a multisite and not on blog id 1, then show a message with a link to go there.
		if ( true === is_multisite() && 1 !== get_current_blog_id() ) {
			?>
			This setting must be set in your primary blog.  Click
			<a href='<?php echo get_admin_url( 1, '/options-general.php?page=boldgrid-settings' ); ?>'>
			here</a> to go the BoldGrid Settings page in your primary blog.
			<?php

			return;
		}

		// Retrieve the WP option boldgrid_settings.
		$options = get_option( 'boldgrid_settings' );

		$enabled = ( false === empty( $options['theme_autoupdate'] ) ? 'checked' : '' );

		$disabled = ( '' === $enabled ? 'checked' : '' );

		echo '<input type="radio" id="theme_autoupdate_enabled"
		 	name="boldgrid_settings[theme_autoupdate]" value="1"' . $enabled . ' /> Enabled &nbsp;
		 	<input type="radio" id="theme_autoupdate_enabled"
		 	name="boldgrid_settings[theme_autoupdate]" value="0"' . $disabled . ' /> Disabled';

		return;
	}

	/**
	 * callback for menu reordering
	 */
	public function boldgrid_menu_callback() {
		$options = get_option( 'boldgrid_settings' );

		?>
<input type="checkbox" id="boldgrid_menu_option"
	name="boldgrid_settings[boldgrid_menu_option]" value="1"
	<?php
		echo checked( 1, ( bool ) $options['boldgrid_menu_option'], false );
		?> />
<label for="boldgrid_menu_option"><?php echo __( 'Use BoldGrid Admin Menu system' ); ?></label>
<?php
	}

	/**
	 * BoldGrid feedback out-out callback.
	 *
	 * @since 1.0.9
	 *
	 */
	public function boldgrid_feedback_optout_callback() {
		$options = get_option( 'boldgrid_settings' );

		?>
<input type="checkbox" id="boldgrid-feedback-optout"
	name="boldgrid_settings[boldgrid_feedback_optout]" value="1"
	<?php
		echo checked( 1, false === empty( $options['boldgrid_feedback_optout'] ), false );
		?> />
<label for="boldgrid_menu_option"><?php echo __( 'Opt-out of feedback' ); ?></label>
<?php
	}

	/**
	 * Validate the submitted options.
	 *
	 * @param array $boldgrid_settings An array of boldgrid settings.
	 * @return array A validated array of boldgrid settings.
	 */
	public function boldgrid_options_validate( $boldgrid_settings ) {
		// Menu reordering.
		$new_boldgrid_settings['boldgrid_menu_option'] = ( ( isset(
			$boldgrid_settings['boldgrid_menu_option'] ) &&
			 1 == $boldgrid_settings['boldgrid_menu_option'] ) ? 1 : 0 );

		// Feedback opt-out.
		$new_boldgrid_settings['boldgrid_feedback_optout'] = ( ( isset(
			$boldgrid_settings['boldgrid_feedback_optout'] ) &&
			 1 == $boldgrid_settings['boldgrid_feedback_optout'] ) ? 1 : 0 );

		// Release version to use.
		$new_boldgrid_settings['release_channel'] = isset( $boldgrid_settings['release_channel'] ) ? $boldgrid_settings['release_channel'] : 'stable';
		$new_boldgrid_settings['theme_release_channel'] = isset( $boldgrid_settings['theme_release_channel'] ) ? $boldgrid_settings['theme_release_channel'] : 'stable';

		// Plugin auto-updates.
		$new_boldgrid_settings['plugin_autoupdate'] = ( false === empty( $boldgrid_settings['plugin_autoupdate'] ) ? 1 : 0 );

		// Theme auto-updates.
		$new_boldgrid_settings['theme_autoupdate'] = ( false === empty( $boldgrid_settings['theme_autoupdate'] ) ? 1 : 0 );

		// Delete the transient holding the cached version data.
		if ( is_multisite() ) {
			delete_site_transient( 'boldgrid_api_data' );
		} else {
			delete_transient( 'boldgrid_api_data' );
		}

		// Return the new validated settings.
		return $new_boldgrid_settings;
	}

	/**
	 * Print BoldGrid settings
	 */
	public function print_section_boldgrid_settings() {
		?>
<h2>BoldGrid Settings</h2>
<form action="options.php" method="post">
		<?php
		settings_fields( 'boldgrid_options' );

		do_settings_sections( 'boldgrid-settings' );

		submit_button( __( 'Save Changes' ), 'secondary' );
		?>
	</form>
<hr />
<?php
	}

	/**
	 * Print the Pointers section
	 */
	public function print_section_to_reset_pointers() {
		// Reset "read" pointers
		if ( isset( $_POST['reset_pointers'] ) and 'true' == $_POST['reset_pointers'] ) {
			$this->reset_pointers();
		}

		?>
<h3>Pointers</h3>
<form method="post">
<?php wp_nonce_field( 'reset_pointers' ); ?>
	<p>
		<input type="checkbox" id="reset_pointers" name="reset_pointers"
			value="true"> Reset Pointers and Admin Notices (help messages)
	</p>
	<p>
		<?php submit_button( __('Reset Pointers'), 'secondary' ); ?>
	</p>
</form>
<hr />
<?php
	}

	/**
	 * Print the Start Over section
	 */
	public function print_section_to_start_over() {
		include BOLDGRID_BASE_DIR . '/pages/includes/boldgrid-settings/start_over.php';
	}

	/**
	 * Removed boldgrid_ admin pointers from dismissed_wp_pointers
	 */
	public function reset_pointers() {
		if ( ! isset( $_POST['_wpnonce'] ) ||
			 ! wp_verify_nonce( $_POST['_wpnonce'], 'reset_pointers' ) ) {
			// nonce not verified; print an error message and return false:
			?>
<div class="error">
	<p>Error processing request to reset pointers (help messages);
		WordPress security violation! Please try again.</p>
</div>
<?php
		} else {
			// clear all the pointers
			update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', '' );

			// clear all admin notices
			delete_option( 'boldgrid_dismissed_admin_notices' );
		}
	}

	/**
	 * Is the BoldGrid-Staging plugin installed?
	 */
	public function set_staging_installed() {
		$staging_plugin_path = 'boldgrid-staging/boldgrid-staging.php';

		$plugin_list = get_plugins();

		$plugin_found = false;

		foreach ( $plugin_list as $plugin_path => $plugin_array_data ) {
			if ( $staging_plugin_path == $plugin_path ) {
				$plugin_found = true;
				break;
			}
		}

		$this->staging_installed = $plugin_found;
	}

	/**
	 * Execute the cleanup scripts needed to 'start over'
	 */
	public function start_over() {
		// Delete any BoldGrid Forms and Entries Installed
		$this->cleanup_boldgrid_forms();

		// Delete pages
		$this->cleanup_pages_and_menus();

		// Delete nav menus
		$this->cleanup_nav_menus();

		// Reset boldgrid framework
		$this->reset_framework();

		// Update / delete several boldgrid_ options
		$this->cleanup_wp_options();

		// Delete theme_mods_{$theme_name} options:
		$this->cleanup_theme_mods();

		// Delete all BoldGrid themes
		$this->cleanup_boldgrid_themes();
	}

	/**
	 * Reset Framework
	 */
	public function reset_framework() {
		// Reset Boldgrid Theme Framework
		if( $this->start_over_active ) {
			do_action( 'boldgrid_framework_reset', true );
		}
		if( $this->start_over_staging ) {
			do_action( 'boldgrid_framework_reset', false );
		}

		// Make sure option is reset if theme not active
		delete_option( 'boldgrid_framework_init' );
	}

	/**
	 * Cleanup BoldGrid forms and entries that might have been generated from the install.
	 * If BoldGrid Forms is an installed and active plugin, we will find all of the forms
	 * by ID, then for each ID found, it will be deleted. After dropping all the tables
	 * a lot of errors occur, so then we remove most all of the options, minus some of the
	 * unique keys that would be needed for it to run or have the same config for the user
	 * that they had before. Once activated, the three default forms we include are there.
	 *
	 * NO FILTER AVAILABLE FOR ACTIVE / STAGING SITE.
	 *
	 * @since .21
	 */
	protected function cleanup_boldgrid_forms() {
		// If user has selected the box to delete BoldGrid Forms, then delete them.
		if ( true === $this->delete_forms ) {
			global $boldgrid_forms;
			$boldgrid_forms['force_uninstall'] = true;

			$plugin = 'boldgrid-ninja-forms/ninja-forms.php';
			deactivate_plugins( $plugin );
			uninstall_plugin( $plugin );
			update_option( 'recently_activated',
				array (
					$plugin => time()
				) + ( array ) get_option( 'recently_activated' ) );
		}
	}

	/**
	 * Cleanup BoldGrid themes that might have been generated from the install.
	 *
	 * NO FILTER FOR ACTIVE / STAGING.
	 *
	 * @since .21
	 *
	 * @param int $_POST['boldgrid_delete_themes']
	 */
	protected function cleanup_boldgrid_themes() {
		// This will provide an array with all of the themes installed
		$themes = wp_get_themes( array (
			'errors' => null
		) );

		if ( true === $this->delete_themes ) {
			// If the user's current theme is a BoldGrid theme, let's switch the user to
			// twentyfifteen.
			if ( Boldgrid_Inspirations_Utility::startsWith( get_stylesheet(), 'boldgrid' ) ) {
				switch_theme( 'twentyfifteen' );
			}

			// Grab each theme, and see if it has $stylesheet (folder name theme is contained in)
			// with "boldgrid" in the name.
			if ( count( $themes ) ) {
				foreach ( $themes as $theme_key => $theme ) {
					if ( $theme->exists() && false !== stristr( $theme_key, 'boldgrid' ) ) {
						// If it does, then delete the theme.
						delete_theme( $theme_key );
					}
				}
			}
		}
	}

	/**
	 * Cleanup theme_mods_boldgrid in WP Options.
	 *
	 * Originally, we got a list of all the themes using wp_get_themes(). If the theme name began
	 * with boldgrid, then we would delete the theme mods. One problem here is that when you delete
	 * a theme, WordPress does not delete the theme mods. So, our call to wp_get_themes() would not
	 * get all of the theme_mods we actually want to delete.
	 *
	 * Instead, we'll use SQL to query for all theme_mods_boldgrid% options, and then delete those.
	 */
	protected function cleanup_theme_mods() {
		// Active site:
		if ( true == $this->start_over_active ) {
			$boldgrid_theme_mods = $this->get_option_names_starting_with( 'theme_mods_boldgrid' );

			if ( $boldgrid_theme_mods ) {
				foreach ( $boldgrid_theme_mods as $option_to_delete ) {
					// Delete all options and set an option which will reset the themes color
					// palette
					update_option( $option_to_delete,
						array (
							'force_scss_recompile' => array (
								'active' => true,
								'staging' => true
							)
						) );
				}
			}
		}

		// Staging site:
		if ( true == $this->start_over_staging ) {
			$boldgrid_theme_mods = $this->get_option_names_starting_with(
				'boldgrid_staging_theme_mods_boldgrid' );

			if ( $boldgrid_theme_mods ) {
				foreach ( $boldgrid_theme_mods as $option_to_delete ) {
					delete_option( $option_to_delete );
				}
			}
		}
	}

	/**
	 * Determine if a user has used BoldGrid to publish a site.
	 *
	 * @return boolean
	 */
	public function user_has_built_a_boldgrid_site() {
		return ( 'yes' == get_option( 'boldgrid_has_built_site' ) ||
			 'yes' == get_option( 'boldgrid_staging_boldgrid_has_built_site' ) );
	}

	/**
	 * Determine if a user wants to start over, and a nonce is verified
	 *
	 * @param string $_POST['start_over']
	 * @param string $_POST['_wpnonce']
	 */
	public function user_wants_to_start_over() {
		if ( isset( $_POST['start_over'] ) && 'Y' == $_POST['start_over'] ) {
			if ( ! isset( $_POST['_wpnonce'] ) ||
				 ! wp_verify_nonce( $_POST['_wpnonce'], 'start_over' ) ) {
				// nonce not verified; print an error message and return false:
				?>
<div class="error">
	<p>Error processing request to start over; WordPress security
		violation! Please try again.</p>
</div>
<?php
				return false;
			} else {
				// Clear to proceed; return true:
				return true;
			}
		} else {
			return false;
		}
	}
}
