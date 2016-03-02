<div class="wp-filter">
	<div class="filter-count">
		<span class="title-count count theme-count themes-current-category"
			data-theme-type="category-themes" data-count="0">0</span> <span
			class="title-count count theme-count themes-other-categories"
			style='display: none;' data-theme-type="additional-themes"
			data-count="0">0</span>
	</div>
	<span class="spinner"></span>
	<ul class="filter-links">
		<li><a class="your_category current"
			data-toggle='themes-current-category'>Category: <em><span
					class='category_name'></em></a></li>
		<li><a class="additional_themes" data-toggle='themes-other-categories'>Additional
				Themes</a></li>
	</ul>

	<a class="drawer-toggle" href="#">Budget</a>

	<div class="filter-drawer">
		<div class='filter-group'>
			<?php
			$prefix = '';
			include BOLDGRID_BASE_DIR . '/pages/includes/budget-selection.php';
			?>
		</div>
		<div style='clear: both;'></div>
	</div>
</div>
