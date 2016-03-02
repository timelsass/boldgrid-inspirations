<div class="posttypediv" id="posttype-page">
	<ul class="posttype-tabs add-menu-item-tabs" id="posttype-page-tabs">
		<li class="tabs" data-body="pages"><a>Select Pages</a></li>
		<?php if (count($subcategories)) { ?>
			<li data-body="category"><a>More Pages</a></li>
		<?php } ?>
	</ul>
	<!-- .posttype-tabs -->

	<div class="tabs-panel tabs-panel-active"
		id="tabs-panel-posttype-page-most-recent">
		<ul id="add-pages-container" data-body="pages"
			class="categorychecklist form-no-clear">
			<a class="select-all noselect pull-right" data-select-all="true">Select
				All</a>
		</ul>
		<ul id="change-category-container" data-body="category"
			class="categorychecklist form-no-clear hidden">
			<?php
			foreach ( $subcategories as $sub_cat ) {
				?>
				<li data-sub-cat-id="<?php echo $sub_cat['id'];?>"><a><?php echo $sub_cat['name']; ?></a></li>
			<?php }?>
		</ul>
	</div>
	<!-- /.tabs-panel -->


	<div class="button-controls">
		<div class="row">
			<span class="add-button pull-right"> <input type="submit"
				value="Create Preview" disabled="disabled"
				id="accordion-preview-button" class="button-primary">
			</span> <span class="add-button pull-right"> <input type="submit"
				value="Install Pages" disabled="disabled"
				class="select-button button-secondary">
			</span> <span class="spinner"></span>
		</div>
		<div id="selection-warning" class="row">
			<div class="col-xs-10 col-xs-offset-2">
				<span>You have not selected any additional pages yet.</span>
			</div>
		</div>
	</div>
</div>
