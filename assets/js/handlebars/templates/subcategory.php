<script id="subcategory-template" type="text/x-handlebars-template">
	<select id='boldgrid_sub_cat_id'>
			<option disabled selected>Choose a sub-category</option>
			{{#each subcategories}}
				<option value='{{id}}'>{{name}} {{cat}}</option>
			{{/each}}
	</select>
</script>
