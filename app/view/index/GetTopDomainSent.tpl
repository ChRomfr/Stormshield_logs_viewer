{strip}
<table class="table table-condensed table-striped">
	<caption>Top domains sent</caption>
	<thead>
		<tr>
			<th>Domaine</th>
			<th>Envoie</th>
			<th>Reception</th>					
		</tr>
	</thead>
	{foreach $data as $row}
		<tr>
			<td>{$row['name']}</td>
			<td>{number_format($row['dom_sent']/1048576, 2, '.', '')} Mo</td>
			<td>{number_format($row['dom_rcvd']/1048576, 2, '.', '')} Mo</td>					
		</tr>
	{/foreach}
</table>
{/strip}