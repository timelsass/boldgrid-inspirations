<script id="build-profile-template" type="text/x-handlebars-template">
<div aria-describedby="{{theme.title}}-action {{theme.title}}-name" data-theme-id="{{theme_id}}" tabindex="0" class="theme available_theme">
	<div class='step-2-theme-coins coin-bg-s'>
		Coins: {{#if_eq theme.coins '0'}}0{{else}}0 - {{theme.coins}}{{/if_eq}}
	</div>
	<div class="theme-screenshot">
		<img src="<?php echo $boldgrid_configs['asset_server'] . $boldgrid_configs['ajax_calls']['get_asset']; ?>?key=<?php
		echo ( isset( $boldgrid_configs['api_key'] ) ? $boldgrid_configs['api_key'] : null );
		?>&id={{theme.screenshotAssetId}}" alt="">
		<span class="more-details">Theme Details</span>
	</div>
	<h3 class="theme-name" id="{{theme.title}}-name">{{theme.title}}</h3>
	<div class="theme-actions" data-preview-url='{{theme.previewUrl}}' data-pde='{{json theme.pde}}' data-theme-title='{{theme.title}}' data-coins='{{theme.coins}}'>
		<button class="select-button button button-secondary activate">Select</button>
		<button class="preview-button button button-primary load-customize hide-if-no-customize" href="{{theme.previewUrl}}">Live Preview</button>
	</div>
</div>
</script>

<script id="build-profile-template-revised"
	type="text/x-handlebars-template">
<div aria-describedby="{{theme.title}}-action {{theme.title}}-name" data-theme-id="{{theme_id}}" tabindex="0" class="theme available_theme">
	<div class='step-2-theme-coins coin-bg-s'>
		Coins: {{#if_eq theme.coins '0'}}0{{else}}0 - {{theme.coins}}{{/if_eq}}
	</div>
	<div class="theme-screenshot">
		<img src="<?php echo $boldgrid_configs['asset_server'] . $boldgrid_configs['ajax_calls']['get_asset']; ?>?key=<?php
		echo ( isset( $boldgrid_configs['api_key'] ) ? $boldgrid_configs['api_key'] : null );
		?>&id={{theme.screenshotAssetId}}" alt="">
		<span class="more-details">Live Preview</span>
	</div>
	<h3 class="theme-name" id="{{theme.title}}-name">{{theme.title}}</h3>
	<div class="theme-actions" data-preview-url='{{theme.previewUrl}}' data-pde='{{json theme.pde}}' data-theme-title='{{theme.title}}' data-coins='{{theme.coins}}'>
		<button class="select-button button button-secondary activate">Select</button>
		<button class="preview-button button button-primary load-customize hide-if-no-customize" href="{{theme.previewUrl}}">Live Preview</button>
	</div>
</div>
</script>

<script id="build-profile-loading-template"
	type="text/x-handlebars-template">
<div data-theme-id='{{theme_id}}' name='available_theme_{{key}}' id='available_theme_{{key}}' class='available_theme' >
	<div class='theme'>
		<div class='theme-screenshot'>
			<img src='https://placehold.it/404x303&text=loading...' />
		</div>
		<h3 class='theme-name'>loading...</h3>
		<div class='theme-actions'>
			<button class='select-button button button-secondary activate' disabled>loading...</button>
			<button class='preview-button button button-primary load-customize hide-if-no-customize' disabled>loading...</button>
		</div>
	</div>
</div>
</script>

<script id="theme-loading-template" type="text/x-handlebars-template">
	<div class='theme available_theme_waiting'>
		<div class='theme-screenshot'>
			<img src='https://placehold.it/404x303&text=loading...' />
		</div>
		<h3 class='theme-name'>loading...</h3>
		<div class='theme-actions'>
			<button class='select-button button button-secondary activate' disabled>Activate</button>
			<button
				class='preview-button button button-primary load-customize hide-if-no-customize'
				disabled>Live Preview</button>
			</div>
	</div>
</script>
