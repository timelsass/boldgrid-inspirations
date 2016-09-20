<?php
// Prevent direct calls.
require BOLDGRID_BASE_DIR . '/pages/templates/restrict-direct-access.php';

/**
 * ****************************************************************************
 * 1: Generate the "Your new site is now installed" message.
 * 2: Set any applicable cookies.
 * ****************************************************************************
 */
$url_to_customizer = 'customize.php?return=' . get_dashboard_url();

// If we don't have the staging plugin installed:
if ( ! is_plugin_active( 'boldgrid-staging/boldgrid-staging.php' ) ) {
	$template = 'Your new BoldGrid site is now installed and <a href="%s" target="_blank">ready to view</a>.';
	$your_new_site_is_now_installed_message = sprintf( $template, get_site_url() );

	$_SESSION['wp_staging_view_version'] = 'production';
} else {
	// If installed to your active site:
	if ( false == $this->is_staging_install() ) {
		$site_type = 'Active';

		$_SESSION['wp_staging_view_version'] = 'production';
	} else {
		// If installed to your staging site:
		$site_type = 'Staging';

		$_SESSION['wp_staging_view_version'] = 'staging';
		$url_to_customizer = "customize.php?staging=1";
	}

	$template = 'Your new BoldGrid site has installed as your <strong>%s</strong> site, and is <a href="%s" target="_blank">ready to view</a>.';
	$your_new_site_is_now_installed_message = sprintf( $template, $site_type, get_site_url() );
}

?>

<div class='wrap hidden' id='stop_and_explain'>
	<div class='plugin-card no-float'>
		<div class='plugin-card-top '>
			<?php echo $this->get_plugin_activation_notices(); ?>
			<h3>Congratulations!</h3>

			<p><?php echo $your_new_site_is_now_installed_message; ?></p>

			<p>Next, we will move on to Phase 2 - Customization. This is the
				stage where you will make the site your own. For first time
				webmasters or if you are limited on time, we recommend starting
				small. BoldGrid will help you build up skills in running your site.
				As you progress, you will know more about what you want to
				accomplish and how to do it.</p>
		</div>
		<div class='plugin-card-bottom'>
			<div class='column-updated'>
				<a href='<?php  echo $url_to_customizer; ?>' class='button button-primary'>Customize</a>
			</div>
		</div>
	</div>

	<hr />
</div>

<?php

// Render 'stop and explain' message.
Boldgrid_Inspirations_Utility::inline_js_file( 'deploy_stop_and_explain.js' );

?>