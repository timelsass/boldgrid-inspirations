<?php
// Don't let this page get loaded directly.
defined( 'WPINC' ) ?  : die();
?>

<div class='wrap'>

	<h1>New From GridBlocks</h1>

	<div id='loading_message'>
		<p>
			<span class='spinner inline'></span>Loading GridBlock Sets.
		</p>
	</div>

	<div id='gridblock_sets' class='theme-browser'></div>

	<input type='hidden' id='new_from_gridblocks_loaded' value='false' />

</div>
