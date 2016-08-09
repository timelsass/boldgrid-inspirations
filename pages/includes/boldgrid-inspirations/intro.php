<?php

$template = '<div style="border: 1px solid #ececec;"><div class="top" style="background:#fff;">%s</div><div class="bottom" style="background:#fafafa;">%s</div></div>';

// echo "<pre>"; print_r( $mode_data ); echo "</pre>";

/*
Array
(
    [has_active_bg_site] =>
    [has_staged_site] =>
    [has_blank_active_site] => 1
    [open-section] =>
    [staging_active] =>
    [url] => https://wpbex-dev-bradm.boldgrid.com/single-site/wp-admin/admin.php?page=boldgrid-inspirations
)
 */

$scenario = '';
$to_check = array( 'has_blank_active_site', 'has_active_bg_site', 'has_staged_site', 'staging_active' );
foreach( $to_check as $check ) {
	$scenario .= ( $mode_data[ $check ] ? 'T' : 'F' );
}

/*
 * [T] has_blank_active_site
 * [ ] has_active_bg_site
 * [ ] has_staged_site
 * [ ] staging_active
 */
if( 'TFFF' === $scenario ) {
	$top = __( 'Each BoldGrid website begins its creation process here, within Inspirations. This is where you\'ll explore different website designs, page sets, and content specific to your industry. Don\'t worry if you are not sure, you can always return here to start over.', 'boldgrid-inspirations' );
	$bottom = '<a class="button button-primary">Begin Inspirations</a>';
}

printf( $template, $top, $bottom );


?>