<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Options
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * THe BoldGrid Inspirations Options class.
 */
class Boldgrid_Inspirations_Options {
	/**
	 * Is the staging plugin installed?
	 *
	 * @var bool
	 */
	public $staging_installed = false;

	/**
	 * Get install options.
	 *
	 * @return mixed
	 */
	public static function get_install_options() {
		$install_options = get_option( 'boldgrid_install_options' );

		return $install_options;
	}

	/**
	 * Add hooks.
	 */
	public function add_hooks() {
		if ( is_admin() ) {
			// Javascript.
			add_action( 'admin_enqueue_scripts',
				array(
					$this,
					'enqueue_boldgrid_options_js',
				)
			);

			// Options Submenu Node.
			add_action( 'admin_menu',
				array(
					$this,
					'boldgrid_admin_add_options_submenu',
				)
			);

			// Options Page Init.
			add_action( 'admin_init',
				array(
					$this,
					'boldgrid_admin_init',
				)
			);
		}
	}

	/**
	 * Enqueue the JS file that controls our agreement checkbox, and
	 * warns a user that they will be deleting stuff if they check
	 * stuff in this section.
	 *
	 * @since 0.21
	 */
	public function enqueue_boldgrid_options_js( $hook ) {
		if ( 'settings_page_boldgrid-settings' === $hook ) {
			wp_enqueue_script(
				'boldgrid-options',
				plugins_url(
					'/assets/js/boldgrid-options.js',
					BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php'
				),
				array(),
				'',
				true
			);
		}
	}

	/**
	 * Function hook to add the BoldGrid Settings submenu.
	 */
	public function boldgrid_admin_add_options_submenu() {
		add_submenu_page(
			'options-general.php',
			'BoldGrid Settings',
			'BoldGrid',
			'administrator',
			'boldgrid-settings',
			array(
				$this,
				'boldgrid_options_page',
			)
		);
	}

	/**
	 * Options page.
	 *
	 * @see Boldgrid_Inspirations_Options::user_wants_to_start_over().
	 * @see Boldgrid_Inspirations_Options::print_section_boldgrid_settings().
	 * @see Boldgrid_Inspirations_Options::print_section_to_reset_pointers().
	 * @see Boldgrid_Inspirations_Options::print_section_to_start_over().
	 */
	public function boldgrid_options_page() {
		// If the user wants to start over, go ahead and delete everything.
		if ( $this->user_wants_to_start_over() ) {
			$this->start_over();
		}

		// "Wrap" the page so that it has nice margins.
		?>
		<div class='wrap'>
		<?php

		$this->print_section_boldgrid_settings();

		$this->print_section_to_reset_pointers();

		$this->print_section_to_start_over();

		?>
		</div>
		<?php
	}

	/**
	 * Admin init function for the options page.
	 */
	public function boldgrid_admin_init() {
		// Is the staging plugin installed?
		$this->set_staging_installed();
	}

