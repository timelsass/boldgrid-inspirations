<div id='theme-selection-wrapper' class='hidden'>
	<div class='admin-notice-container'></div>

	<div>
		<h1><?php _e('Themes','boldgrid-inspirations'); ?></h1>
	</div>

	<div class='step-2-nav-bar-wrapper'></div>

	<div id="error-message" class="error hidden">
		<p>An error occurred while building your preview sites, please refresh
			and try again.</p>
	</div>

	<div id="boldgrid-theme-selection">
		<div class='boldgrid-loading hidden'></div>

		<div class="theme-browser rendered themes-current-category">
			<div class='themes' name='available_themes' id='available_themes'></div>
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
