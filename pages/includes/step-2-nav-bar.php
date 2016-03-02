<?php

// Prevent direct calls
if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

?>

<div class='wrap step-2-nav-bar'>
	<div class='wp-filter'>
		<ul class='filter-links category'>
			<li class='category-label'><strong>Category</strong>: <span
				class="count theme-count category-name">Unknown</span></li>
		</ul>
		<a class="additional_themes drawer-toggle hidden"
			data-theme-type='current'>Additional Themes</a>
		<ul class='filter-links'>
			<li class='coin-budget-label coin-bg-s'>Coin Budget:</li>
			<li><a data-value='20' class='coin_budget current'>0 - 20 Coins</a></li>
			<li><a data-value='40' class='coin_budget'>0 - 40 Coins</a></li>
			<li><a data-value='60' class='coin_budget'>0 - 60 Coins</a></li>
			<li><a data-value='80' class='coin_budget'>0 - 80 Coins</a></li>
			<li><a data-value='0' class='coin_budget'>Free</a></li>
		</ul>
	</div>
</div>
