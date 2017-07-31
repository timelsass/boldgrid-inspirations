<?php
// Prevent direct calls.
require BOLDGRID_BASE_DIR . '/pages/templates/restrict-direct-access.php';

// Check asset server availability.
$is_asset_server_available = (bool) get_site_transient( 'boldgrid_available' );

// Print a message for connection failure.
$notice_template_file = BOLDGRID_BASE_DIR .
'/pages/templates/boldgrid-connection-issue.php';

if ( ! $is_asset_server_available &&
! in_array( $notice_template_file, get_included_files(), true ) ) {
	include $notice_template_file;
}

$reseller = new Boldgrid\Library\Library\Reseller();
$coinUrl = $reseller->data['reseller_coin_url'];

// Configure the "purchase coins" link.
$reseller_link = sprintf(
	wp_kses(
		__( 'You can purchase additional coins through <a href="%1$s" target="_blank">BoldGrid Central</a>.', 'boldgrid-inspirations' ),
		array( 'a' => array( 'href' => array(), 'target' => array() ) )
	),
	esc_url( $reseller->centralUrl )
);
if ( $reseller->centralUrl !== $coinUrl && isset( $reseller->data['reseller_title'] ) ) {
	$reseller_link = sprintf(
		wp_kses(
			__( 'You can purchase additional coins through your official BoldGrid reseller, <a href="%s" target="_blank">%s</a>.', 'boldgrid-inspirations' ),
			array( 'a' => array( 'href' => array(), 'target' => array() ) )
		),
		$coinUrl,
		$reseller->data['reseller_title']
	);
}

?>

<div class='wrap'>

<?php
	// include the navigation
	include BOLDGRID_BASE_DIR . '/pages/includes/cart_header.php';
?>

	<div class='plugin-card'>

		<div class='plugin-card-top'>
			<p><?php
				echo $reseller_link . ' ' .
				esc_html__( 'After you have purchased additional coins, your new coin balance will update on the transaction pages.', 'boldgrid-inspirations' );
			?></p>
		</div>

	</div>

</div>