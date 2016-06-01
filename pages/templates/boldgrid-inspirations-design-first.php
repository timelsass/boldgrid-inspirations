<script type="text/html" id="tmpl-init-categories">
	<#
		_.each( data, function( category ) {
	#>
			<strong>{{category.name}}</strong><br />
	<#
			_.each( category.subcategories, function( sub_category ) {
	#>
				<input type="radio" name="sub_category"> {{sub_category.name}}<br>
	<#
			});
		});
	#>
</script>