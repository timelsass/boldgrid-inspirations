<?php

// Prevent direct calls
if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

include BOLDGRID_BASE_DIR . '/pages/templates/widget_customization.php';

?>

<div id='widget_customization_tabs'>loading...</div>

<div id='widget_customization_body'></div>
