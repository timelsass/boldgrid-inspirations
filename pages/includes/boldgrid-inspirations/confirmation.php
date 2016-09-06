<?php

/**
 * Inspirations Confirmation message
 *
 * This file is intended to produced the confirmation message for the user in the last step of
 * Inspirations.
 *
 * @since 1.2.3
 */

// Language strings.
// @todo: Possibly convert to array after receiving final messages.
$if_overwriting               = __( 'If you choose to overwrite your existing site, your current pages will be moved to the trash', 'boldgrid-inspirations' );
$note                         = __( 'Note', 'boldgrid-inspirations' );
$note_install_staging         = __( 'We will install your new site next to your existing site (this is known as Staging). This also requires the BoldGrid Staging plugin, which we\'ll download and active for you', 'boldgrid-inspirations' );
$need_to_install              = __( 'Before you can add your own personal touches to your <span id="install-modal-destination"></span> website, we\'ll first need to install your new website for you. After installation, you can add your own images, change text, etc.', 'boldgrid-inspirations' );
$detected_staging             = __( 'We\'ve detected that you have Staging Installed. Staging allows you to maintain your "Active Site" (publically visible) while you work on a staged site behind the scenes. We recommend that you use Staging only after you have built your first BoldGrid website and are needing to make lots of changes.', 'boldgrid-inspirations' );
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
$go_back                      = __( 'Go back','boldgrid-inspirations' );
$install_this_website         = __( 'Install this website!','boldgrid-inspirations' );

$bottom = '	<button class="go-back button button-secondary">' . $go_back . '</button>
			<button class="button button-primary install-this-website" data-start-over="true" >' . $install_this_website . '</button>';

$template = '
	<div class="wrap confirmation hidden">
		<div class="boldgrid-plugin-card">
			<div class="top">
				%s
				<p class="note-overwrite">' . $note . ': <em>' . $if_overwriting . '</em>.</p>
				<p class="note-download-staging">' . $note . ': <em>' . $note_install_staging . '</em>.</p>
			</div>
			<div id="select-install-type" class="bottom">
				%s
			</div>
		</div>
	</div>
';


// Generate an array of scenario data. This will be used in the switch statement immediately below.
$scenario = array(
	$mode_data['has_blank_active_site'],
	$mode_data['has_active_bg_site'],
	$mode_data['has_staged_site'],
	$mode_data['staging_active'],
	$mode_data['staging_installed'],
);

/*
 * Create the message for the user in the final step of inspirations.
 *
 * That message is built based upon the user's current scenario. For example, do they have an
 * existing website, do they have the Staging plugin installed, etc.
 */
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

printf( $template, $top, $bottom );

?>