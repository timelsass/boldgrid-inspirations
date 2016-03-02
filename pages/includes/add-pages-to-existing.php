<div id="add-existing-pages"
	class='bold-grid-enabled fluid-container hidden'>
	<div class='admin-notice-container'></div>

	<div>
		<h2><?php _e('Add Pages','boldgrid-inspirations'); ?></h2>
	</div>

	<div class='step-2-nav-bar-wrapper'>
		<?php require 'step-2-nav-bar.php'; ?>
	</div>

	<div class='row page-selection-and-preview'>
		<div class="col-lg-3 col-md-4 col-sm-5">
			<?php echo $accordion; ?>
		</div>
		<div class="col-lg-9 col-md-8  col-sm-7">
			<div class='boldgrid-loading hidden'></div>
			<div id="page_set_preview" name="page_set_preview"
				class="hidden page-selection-preview"></div>
		</div>
	</div>
</div>
