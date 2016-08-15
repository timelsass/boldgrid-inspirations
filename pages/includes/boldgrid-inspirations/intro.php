<?php

$template = '
	<div class="wrap">

		<h1>Inspirations</h1>

		<div style="border: 1px solid #dfdfdf; width:100%%; max-width:600px;">
			<div class="top" style="background:#fff; padding:30px 15px;">%s</div>
			<div id="select-install-type" class="bottom" style="background:#fafafa;padding:15px;text-align:right;border-top:1px solid #dfdfdf;">%s</div>
		</div>
	</div>
';

$standard_intro = __( 'Each BoldGrid website begins its creation process here, within Inspirations. This is where you\'ll explore different website designs, page sets, and content specific to your industry. Don\'t worry if you are not sure, you can always return here to start over.', 'boldgrid-inspirations' );

$detected_staging = __( 'We\'ve detected that you have Staging Installed. Staging allows you to maintain your "Active Site" (publically visible) while you work on a staged site behind the scenes. We recommend that you use Staging only after you have built your first BoldGrid website and are needing to make lots of changes.', 'boldgrid-inspirations' );

// Generate an array of scenario data. This will be used in the switch statement immediately below.
$scenario = array(
	$mode_data['has_blank_active_site'],
	$mode_data['has_active_bg_site'],
	$mode_data['has_staged_site'],
	$mode_data['staging_active'],
);

switch( $scenario ) {
	/*
	 * [T] has_blank_active_site
	 * [ ] has_active_bg_site
	 * [ ] has_staged_site
	 * [T] staging_active
	 */
	case array( true, false, false, true ):
		$top = $standard_intro . '<hr style="margin:15px 0px;" /><h2>' . __( 'Staging your website', 'boldgrid-inspirations' ) . '</h2>' . $detected_staging;
		$bottom = '<a class="button" data-install-type="staging">' . __( 'Install as Staged Site', 'boldgrid-inspirations' ) . '</a> <a class="button button-primary">' . __( 'Install as Active Site', 'boldgrid-inspirations' ) . '</a>';
		break;

	/*
	 * [T] has_blank_active_site
	 * [ ] has_active_bg_site
	 * [ ] has_staged_site
	 * [ ] staging_active
	 */
	case array( true, false, false, false ):
	default:
		$top = $standard_intro;
		$bottom = '<a class="button button-primary">' . __( 'Begin Inspirations', 'boldgrid-inspirations' ) . '</a>';
		break;
}

printf( $template, $top, $bottom );

?>