{strip}
<table class="table table-condensed table-striped">
	<caption>Port Receive/Sent </caption>
	<thead>
		<tr>
			<th>Port</th>
			<th>Receive</th>
			<th>Sent</th>
		</tr>
	</thead>
	{foreach $data as $row}
		<tr>
			<td>{$row.dst_port}{if !empty($row.dst_port_name)}<br/>{$row['dst_port_name']}{/if}</td>
			<td>{number_format($row['rcvd']/1048576, 2, '.', '')} Mo</td>
			<td>{number_format($row['sent']/1048576, 2, '.', '')} Mo</td>
		</tr>
	{/foreach}
</table>
{/strip}