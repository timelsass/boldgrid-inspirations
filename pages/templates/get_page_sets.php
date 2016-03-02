<script id="page-set-selection" type="text/x-handlebars-template">
	<h3>Page Set</h3>
	<ul class='page-sets'>
		{{#each pageSets}}
			<li>
				<div>
					<input type='radio' name='available_page_set_id' id='available_page_set_id' value='{{id}}' 
    					{{#if_eq is_default_page_set "1"}}
        					checked="checked"
    					{{/if_eq}}
					/>
	   				{{page_set_name}}
					{{#if_eq is_active "0"}}
						<span style="color: red;">(Inactive)</span>
					{{/if_eq}}
				</div>
				<div>{{page_set_description}}</div>
			</li>
    	{{/each}}
	</ul>
</script>

<script id="page-set-preview-select-template"
	type="text/x-handlebars-template">
<div class='theme-browser rendered'>
	<div class='themes'>
		<div class='step-3-theme-coins coin-bg-s'>
			Coins: {{#if_eq coins '0'}}0{{else}}0 - {{coins}}{{/if_eq}}
		</div>
		<div aria-describedby="{{theme_title}}-action {{theme_title}}-name" tabindex="0" class="theme">
			<h3 class="theme-name" id="{{theme_title}}-name">{{theme_title}}</h3>
			<div class="theme-actions" data-preview-url='{{preview_url}}' data-pde='{{json theme.pde}}' data-theme-title='{{theme_title}}' data-coins='{{coins}}'>
				<button class="preview-button button button-secondary load-customize hide-if-no-customize">Preview</button>
				<button class="select-button button button-primary activate">Select</button>
			</div>
			<div class="theme-screenshot"><img src="{{page_set_thumbnail_url}}" alt=""></div>
		</div>
	</div>
</div>
</script>
