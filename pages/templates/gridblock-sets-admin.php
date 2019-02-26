<script id="tmpl-gridblock_set_container" type="text/html">
<div class='gridblock-set theme' data-gridblock-set-key='{{data.key}}' data-gridblock-set-category='{{data.category}}'>
	<div class='preview'>
		<div class='preview-fader'>
				<span><?php echo esc_html__( 'Preview', 'boldgrid-inspirations' ); ?></span>
		</div>
		<iframe></iframe>
	</div>
	<div class='controls'>
		<span>{{data.title}}</span>
		<a class='button button-primary'><?php echo esc_html__( 'Select', 'boldgrid-inspirations' ); ?></a>
		<div style='clear:both;'></div>
	</div>
</div>
</script>
<script id="tmpl-gridblock_set_blank_container" type="text/html">
<div class='gridblock-set blank theme'>
	<div class='preview'>
		<!-- <iframe></iframe> -->
	</div>
	<div class='controls'>
		<span><?php echo esc_html__( 'Blank', 'boldgrid-inspirations' ); ?></span>
		<a class='button button-primary'><?php echo esc_html__( 'Select', 'boldgrid-inspirations' ); ?></a>
		<div style='clear:both;'></div>
	</div>
</div>
</script>
<script id="tmpl-gridblock_set_error_fetching" type="text/html">
<div class='error-fetching-gridblock-set'>
	<p><?php echo esc_html__( 'Whoops! There seemed to be a problem downloading the newest GridBlock Sets.', 'boldgrid-inspirations' ); ?></p>
	<p>
		<a class='button' id='try_again' ><?php echo esc_html__( 'Try Again', 'boldgrid-inspirations' ); ?></a>
		<a class='button button-primary' href='post-new.php?post_type=page'><?php echo esc_html__( 'Add Blank', 'boldgrid-inspirations' ); ?></a>
	</p>
</div>
</script>
