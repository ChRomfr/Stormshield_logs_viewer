{strip}
<table class="table table-condensed table-striped">
	<caption>Top users receive </caption>
	<thead>
		<tr>
			<th>Users</th>
			<th>Reception</th>
			<th>Envoie</th>
		</tr>
	</thead>
	{foreach $data as $row}
		<tr>
			<td>
				<a href="{$config.url}index.php/stats/user/{$row.user_id}?date={$date}" title="Detail">
					{if !empty($row['name'])}
						{$row['name']}
					{else}
						unknow
					{/if}
				</a>
			</td>
			<td>{number_format($row['rcvd']/1048576, 2, '.', '')} Mo</td>
			<td>{number_format($row['sent']/1048576, 2, '.', '')} Mo</td>
		</tr>
	{/foreach}
</table>
{/strip}