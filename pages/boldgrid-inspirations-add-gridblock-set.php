<?php
// Prevent direct calls.
require BOLDGRID_BASE_DIR . '/pages/templates/restrict-direct-access.php';

?>

<div class='wrap'>

	<h1><?php echo esc_html__( 'New From GridBlocks', 'boldgrid-inspirations' ); ?></h1>

	<div id='loading_message'>
		<p>
			<span class='spinner inline'></span><?php echo esc_html__( 'Loading GridBlock Sets.', 'boldgrid-inspirations' ); ?>
		</p>
	</div>

	<div id='gridblock_sets' class='theme-browser'></div>

	<input type='hidden' id='new_from_gridblocks_loaded' value='false' />

</div>
