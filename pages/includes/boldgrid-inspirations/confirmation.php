<?php

$congrats = __('<strong>Congratulations</strong>, we\'re almost ready to install your new site!','boldgrid-inspirations');

$template = '
	<div class="wrap confirmation hidden" data-animated="false">
		<div style="border: 1px solid #dfdfdf; width:100%%; max-width:600px;">
			<div class="top" style="background:#fff; padding:15px;">
				%s
				<p class="note-overwrite" style="color: #aaa;">Note: <em>If you choose to overwrite your existing site, your current pages will be moved to the trash</em>.</p>
				<p class="note-download-staging" style="color: #aaa;">Note: <em>We will install your new site next to your existing site (this is known as Staging). This also requires the BoldGrid Staging plugin, which we\'ll download and active for you</em>.</p>
			</div>
			<div id="select-install-type" class="bottom" style="background:#fafafa;padding:15px;text-align:right;border-top:1px solid #dfdfdf;">
				%s
			</div>
		</div>
	</div>
';

$need_to_install = __('Before you can add your own personal touches to your <span id="install-modal-destination"></span> website, we\'ll first need to install your new website for you. After installation, you can add your own images, change text, etc.','boldgrid-inspirations');

$ready_to_install = '<p>' . __('Are you ready to install this website?','boldgrid-inspirations') . '</p>';

$detected_staging = __( 'We\'ve detected that you have Staging Installed. Staging allows you to maintain your "Active Site" (publically visible) while you work on a staged site behind the scenes. We recommend that you use Staging only after you have built your first BoldGrid website and are needing to make lots of changes.', 'boldgrid-inspirations' );


$bottom = 	'<button class="go-back button button-secondary">' . __('Go back','boldgrid-inspirations') . '</button>
			<button class="button button-primary install-this-website" data-start-over="true" >' . __('Install this website!','boldgrid-inspirations') . '</button>';

$have_both_active_and_staging = __( 'It appears you have both an Active and Staging site. How would you like to install this site?', 'boldgrid-inspirations' );
$have_active_no_staging       = __( 'It appears you already have an existing site. How would you like to install this site?', 'boldgrid-inspirations' );
$install_as_active            = __( 'Install as my Active site.', 'boldgrid-inspirations' );
$install_as_staging           = __( 'Install as my Staging site.', 'boldgrid-inspirations' );
$overwrite_active             = __( 'Overwrite my Active site.', 'boldgrid-inspirations' );
$overwrite_staging            = __( 'Overwrite my Staging site.', 'boldgrid-inspirations' );
$install_where                = __( 'Where would you like to install your new site?', 'boldgrid-inspirations' );
$download_staging             = __( 'Download the BoldGrid Staging plugin and install as my Staging site.', 'boldgrid-inspirations' );
$activate_staging             = __( 'Activate the BoldGrid Staging plugin and install as my Staging site.', 'boldgrid-inspirations' );
$install_next_to_active       = __( 'Install next to my existing site', 'boldgrid-inspirations' );


// Generate an array of scenario data. This will be used in the switch statement immediately below.
$scenario = array(
	$mode_data['has_blank_active_site'],
	$mode_data['has_active_bg_site'],
	$mode_data['has_staged_site'],
	$mode_data['staging_active'],
	$mode_data['staging_installed'],
);

error_log( print_r( $scenario,1));

switch( $scenario ) {
	/*
	 * [T] Has blank active site.
	 * [ ] Has active BG site.
	 * [ ] Has staged site.
	 * [ ] Staging is active.
	 * [ ] Staging is installed.
	 */
	case array( true, false, false, false, false ):
		$top = '<p>' . $need_to_install . '</p>';
		break;
	/*
	 * [T] Has blank active site.
	 * [ ] Has active BG site.
	 * [ ] Has staged site.
	 * [T] Staging is active.
	 * [T] Staging is installed.
	 */
	case array( true, false, false, true, true ):
		$top = '
			<p>' . $install_where . '</p>
			<p>
				<input type="radio" name="install-decision" value="install-as-active" checked>' . $install_as_active . '<br />
				<input type="radio" name="install-decision" value="install-as-staging">' . $install_as_staging . '
			</p>
		';
		break;

	/*
	 * [ ] Has blank active site.
	 * [T] Has active BG site.
	 * [ ] Has staged site.
	 * [T] Staging is active.
	 * [T] Staging is installed.
	 */
	case array( false, true, false, true, true ):
		$top = '
			<p>' . $install_where . '</p>
			<p>
				<input type="radio" name="install-decision" value="overwrite-active" checked>' . $overwrite_active . '<br />
				<input type="radio" name="install-decision" value="install-as-staging">' . $install_as_staging . '
			</p>
		';
		break;

	/*
	 * [ ] Has blank active site.
	 * [T] Has active BG site.
	 * [T] Has staged site.
	 * [T] Staging is active.
	 * [T] Staging is installed.
	 */
	case array( false, true, true, true, true ):
		$top = '
			<p>' . $have_both_active_and_staging . '</p>
			<p>
				<input type="radio" name="install-decision" value="overwrite-active" checked>' . $overwrite_active . '<br />
				<input type="radio" name="install-decision" value="overwrite-staging">' . $overwrite_staging . '
			</p>
		';
		break;

	/*
	 * [ ] Has blank active site.
	 * [T] Has active BG site.
	 * [ ] Has staged site.
	 * [ ] Staging is active.
	 * [ ] Staging is installed.
	 */
	case array( false, true, false, false, false ):
		$top = '
			<p>' . $have_active_no_staging . '</p>
			<p>
				<input type="radio" name="install-decision" value="overwrite-active" checked>' . $overwrite_active . '<br />
				<input type="radio" name="install-decision" value="download-staging">' . $install_next_to_active . '
			</p>
		';
		break;

	/*
	 * [ ] Has blank active site.
	 * [T] Has active BG site.
	 * [ ] Has staged site.
	 * [ ] Staging is active.
	 * [T] Staging is installed.
	 */
	case array( false, true, false, false, true ):
		$top = '
			<p>' . $have_active_no_staging . '</p>
			<p>
				<input type="radio" name="install-decision" value="overwrite-active" checked>' . $overwrite_active . '<br />
				<input type="radio" name="install-decision" value="activate-staging">' . $activate_staging . '
			</p>
		';
		break;

	default:
		$top = '
			<p>' . $have_active_no_staging . '<p>
			<p>
				<input type="radio" name="install-decision" value="overwrite-active" checked>' . $overwrite_active . '<br />
			</p>
		';
		break;
}

// printf( $template, $top, print_r($mode_data,1), $bottom );
printf( $template, $top, $bottom );

?>