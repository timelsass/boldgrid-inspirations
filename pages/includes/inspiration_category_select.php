<div class='step-1-header'>
	<h1><?php _e('Step 1: Choose your Category','boldgrid-inspirations'); ?></h1>
	<form id='category_search'>
		<input id="category-search-input" name="s" value="" type="search"
			autocomplete="off" placeholder="Search Categories ..."
			class="wp-filter-search hidden">
	</form>
</div>

<div id='category_search_results'></div>

<?php
include BOLDGRID_BASE_DIR . '/pages/includes/browse_category.php';