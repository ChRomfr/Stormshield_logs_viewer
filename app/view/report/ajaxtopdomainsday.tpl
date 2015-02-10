<table class="table table-condensed table-striped" id="tdetailbyip">
	<thead>
		<tr>
			<th>Site</th>
			<th>Hits</th>
			<th>Bytes</th>
			<th>Bytes<th>
	</thead>
	<tbody>
		{foreach $logs.top_sites as $row name=ttopdomain}
			<tr>
				<td><a href="{$Helper->getLink("report/domaindetail?url={$row.url}&amp;date={$date}")}">{$row.url}</a></td>
				<td>{$row.hits}</td>
				<td>{$row.bytes|formatBytes}</td>
				<td><small>{$row.bytes}</small></td>
			</tr>
			{if $smarty.foreach.ttopdomain.index == 20}{break}{/if}
		{/foreach}
	</tbody>
</table>