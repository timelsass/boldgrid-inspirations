<?php

/*
 * Configure varialbes
 */
$lang = array(
	'standard' =>					__(	'With BoldGrid, when creating a new site, you start with <strong class="dashicons dashicons-inline dashicons-lightbulb">Inspirations</strong> then you <strong class="dashicons dashicons-inline dashicons-admin-customize">Customize</strong> to your particular situation. You are starting Inspirations now so you will explore different website designs, pages you may want in your site, content that is specific to your industry, and functionality you may need. <strong>Don\'t worry if you are not sure, you can always return to Inspirations to get different pages or designs, or even start completely over</strong>.', 'boldgrid-inspirations' ),
	'you_have_staging' =>			__( 'Your BoldGrid install has "Staging". Staging allows you to have an "Active Site" (a site publicly available to your visitors) while also working on a new site in Staging.', 'boldgrid-inspirations' ),
	'specific' =>					__( 'The following is specific to you.', 'boldgrid-inspirations' ),
	'staging-yes-active-no' =>		__( 'We\'ve noticed that you have installed a Staging Site and that you are running a default Active Site. If you want a different theme or additional pages for your Staging Site, go to <a href="admin.php?page=boldgrid-inspirations&boldgrid-tab=themes"><strong>Install New Themes</strong></a> or <a href="edit.php?post_type=page&page=boldgrid-add-gridblock-sets"><strong>Add New Pages</strong></a>. If you are just experimenting, you may want to look at <a href="admin.php?page=boldgrid-tutorials"><strong>how to Start Over</strong></a>. You can also choose to just install a new site as the Active site. <strong>Please select an install location to continue</strong>.', 'boldgrid-inspirations' ),
	'staging-no-active-yes' =>		__( 'We\'ve noticed that you have an Active Site. If you want a different theme or additional pages for your Active Site, go to <a href="admin.php?page=boldgrid-inspirations&boldgrid-tab=themes"><strong>Install New Themes</strong></a> or <a href="edit.php?post_type=page&page=boldgrid-add-gridblock-sets"><strong>Add New Pages</strong></a>. If you are working on a new site or working on a significant change in your existing site, we recommend you use Staging. <strong>Please select an install location to continue</strong>.', 'boldgrid-inspirations' ),
	'choice-1' =>					__( 'We\'ve noticed that you have not installed a site of your own and are running a default site.', 'boldgrid-inspirations' ),
	'choice-2' =>					__( 'In this case, you can choose to leave the default site running while you work on your new site in Staging or simply install your new site as Active. Installing to Active requires less steps and is faster so we generally recommend that choice. <strong>Please select an install location to continue</strong>.', 'boldgrid-inspirations' ),
	'install_as_staging' =>			__(	'Install as Staging', 'boldgrid-inspirations' ),
	'install_as_active' =>			__(	'Install as Active', 'boldgrid-inspirations' ),
	'new_theme_for_active' =>		__( 'New Theme for Active', 'boldgrid-inspirations' ),
	'new_theme_for_staging' =>		__( 'New Theme for Staging', 'boldgrid-inspirations' ),
	'install_new_staging_site' =>	__( 'Install New Staging Site', 'boldgrid-inspirations' ),
	'install_new_active_site' =>	__( 'Install New Active Site', 'boldgrid-inspirations' ),
	// Pre install modal.
	'install_your_new_website' =>	__( 'Install your new website!', 'boldgrid-inspirations' ),
);

/*
 * Define our templates.
 *
 * These templates will be used within printf calls, which will also pass in specific $lang values
 * defined above.
 */
$templates = array(
	'plugin-card' => '	<div class="wrap">
							<div id="select-install-type">
								<div class="plugin-card col-xs-12 col-sm-8 col-md-8 col-lg-6">
									<div class="plugin-card-top">%s</div>
									<div class="plugin-card-bottom">
										<div class="column-updated">%s</div>
									</div>
								</div>
							</div>
						</div>',
	'buttons' => array(
		'active_or_staging' => '	<a class="button button-secondary" data-install-type="staging">%s</a>
									<a class="button button-primary" data-install-type="active">%s</a>',
		'install_to_staging' => '	<a class="button button-secondary" href="admin.php?page=boldgrid-inspirations&boldgrid-tab=themes">%s</a>
									<a class="button button-primary" data-install-type="staging">%s</a>',
		'install_to_active' => '	<a class="button button-secondary" href="admin.php?page=boldgrid-inspirations&boldgrid-tab=themes">%s</a>
									<a class="button button-primary" data-install-type="active">%s</a>',
	)
);



// Grab our mode data and determine which scenario we're in.
$scenarios = $this->mode_data;
$scenario = null;

switch( $scenarios['mode'] ) {
	case 'standard':
		if( empty( $scenarios['has_active_site'] ) && empty( $scenarios['has_staged_site'] ) ) {
			$scenario = 'choice';
		}elseif( 'stage' === $scenarios['install_destination'] ) {
			$scenario = 'install_to_staging';
		}elseif( 'active' === $scenarios['install_destination'] ) {
			$scenario = 'install_to_active';
		}
		break;
}

// Create our top / bottom messages.
switch( $scenario ) {
	case 'choice':
		$top = '	<p>' . $lang['standard'] . '</p>
					<p>' . $lang['you_have_staging'] . ' ' . $lang['choice-1'] . '</p>
					<p>' . $lang['choice-2'] . '</p>';
		$bottom = sprintf( $templates['buttons']['active_or_staging'], $lang['install_as_staging'], $lang['install_as_active'] );
		break;
	case 'install_to_staging':
		$top = '	<p>' . $lang['standard'] . '</p>
					<p>' . $lang['staging-no-active-yes'] . '</p>';
		$bottom = sprintf( $templates['buttons']['install_to_staging'], $lang['new_theme_for_active'], $lang['install_new_staging_site'] );
		break;
	case 'install_to_active':
		$top = '	<p>' . $lang['standard'] . '</p>
					<p>' . $lang['staging-yes-active-no'] . '</p>';
		$bottom = sprintf( $templates['buttons']['install_to_active'], $lang['new_theme_for_staging'], $lang['install_new_active_site'] );
		break;
}

// Print.
printf( $templates['plugin-card'], $top, $bottom );

?>