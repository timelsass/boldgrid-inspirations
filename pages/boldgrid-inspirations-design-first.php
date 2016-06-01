<?php
// Configure variables.
$lang = array(
	'Design' => __( 'Design', 'boldgrid-inspirations' ),
	'Content' => __( 'Content', 'boldgrid-inspirations' ),
);

?>
<div class='wrap'>

	<p><?php echo $lang['Design'] ?> / <?php echo $lang['Content']; ?></p>

	<div id='screen-design'>

		<div class='left' id='categories' >
			<strong>Category Filter</strong>
		</div>

		<div class='right'>
			Themes go here.
		</div>
	</div>

	<div style='clear:both;'></div>

	<div id='screen-content'>
		Content tab
	</div>

</div>