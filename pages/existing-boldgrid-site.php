<?php

// Prevent direct calls
if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

?>

<p>It appears you have already build a site with BoldGrid!</p>

<p>
	If you would like to start over and build another site, please see the
	<strong>Start Over</strong> section within your <a
		href='admin.php?page=imh-wpb-options'>boldgrid settings page</a>.
</p>

<p>
	You may also keep your existing site, and instead build a new BoldGrid
	website in a staging environment using our <a
		href='https://repo.boldgrid.com/boldgrid-staging.zip' target='_blank'>BoldGrid
		Staging Plugin</a>.
</p>