	/**
	 * Display the options page body.
	 */
	public function boldgrid_options_global_text() {
		?>
		Here you may change the BoldGrid plugin global settings.<br />
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
	 *
	 * @see Boldgrid_Inspirations_Options::get_boldgrid_settings()
	 *
	 * @param string $type Release channel option type.
	 */
	public function boldgrid_option_select_release_channel_text( $type = '' ) {
		// Get boldgrid_settings.
		$boldgrid_settings = Boldgrid_Inspirations_Options::get_boldgrid_settings();

		// Should we show the candidate option?
		$show_all_channels = ( isset( $_GET['channels'] ) && 'all' === $_GET['channels'] );

		/*
		 * Print the radio buttons for stage, edge, and candidate (if applicable).
		 *
		 * 1: Create an array $channel_options of radio options.
		 * 2: Use implode to display the array of radio options.
		 */

		// STABLE.
		$stable_checked = ( ! isset( $boldgrid_settings[ $type . 'release_channel' ] ) ||
			 ( isset( $boldgrid_settings[ $type . 'release_channel'] ) && 'stable' == $boldgrid_settings[ $type . 'release_channel'] ) ) ? 'checked' : '';

		$channel_options[] = '<input type="radio" id="' . $type .
			'release_channel_stable" name="boldgrid_settings[' . $type . 'release_channel]" value="stable" ' .
			 $stable_checked . ' /> Stable';

		// EDGE.
		$edge_checked = ( isset( $boldgrid_settings[ $type . 'release_channel'] ) &&
			 $boldgrid_settings[ $type . 'release_channel' ] == "edge" ) ? 'checked' : '';

		$channel_options[] = '<input type="radio" id="' . $type .
			'release_channel_edge" name="boldgrid_settings[' . $type . 'release_channel]" value="edge" ' .
			 $edge_checked . '/> Edge';

		// CANDIDATE.
		$candidate_checked = ( isset( $boldgrid_settings[ $type . 'release_channel' ] ) &&
			 'candidate' === $boldgrid_settings[ $type . 'release_channel' ] ) ? 'checked' : '';

		// Only display candidate if it is checked or true == $show_all_channels.
		if ( 'checked' === $candidate_checked || $show_all_channels ) {
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
	 */
	public function plugin_autoupdate_text() {
		// Retrieve the WP option boldgrid_settings.
		$options = Boldgrid_Inspirations_Options::get_boldgrid_settings();

		$enabled = ( ! empty( $options['plugin_autoupdate'] ) ? ' checked' : '' );

		$disabled = ( empty( $enabled ) ? ' checked' : '' );

		?>
<input type="radio" id="plugin_autoupdate_enabled"
name="boldgrid_settings[plugin_autoupdate]" value="1"<?php echo $enabled; ?> /> Enabled
 &nbsp;
<input type="radio" id="plugin_autoupdate_enabled"
name="boldgrid_settings[plugin_autoupdate]" value="0"<?php echo $disabled; ?> /> Disabled
		<?php
	}

	/**
	 * Display the options page setting for Theme Auto-Updates.
	 *
	 * @since 1.1.8
	 *
	 * @see Boldgrid_Inspirations_Options::get_boldgrid_settings()
	 */
	public function theme_autoupdate_text() {
		// Retrieve the WP option boldgrid_settings.
		$options = Boldgrid_Inspirations_Options::get_boldgrid_settings();

		$enabled = ( ! empty( $options['theme_autoupdate'] ) ? ' checked' : '' );

		$disabled = ( '' === $enabled ? ' checked' : '' );

		?>
<input type="radio" id="theme_autoupdate_enabled"
name="boldgrid_settings[theme_autoupdate]" value="1"<?php echo $enabled; ?> /> Enabled
 &nbsp;
<input type="radio" id="theme_autoupdate_enabled"
name="boldgrid_settings[theme_autoupdate]" value="0"<?php echo $disabled; ?> /> Disabled
		<?php
	}

	/**
	 * Callback for menu reordering.
	 *
	 * @see Boldgrid_Inspirations_Options::get_boldgrid_settings()
	 */
	public function boldgrid_menu_callback() {
		$options = Boldgrid_Inspirations_Options::get_boldgrid_settings();

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
	 * @see Boldgrid_Inspirations_Options::get_boldgrid_settings()
	 */
	public function boldgrid_feedback_optout_callback() {
		$options = Boldgrid_Inspirations_Options::get_boldgrid_settings();

		?>
<input type='checkbox' id='boldgrid-feedback-optout'
name='boldgrid_settings[boldgrid_feedback_optout]' value='1'
		<?php
		echo checked( 1, ! empty( $options['boldgrid_feedback_optout'] ), false );
		?> />
<label for='boldgrid_menu_option'><?php echo __( 'Opt-out of feedback' ); ?></label>
		<?php
	}

	/**
	 * Validate the submitted options.
	 *
	 * @param array $boldgrid_settings An array of BoldGrid settings.
	 * @return array A validated array of BoldGrid settings.
	 */
	public function boldgrid_options_validate( $boldgrid_settings ) {

		$new_boldgrid_settings = Boldgrid_Inspirations_Config::set_default_settings( $boldgrid_settings );

		// Delete the transient holding the cached version data.
		delete_site_transient( 'boldgrid_api_data' );

		// Return the new validated settings.
		return $new_boldgrid_settings;
	}

	/**
	 * Redirect to Inspirations or the dashboard.
	 *
	 * @see Boldgrid_Inspirations_Utility::inline_js_oneliner()
	 */
	public function js_redirect_to_options_page() {
		$url_to_redirect_to = get_site_url() . '/wp-admin/admin.php?page=boldgrid-inspirations';

		// Redirect back to the plugin.
		Boldgrid_Inspirations_Utility::inline_js_oneliner(
			'setTimeout(function() { location = "' . $url_to_redirect_to . '"; }, 100);'
		);
	}

	/**
	 * Print BoldGrid settings.
	 */
	public function print_section_boldgrid_settings() {
		// If updating, then process.
		if ( isset( $_POST['action'] ) && 'update' === $_POST['action'] ) {
			$this->process_boldgrid_settings();
		}
		?>
<h2>BoldGrid Settings</h2>
<form method="post">
<input type="hidden" name="action" value="update">
	<?php
		wp_nonce_field( 'boldgrid_options' );
	?>
	<h2>Global Settings</h2>
	<?php echo $this->boldgrid_options_global_text(); ?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">Plugin Update Channel</th>
				<td><?php echo $this->plugin_channel_text(); ?></td>
			</tr>
			<tr>
				<th scope="row">Plugin Auto-Updates</th>
				<td><?php echo $this->plugin_autoupdate_text(); ?></td>
			</tr>
			<tr>
				<th scope="row">Theme Update Channel</th>
				<td><?php echo $this->theme_channel_text(); ?></td>
			</tr>
			<tr>
				<th scope="row">Theme Auto-Updates</th>
				<td><?php echo $this->theme_autoupdate_text(); ?></td>
			</tr>
			<tr>
				<th scope="row">Reorder Admin Menu</th>
				<td><?php echo $this->boldgrid_menu_callback(); ?></td>
			</tr>
		</tbody>
	</table>
		<?php
		submit_button( esc_html__( 'Save Changes', 'boldgrid-inspirations' ), 'secondary' );
		?>
</form>
<hr />
		<?php
	}

	/**
	 * Process updated BoldGrid settings.
	 *
	 * @since 1.2.6.1
	 * @access private
	 *
	 * @return bool
	 */
	private function process_boldgrid_settings() {
		// If not updating or boldgrid_settings was not passed, then fail.
		if ( ! isset( $_POST['action'] ) || 'update' !== $_POST['action'] ||
			! isset( $_POST['boldgrid_settings'] ) ) {
				return false;
		}

		// Verify nonce "boldgrid_options".
		check_admin_referer( 'boldgrid_options' );

		// Verify capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			include BOLDGRID_BASE_DIR . '/pages/templates/unauthorized-request.php';

			return false;
		}

		// Grab our BoldGrid settings from POST.
		$boldgrid_settings = $_POST['boldgrid_settings'];

		/*
		 * Fix checkbox settings.
		 *
		 * The "Reorder Admin Menu" setting is a checkbox. If left unchecked and submitted, the
		 * 'boldgrid_menu_option' setting will be missing from POST, and the boldgrid_options_validate
		 * method will default it to true / 1.
		 */
		if( ! isset( $boldgrid_settings['boldgrid_menu_option'] ) ) {
			$boldgrid_settings['boldgrid_menu_option'] = 0;
		}

		// Validate settings from form post.
		$boldgrid_settings = $this->boldgrid_options_validate( $boldgrid_settings );

		// Save updated settings.
		update_site_option( 'boldgrid_settings', $boldgrid_settings );

		// If mutlisite, then save the menu option per blog.
		if ( is_multisite() ) {
			$boldgrid_settings_blog['boldgrid_menu_option'] =
			$boldgrid_settings['boldgrid_menu_option'];
			update_option( 'boldgrid_settings', $boldgrid_settings_blog );
		}

		include BOLDGRID_BASE_DIR . '/pages/templates/settings-saved.php';

		return true;
	}

	/**
	 * Print the Reset Pointers section.
	 */
	public function print_section_to_reset_pointers() {
		// Reset "read" pointers
		if ( isset( $_POST['reset_pointers'] ) && 'true' === $_POST['reset_pointers'] ) {
			$this->reset_pointers();
		}

		?>
<h3>Pointers</h3>
<form method='post'>
		<?php wp_nonce_field( 'reset_pointers' ); ?>
	<p>
		<input type='checkbox' id='reset_pointers' name='reset_pointers' value='true' />
		 Reset Pointers and Admin Notices (help messages)
	</p>
	<p>
		<?php submit_button( esc_html__( 'Reset Pointers', 'boldgrid_inspirations' ), 'secondary' ); ?>
	</p>
</form>
<hr />
		<?php
	}

	/**
	 * Print the Start Over section.
	 */
	public function print_section_to_start_over() {
		include BOLDGRID_BASE_DIR . '/pages/includes/boldgrid-settings/start_over.php';
	}

	/**
	 * Removed boldgrid_ admin pointers from dismissed_wp_pointers.
	 */
	public function reset_pointers() {
		if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce( $_POST['_wpnonce'], 'reset_pointers' )
		) {
			// WP nonce not verified; print an error message and return false.
			?>
<div class='error'>
	<p>
		<?php
		esc_html_e(
			'Error processing request to reset pointers (help messages). WordPress security violation! Please try again.',
			'boldgrid-inspirations'
		);
		?>
	</p>
</div>
<?php
		} else {
			// Clear all the pointers.
			update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', '' );

			/*
			 * Clear all admin notices.
			 *
			 * There are 2 calls below. The first call is the original method in which we stored
			 * notices, the second is when we began storing dismissal data per user.
			 */
			delete_option( 'boldgrid_dismissed_admin_notices' );
			delete_metadata ( 'user', 0, 'boldgrid_dismissed_admin_notices', '', true );
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
			if ( $staging_plugin_path === $plugin_path ) {
				$plugin_found = true;
				break;
			}
		}

		$this->staging_installed = $plugin_found;
	}

	/**
	 * Execute the cleanup scripts needed to 'start over'.
	 */
	public function start_over() {
		$start_over = new BoldGrid_Inspirations_Start_over();

		// Configure whether we're starting over with our active site, staging, or both.
		if ( ! is_plugin_active( 'boldgrid-staging/boldgrid-staging.php' ) ) {
			$start_over->start_over_active = true;
			$start_over->start_over_staging = false;
		} else {
			if ( isset( $_POST['start_over_active'] ) && 'start_over_active' === $_POST['start_over_active'] ) {
				$start_over->start_over_active = true;
			}
			if ( isset( $_POST['start_over_staging'] ) && 'start_over_staging' === $_POST['start_over_staging'] ) {
				$start_over->start_over_staging = true;
			}
		}

		// Are we deleting forms?
		$start_over->delete_forms = ( ( isset( $_POST['boldgrid_delete_forms'] ) && 1 == $_POST['boldgrid_delete_forms'] ) ? true : null );

		// Are we deleting pages?
		$start_over->delete_pages = ( isset( $_POST['delete_pages'] ) && 'true' == $_POST['delete_pages'] );

		// Are we deleting themes?
		$start_over->delete_themes = ( isset( $_POST['boldgrid_delete_themes'] ) && 1 == $_POST['boldgrid_delete_themes'] ? true : false );

		$start_over->start_over();

		// Redirect back to the plugin.
		$this->js_redirect_to_options_page();

		exit();
	}

	/**
	 * Determine if a user has used BoldGrid to publish a site.
	 *
	 * @return boolean
	 */
	public function user_has_built_a_boldgrid_site() {
		return (
			'yes' === get_option( 'boldgrid_has_built_site' ) ||
			'yes' === get_option( 'boldgrid_staging_boldgrid_has_built_site' )
		);
	}

	/**
	 * Determine if a user wants to start over, and a nonce is verified
	 *
	 * @param string $_POST['start_over']
	 * @param string $_POST['_wpnonce']
	 * @return bool Whether or not the user wants to start over.
	 */
	public function user_wants_to_start_over() {
		if ( isset( $_POST['start_over'] ) && 'Y' === $_POST['start_over'] ) {
			if ( empty( $_POST['_wpnonce'] ) ||
			! wp_verify_nonce( $_POST['_wpnonce'], 'start_over' )
			) {
				// WP nonce not verified; print an error message and return false.
				?>
<div class='error'>
				<?php
		esc_html_e(
			'Error processing request to start over. WordPress security violation! Please try again.',
			'boldgrid-inspirations'
		);
				?>
</div>
				<?php
				return false;
			} else {
				// Clear to proceed; return true.
				return true;
			}
		}

		return false;
	}

	/**
	 * Get BoldGrid Settings.
	 *
	 * @since 1.2.6
	 *
	 * @static
	 *
	 * @return array BoldGrid Settings array.
	 */
	public static function get_boldgrid_settings() {
		// Get settings from WP site options.
		$boldgrid_settings = get_site_option( 'boldgrid_settings' );

		// Get settings from WP option (blog).
		$boldgrid_settings_blog = get_option( 'boldgrid_settings' );

		// If using multisite and the site option is empty, then use the blog option.
		if ( is_multisite() && empty( $boldgrid_settings ) ) {
			$boldgrid_settings = $boldgrid_settings_blog;
		} else {
			// Include menu setting.
			$boldgrid_settings['boldgrid_menu_option'] = (
				! empty( $boldgrid_settings_blog['boldgrid_menu_option'] ) ?
				1 : 0
			);
		}

		return $boldgrid_settings;
	}
}
