<div id='theme-selection-wrapper'>
	<div class='admin-notice-container'></div>

	<div id='theme-selection-h1'>
		<h1><?php _e('Themes','boldgrid-inspirations'); ?></h1>
	</div>

	<div class='step-2-nav-bar-wrapper'></div>

	<div id="error-message" class="error hidden">
		<p>An error occurred while building your preview sites, please refresh
			and try again.</p>
	</div>

	<div id="boldgrid-theme-selection">
		<div class='step-2-nav-bar-wrapper'>
			<div class='wrap step-2-nav-bar'>
				<div class='wp-filter' id='additional-themes-bar'>
					<ul class='filter-links category'>
						<li class='category-label'><strong>Category</strong>: <span
							class="count theme-count category-name">Unknown</span></li>
					</ul>
					<a class="additional_themes drawer-toggle"
						data-theme-type='current'>Additional Themes</a>
				</div>
			</div>
		</div>

		<div class='boldgrid-loading hidden'></div>

		<div class="theme-browser rendered themes-current-category">
			<div class='themes' id='available_themes'></div>
			<div class='clear'></div>
		</div>

		<div class="theme-browser rendered themes-other-categories hidden">
			<div class="theme-seperator themes-other-categories">
				<div class="bg-seperator">
					<h3>Additional Themes</h3>
					<span>The following themes don't match your category, but the home
						page content can be changed to fit your needs.</span>
					<hr>
				</div>
			</div>
			<div class='themes' id='additional_themes'></div>
			<div class='clear'></div>
		</div>
	</div>
</div>
