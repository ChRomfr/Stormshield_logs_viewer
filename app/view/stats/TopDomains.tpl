{strip}
<table class="table table-condensed table-striped">
	<caption>Top domains</caption>
	<thead>
		<tr>
			<th>Domain</th>
			<th>Receive</th>
			<th>Sent</th>
		</tr>
	</thead>
	<tbody>
		{foreach $data as $row}
		<tr>
			<td>{$row['domain']}</td>
			<td>{number_format($row['rcvd']/1048576, 2, '.', '')} Mo</td>
			<td>{number_format($row['sent']/1048576, 2, '.', '')} Mo</td>	
		</tr>
		{/foreach}
	</tbody>
</table>
{/strip}