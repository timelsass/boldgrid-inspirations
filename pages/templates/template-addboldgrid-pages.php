<script id="add-boldgrid-page-template"
	type="text/x-handlebars-template">
	{{#if section_doesnt_exist}}
	<h4 class="page_cat_divider" data-sub-cat-id={{sub_cat_id}}>{{sub_cat_name}}</h4><hr>
	<div class='page-cat-wrap' data-sub-cat-id="{{sub_cat_id}}">
	{{/if}}
	{{#each pages}}
		<li>
			<label class="menu-item-title"><input type="checkbox" value="{{id}}" class="menu-item-checkbox">{{page_title}}</label>
		</li>
	{{/each}}
	{{#if section_doesnt_exist}}
		</div>
	{{/if}}
</script>
