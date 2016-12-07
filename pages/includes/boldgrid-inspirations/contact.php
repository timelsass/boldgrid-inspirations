<?php
$lang = array(
	'Add_a_map' => __( 'Add a map', 'boldgrid-inspirations' ),
	'Address' => __( 'Address', 'boldgrid-inspirations' ),
	'Back' => __( 'Back', 'boldgrid-inspirations' ),
	'Company_name' => __( 'Company name / site title', 'boldgrid-inspirations' ),
	'Do_not_display' => __( 'Do not display', 'boldgrid-inspirations' ),
	'Email' => __( 'Email', 'boldgrid-inspirations' ),
	'Intro' => __( 'The information you provide below will be used to populate contact information and social media icons throughout your BoldGrid website.', 'boldgrid-inspirations' ),
	'Next' => __( 'Next', 'boldgrid-inspirations' ),
	'Phone' => __( 'Phone', 'boldgrid-inspirations' ),
	'Social_media' => __( 'Social Media', 'boldgrid-inspirations' ),
);

$social_media = array(
	'facebook' => 'facebook.com/username',
	'twitter' => 'twitter.com/username',
	'google-plus' => 'plus.google.com/username',
	'linkedin-square' => 'linkedin.com/username',
	'youtube' => 'youtube.com/username',
	'instagram' => 'instagram.com/username',
	'plus' => 'Custom url',
);

$social_media_index = '<div id="social-media-index">';
foreach( $social_media as $key => $url ) {
	$social_media_index .= sprintf(
		'<span data-icon="%1$s" data-sample-url="%2$s"><i class="fa fa-%1$s" aria-hidden="true"></i></span>',
		$key,
		$url
	);
}
$social_media_index .= '</div>';

$blogname = get_option( 'blogname' );
?>


<div class="boldgrid-plugin-card">
	<div class="top">

		<p><?php echo $lang['Intro']; ?></p>

		<div class='survey-field'>
			<span class='title'><?php echo $lang['Company_name']; ?></span>
			<input class='main-input' type='text' name="survey[blogname][value]" value="<?php echo esc_attr( $blogname ); ?>" />
		</div>

		<div class='survey-field'>
			<span class='title'><?php echo $lang['Email']; ?></span>
			<div class='option'><?php echo $lang['Do_not_display']; ?> <input type="checkbox" name="survey[email][do-not-display]" /></div>
			<input class='main-input' type='text' name="survey[email][value]" value="<?php echo esc_attr( $user_email ); ?>" />
		</div>

		<div class='survey-field'>
			<span class='title'><?php echo $lang['Phone']; ?></span>
			<div class='option'><?php echo $lang['Do_not_display']; ?> <input type="checkbox" name="survey[phone][do-not-display]" /></div>
			<input class='main-input' type='text' name="survey[phone][value]" value="777-765-4321" />
		</div>

		<div class='survey-field'>
			<span class='title'><?php echo $lang['Address']; ?></span>
			<div class='option'><?php echo $lang['Do_not_display']; ?> <input type="checkbox" name="survey[address][do-not-display]" /></div>
			<input class='main-input' type='text' name="survey[address][value]" value="1234 Your St, City, STATE, 12345" />
			<div class='add-a-map'><input type="checkbox" name="survey[map]" /><?php echo $lang['Add_a_map']; ?></div>
		</div>

		<div class='survey-field' id='social-media'>
			<span class='title'><?php echo $lang['Social_media']; ?></span>
			<div class='option'><?php echo $lang['Do_not_display']; ?> <input type="checkbox" name="survey[social][do-not-display]" /></div>
		</div>

		<?php echo $social_media_index; ?>

	</div>
	<div class="bottom">
		<a class="button button-secondary"><?php echo $lang['Back']; ?></a>
		<a class="button button-primary"><?php echo $lang['Next']; ?></a>
	</div>
</div>