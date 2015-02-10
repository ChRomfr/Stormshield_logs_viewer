{strip}
<table class="table table-striped table-condensed">
	<thead>
		<tr>
			<th>Heure</th>
			<th>Source</th>
			<th></th>
			<th>Destination</th>
			<th></th>
			<th>Receive</th>
			<th>Sent</th>
		</tr>
	</thead>
	{foreach $logs as $log}
	<tr>
		<td>{$log.hours}</td>
		<td>
			{if !empty($log.src_name)}{$log.src_name}<br/>{/if}
			{$log.src_ip}
			{if !empty($log.user)}<br/>{$log.user}{/if}
		</td>
		<td>
			{$log.src_port_name}
			({$log.src_port})
		</td>
		<td>{$log.dst_name}</td>
		<td>
			{$log.dst_port_name}
			({$log.dst_port})
		</td>
		<td>{$log.rcvd}</td>
		<td>{$log.sent}</td>
	</tr>
	{/foreach}
</table>
<hr/>
<div class="text-center">{$nblogs|number_format} ligne(s)</div>
{if isset($Pagination)}
<div class="pull-left">{$Pagination->render()}</div>
{/if}
<div class="clearfix"></div>
{/strip}