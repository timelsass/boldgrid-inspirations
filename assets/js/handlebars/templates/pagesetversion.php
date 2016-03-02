<script id="pagesetversion-template" type="text/x-handlebars-template">	
	<select id='boldgrid_page_set_version_type'>
		<option disabled selected>Choose a pageset version</option>
		<option value='active'>Active</option>
		{{#if this}}
				<option value='inprogress'>In Progress</option>
		{{/if}}
	</select>
</script>
