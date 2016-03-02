<script id="transactions-template" type="text/x-handlebars-template">
<table class="widefat" id='receipts'>
	<thead>
		<tr>
			<th>Transaction ID</th>
			<th class='sort-date sorted asc'>
				<a href=''>
					<span>Date</span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th>Items</th>
			<th>Coins</th>
			<th>Invoice</th>
		</tr>
	</htead>
	<tbody>
		{{#each transactions}}
		<tr>
			<td>{{transaction_id}}</td>
			<td>{{transaction_date}}</td>
			<td>{{objCount transaction_item}}</td>
			<td>
				<span class='coin-bg-s'>
					{{#ifCond transaction_total '<' 0}}{{multiply transaction_total "-1"}}{{else}}{{transaction_total}}{{/ifCond}} 
					{{#ifCond transaction_total '>' 0}}(Credit){{/ifCond}}
				</span>
			</td>
			<td><a class='view' data-transaction-id="{{transaction_id}}" href='#'>View</a></td>
		</tr> 
		{{/each}}
</table>
</script>

<script id="no-transactions-template" type="text/x-handlebars-template">
	<p>There are no transactions to display at this time.</p>
</script>

<?php // Example object being passed in: http://pastebin.com/sgQL6Bb1 ?>
<script id="transaction-template" type="text/x-handlebars-template">
<h1>Invoice for Transaction ID: {{transaction_id}}</h1>
<table class="widefat receipt">
	<thead>
		<tr>
			<th>Description</th>
			<th></th>
			<th>Coins</th>
			<th></th>
		</tr>
	</htead>
	<tbody>
		{{#each transaction_item}}
		<tr data-user-transaction-item-id='{{user_transaction_item_id}}'>
			<td class='thumbnail'></td>
			<td>
				{{description}}
				{{#ifCond coins '>' 0}}
					{{#isSetAndNotNull ../../transaction_reseller_title}}
						(<strong>Processed by</strong>: <em>{{../../../transaction_reseller_title}})</em>
					{{/isSetAndNotNull}}
				{{/ifCond}}
			</td>
			<td>
				<span class='coin-bg-s'>
					{{#ifCond coins '<' 0}}{{multiply coins "-1"}}{{else}}{{coins}}{{/ifCond}}
				</span>
			</td>
			<td class='redownload'></td>
		</tr> 
		{{/each}}
</table>
</script>

<script id="tablenav-top-template" type="text/x-handlebars-template">
	<div class='tablenav-pages'>
		{{this}} Invoices
	</div>
</script>
