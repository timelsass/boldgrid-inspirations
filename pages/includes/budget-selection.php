<?php
if ( false == isset( $prefix ) ) {
	$prefix = 'radio_';
}
?>
<div id='budget_container'>
	<h3>Budget</h3>
	<p class='budget-description'>Choose a budget below for the images used
		within your site. You can add, change, and remove images at anytime -
		this is just a starting point!</p>
	<div class='budgets'>
		<div>
			<input type="radio" name='<?php echo $prefix;?>coin_budget'
				id='radio_coin_budget' value="20"> 0 - 20
		</div>
		<div>
			<input type="radio" name='<?php echo $prefix;?>coin_budget'
				id='radio_coin_budget' value="40"> 0 - 40
		</div>
		<div>
			<input type="radio" name='<?php echo $prefix;?>coin_budget'
				id='radio_coin_budget' value="80" checked> 0 - 80
		</div>
		<div>
			<input type="radio" name='<?php echo $prefix;?>coin_budget'
				id='radio_coin_budget' value="10000"> Show all
		</div>
		<div>
			<input type="radio" name='<?php echo $prefix;?>coin_budget'
				id='radio_coin_budget' value="0"> Free only
		</div>
	</div>
	<hr />
</div>
