<script type="text/html" id="tmpl-init-categories">
	<div class='category-filter' ><?php echo __( 'Category filter', 'boldgrid-inspirations' ); ?></div>

	<div class='category'>
		<div class='sub-category'>
			<input type="radio" name="sub-category" checked data-sub-category-id="0" > <?php echo __( 'All', 'boldgrid-inspirations' ); ?>
		</div>
	</div>

	<#
		_.each( data, function( category ) {
	#>
			<div class='category' data-category-id='{{category.id}}' >
				<span class='category-name' >{{category.name}}</span>
				<a class='expand'></a>
				<div class='sub-categories hidden' data-category-id='{{category.id}}'>
	<#
				_.each( category.subcategories, function( sub_category ) {
	#>
					<div class='sub-category'>
						<input type="radio" name="sub-category" data-sub-category-id="{{sub_category.id}}"> <span class='sub-category-name'>{{sub_category.name}}</span>
					</div>
	<#
				});
	#>
				</div>
			</div>
	<#
		});
	#>
</script>

<script type='text/html' id='tmpl-theme'>
	<#
		// Format our theme title.
		data.theme.Title = data.theme.Name.replace( 'boldgrid-', '' );
		data.key = IMHWPB.configs.api_key
	#>
	<div	class="theme"
			tabindex="0"
			aria-describedby="boldgrid-florentine-action boldgrid-florentine-name"
			data-category-id="{{data.category.id}}"
			data-sub-category-id="{{data.sub_category.id}}"
			data-sub-category-title="{{data.sub_category.name}}"
			data-page-set-id="{{data.sub_category.defaultPageSetId}}"
			data-theme-id="{{data.theme.Id}}"
			data-theme-title="{{data.theme.Title}}"
	">

		<div class="theme-screenshot">
			<img src="{{data.configs.asset_server}}/api/asset/get?key={{data.configs.api_key}}&id={{data.profile.asset_id}}" alt="">
		</div>

		<h2 class="theme-name" >
			<span class='name'>{{data.theme.Title}}</span>
			<span class='sub-category-name'>- {{data.sub_category.name}}</span>
		</h2>

		<div class="theme-actions">
			<a class="button button-primary hide-if-no-customize">Select</a>
		</div>
	</div>
</script>

<script type='text/html' id='tmpl-pagesets'>
	<#
		_.each( data, function( pageset ) {
			pageset.is_default_page_set = ( '1' === pageset.is_default_page_set ? 'checked' : '' );
	#>
		<div class='pageset-option'>
			<input type="radio" name="pageset" data-page-set-id="{{pageset.id}}" {{pageset.is_default_page_set}} > {{pageset.page_set_name}}<br />
			{{pageset.page_set_description}}
		</div>
	<#
		});
	#>
</script>