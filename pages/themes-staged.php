<?php

// Prevent direct calls
if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

?>

<h2>Staging Theme</h2>

<p>Please choose a theme below to set as your staged theme.</p>

<h3>Currentyly staged theme</h3>

<?php

if ( false == $this->staged_theme ) {
	?>
<p>You do not currently have a staging theme set.</p>
<?php
}
?>

<div class='theme-browser redered'>
	<div class='themes'>

<?php
foreach ( $this->all_themes as $theme ) {
	$template = $theme->template;
	$stylesheet = $theme->stylesheet;
	$theme_name = $theme->get( 'Name' );
	
	/**
	 * if this theme is the current staging theme
	 */
	if ( false != $this->staged_theme and
		 $this->staged_theme->get_stylesheet() == $theme->get_stylesheet() ) {
		$class_active = 'active';
		$span_staged = '<span>Staged:</span> ';
		$stage_button = '';
	} else {
		$class_active = '';
		$span_staged = '';
		$stage_button = '<a class="button button-secondary stage" data-stylesheet=' . $stylesheet .
			 '>Stage</a>';
	}
	?>
		<div
			aria-describedby="<?php echo $template; ?>-action <?php echo $template; ?>-name"
			tabindex="0" class="theme <?php echo $class_active; ?>">

			<div class="theme-screenshot">
				<img
					src="<?php echo $this->themes_dir_uri; ?>/<?php echo $template; ?>/screenshot.png"
					alt="">
			</div>

			<h3 class="theme-name" id="<?php echo $template; ?>-name"><?php echo $span_staged . $theme_name; ?></h3>

			<div class="theme-actions">

				<?php echo $stage_button; ?>

			</div>

		</div>
<?php
}
?>

	</div>
</div>
