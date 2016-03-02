<script id="theme-template" type="text/x-handlebars-template">	
	<select id='boldgrid_theme_id'>
		<option disabled selected>Choose a theme</option>
		{{#each this}}
			<option value='{{id}}'>{{name}}</option>
		{{/each}}
	</select>
</script>
