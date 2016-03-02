<script id="inspiration-selection-template"
	type="text/x-handlebars-template">
	<div class="updated notice is-dismissible">
		<p>Your new BoldGrid site will be installed as your <strong>{{install_type}}</strong> site.
			{{#if_eq install_type "Active"}}
				To install your staging site instead click <strong><a href="{{url}}&force-section=staging">here</a></strong>.
			{{else}}
				To install your active site instead click <strong><a href="{{url}}&force-section=active">here</a></strong>.
			{{/if_eq}}
		</p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	</div>
</script>
<script id="inspiration-content-selection-template"
	type="text/x-handlebars-template">
	<div class="updated notice is-dismissible">
		<p>Your new BoldGrid content will be installed to your <strong>{{install_type}}</strong> site.
			{{#if_eq install_type "Active"}}
				To install to your staging site instead click <strong><a href="{{url}}&force-section=staging">here</a></strong>.
			{{else}}
				To install to your active site instead click <strong><a href="{{url}}&force-section=active">here</a></strong>.
			{{/if_eq}}
		</p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	</div>
</script>
<script id="inspiration-recognize-template"
	type="text/x-handlebars-template">
	<div class="updated notice is-dismissible">
		<p>We've recognized that you've already installed your <strong>{{install_type}}</strong> site.
	 		Your {{content_type}} will be installed in that installation.</p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	</div>
</script>
<script id="inspiration-recognize-site-template"
	type="text/x-handlebars-template">
	<div class="updated notice is-dismissible">
		<p>We've recognized that you've already installed your {{existing_install_type}} site.
	 	This new installation will be your <strong>{{new_install_type}}</strong> site.</p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	</div>
</script>

<script id="inspiration-has-not-built-with-either-template"
	type="text/x-handlebars-template">
	<div class="error notice is-dismissible">
		<p>We've recognized that you haven't installed an <em>Active</em> or <em>Staging</em> site with Inspirations. Before adding additional BoldGrid pages and themes, we recommend that you start with <a href='admin.php?page=boldgrid-inspirations&boldgrid-tab=install' class='dashicons-before dashicons-lightbulb' style='text-decoration:none'>Inspirations</a>.</p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	</div>
</script>
