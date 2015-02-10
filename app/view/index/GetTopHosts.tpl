{strip}
<table class="table table-condensed table-striped">
	<caption>Top hosts receive </caption>
	<thead>
		<tr>
			<th>Host</th>
			<th>Receive</th>
			<th>Sent</th>
		</tr>
	</thead>
	{foreach $data as $row}
		<tr>
			<td>
				<a href="{$Helper->getLink("stats/host/{$row.src_ip}?date={$date}")}" title="">
					{if !empty($row.src_name)}{$row.src_name}<br/>{/if}{$row.src_ip}
				</a>
			</td>
			<td>{number_format($row['rcvd']/1048576, 2, '.', '')} Mo</td>
			<td>{number_format($row['sent']/1048576, 2, '.', '')} Mo</td>
		</tr>
	{/foreach}
</table>
{/strip}