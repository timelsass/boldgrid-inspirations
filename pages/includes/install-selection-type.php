<h1>
Inspirations
<?php
if ( isset( $is_author ) && true === $is_author ) {
	?>
<span style="color: red;"> (as an author)</span>
<?php
}
?>
</h1>

<div id="select-install-type" class='hidden'>
	<div class='wrap'>
		<div class='plugin-card col-xs-12 col-sm-8 col-md-8 col-lg-6'>
			<div class='plugin-card-top'>
				<p>
					With BoldGrid, when creating a new site, you start with <strong
						class='dashicons dashicons-inline dashicons-lightbulb'>Inspirations</strong>
					then you <strong
						class='dashicons dashicons-inline dashicons-admin-customize'>Customize</strong>
					to your particular situation. You are starting Inspirations now so
					you will explore different website designs, pages you may want in
					your site, content that is specific to your industry, and
					functionality you may need. <strong>Don't worry if you are not
						sure, you can always return to Inspirations to get different pages
						or designs, or even start completely over</strong>.
				</p>

				<!--
				***************************************************************
				Begin custom messages.
				***************************************************************
				-->
				<div class="error notice specific-to-you hidden">
					<p>The following is specific to you.</p>
				</div>

				<p
					class='staging-plugin-installed staging-site-installed active-site-not-installed hidden'>
					Your BoldGrid install has "Staging". Staging allows you to have an
					"Active Site" (a site publicly available to your visitors) while
					also working on a new site in Staging. We've noticed that you have
					installed a Staging Site and that you are running a default Active
					Site. If you want a different theme or additional pages for your
					Staging Site, go to <a
						href='admin.php?page=boldgrid-inspirations&boldgrid-tab=themes'><strong>Install
							New Themes</strong></a> or <a
						href='admin.php?page=boldgrid-inspirations'><strong>Add New Pages</strong></a>.
					If you are just experimenting, you may want to look at <a
						href='admin.php?page=boldgrid-tutorials'><strong>how to Start Over</strong></a>.
					You can also choose to just install a new site as the Active site.
					<strong>Please select an install location to continue</strong>.
				</p>

				<p
					class='staging-plugin-installed staging-site-not-installed active-site-installed hidden'>
					Your BoldGrid install has "Staging". Staging allows you to have an
					"Active Site" (a site publicly available to your visitors) while
					also working on a new site in Staging. We've noticed that you have
					an Active Site. If you want a different theme or additional pages
					for your Active Site, go to <a
						href='admin.php?page=boldgrid-inspirations&boldgrid-tab=themes'><strong>Install
							New Themes</strong></a> or <a
						href='admin.php?page=boldgrid-inspirations'><strong>Add New Pages</strong></a>.
					If you are working on a new site or working on a significant change
					in your existing site, we recommend you use Staging. <strong>Please
						select an install location to continue</strong>.
				</p>

				<p class='choice_intro_text hidden'>Your BoldGrid install has
					"Staging". Staging allows you to have an "Active Site" (a site
					publicly available to your visitors) while also working on a new
					site in Staging. We've noticed that you have not installed a site
					of your own and are running a default site.</p>

				<p class='choice_intro_text hidden'>
					In this case, you can choose to leave the default site running
					while you work on your new site in Staging or simply install your
					new site as Active. Installing to Active requires less steps and is
					faster so we generally recommend that choice. <strong>Please select
						an install location to continue</strong>.
				</p>


			</div>
			<div class='plugin-card-bottom'>
				<div class='column-updated' style='width: auto;'>
					<!--
					***********************************************************
					Begin custom buttons.
					***********************************************************
					-->
					<div
						class='staging-plugin-installed staging-site-installed active-site-not-installed hidden'>
						<a class='button button-secondary'
							href='admin.php?page=boldgrid-inspirations&boldgrid-tab=themes'>New
							Theme for Staging</a> <a class="button button-primary"
							data-install-type="active">Install New Active Site</a>
					</div>

					<div
						class='staging-plugin-installed staging-site-not-installed active-site-installed hidden'>
						<a class='button button-secondary'
							href='admin.php?page=boldgrid-inspirations&boldgrid-tab=themes'>New
							Theme for Active</a> <a class="button button-primary"
							data-install-type="staging">Install New Staging Site</a>
					</div>

					<div class='choice_intro_text hidden'>
						<a class="button button-secondary" data-install-type="staging">Install
							as Staging</a> <a class="button button-primary"
							data-install-type="active">Install as Active</a>
					</div>

					<div class='no_staging_intro_text hidden'>
						<a class="button button-primary" data-install-type="active">Continue</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="select-content-install-type" class='hidden'>
	<div class='wrap'>
		<div class='plugin-card'>
			<div class='plugin-card-top'>
				<h3 class='choice_intro_text hidden'>How would you like to install
					your new content?</h3>
				<p>We noticed that you have an Active and a Staging Site. Please
					select an install location to continue.</p>
			</div>
			<div class='plugin-card-bottom'>
				<div class='column-updated'>
					<div class='choice_intro_text hidden'>
						<a class="button button-secondary" data-install-type="staging">Install
							as Staging</a> <a class="button button-primary"
							data-install-type="active">Install as Active</a>
					</div>
					<div class='no_staging_intro_text hidden'>
						<a class="button button-primary" data-install-type="active">Continue</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
