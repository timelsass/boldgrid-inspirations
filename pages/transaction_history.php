<?php
// Prevent direct calls.
require BOLDGRID_BASE_DIR . '/pages/templates/restrict-direct-access.php';

add_thickbox();

include BOLDGRID_BASE_DIR . '/pages/templates/transaction_history.php';

?>
<div class='wrap'>
<?php
	include BOLDGRID_BASE_DIR . '/pages/includes/cart_header.php';
?>
	<h1>Transaction History</h1>
	<div class='tablenav top'></div>
	<div id='transactions'>Loading transaction history...</div>
	<div class='tablenav bottom'></div>
	<div id='transaction' class='hidden'></div>
</div>
