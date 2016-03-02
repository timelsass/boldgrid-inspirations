<script id="get-categories-template" type="text/x-handlebars-template">
	{{#each categories}}
		<div class='categories'>
			<span class='category' data-category-id='{{id}}' data-page-set-id='{{defaultPageSetId}}' id='category-{{toLowerCase name}}'>
				{{name}}
            </span>
            {{#each subcategories}}
			<div class='sub_categories'>
			<ul>
			     <li>
					<span class='sub_category' data-sub-category-id='{{id}}' data-page-set-id='{{defaultPageSetId}}'  data-category-id='{{../id}}' >
                    {{name}}
                    </span>
                 </li>
			</ul>
			</div><!-- sub_categories -->
            {{/each}}
		</div><!-- categories -->
	{{/each}}
</script>
<script id="category-search-results-template"
	type="text/x-handlebars-template">
<h4>Search results for <em>'{{query}}'</em> :</h4>
{{#if category_search_results}}
	<ul>
	{{#each category_search_results}}
		<li>{{parent_category_name}} &raquo; <a data-sub-category-id="{{CategoryId}}" href='#' class='category_search_result'>{{sub_category_name}}</a></li>
	{{/each}}
	</ul>
{{else}}
	<p>No search results</p>
{{/if}}
</script>
