<?php

$template = '
	<div class="wrap confirmation hidden">
		<div style="border: 1px solid #dfdfdf; width:100%%; max-width:600px;">
			<div class="top" style="background:#fff; padding:15px;">
				%s
			</div>
			<div id="select-install-type" class="bottom" style="background:#fafafa;padding:15px;text-align:right;border-top:1px solid #dfdfdf;">
				%s
			</div>
		</div>
	</div>
';

$congrats = __('<strong>Congratulations</strong>, you\'ve completed the first two steps!','boldgrid-inspirations');
$need_to_install = __('Before you can add your own personal touches to your <span id="install-modal-destination"></span> website, we\'ll first need to install your new website for you. After installation, you can add your own images, change text, etc.','boldgrid-inspirations');
$ready_to_install = '<p>' . __('Are you ready to install this website?','boldgrid-inspirations') . '</p>';

$detected_staging = __( 'We\'ve detected that you have Staging Installed. Staging allows you to maintain your "Active Site" (publically visible) while you work on a staged site behind the scenes. We recommend that you use Staging only after you have built your first BoldGrid website and are needing to make lots of changes.', 'boldgrid-inspirations' );

$standard_intro = '<p>' . $congrats . '</p><p>' . $need_to_install . '</p>';

$overwrite_active = '<p>' . __( 'We\'ve detected that you have an existing website. Installing a new site now will move all of your exisiting pages to the trash.', 'boldgrid-inspirations' ) . '</p>';

$bottom = 	'<button class="go-back button button-secondary">' . __('Go back','boldgrid-inspirations') . '</button>
			<button class="button button-primary install-this-website" data-start-over="true" >' . __('Install this website!','boldgrid-inspirations') . '</button>';

// Generate an array of scenario data. This will be used in the switch statement immediately below.
$scenario = array(
	$mode_data['has_blank_active_site'],
	$mode_data['has_active_bg_site'],
	// $mode_data['has_staged_site'],
	// $mode_data['staging_active'],
);

switch( $scenario ) {
	/*
	 * [T] has_blank_active_site
	 * [ ] has_active_bg_site
	 */
	case array( true, false ):
		$top = $standard_intro . $ready_to_install;
		break;

	/*
	 * [ ] has_blank_active_site
	 * [T] has_active_bg_site
	 */
	case array( false, true ):
		$top = $standard_intro . $overwrite_active . $ready_to_install;
		break;

	/*
	 * [ ] has_blank_active_site
	 * [ ] has_active_bg_site
	 */
	case array( false, false ):
		$top = $standard_intro . $overwrite_active . $ready_to_install;
		break;
}

// printf( $template, $top, print_r($mode_data,1), $bottom );
printf( $template, $top, $bottom );

?>