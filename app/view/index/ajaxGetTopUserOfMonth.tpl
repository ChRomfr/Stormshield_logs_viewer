{strip}
<h4>Top traffic par IP</h4>
<table class="table table-striped table-condensed">
	<thead>
		<tr>
			<th>Utilisateur</th>
			<th>Bande passante</th>
		</tr>
	</thead>
	<tbody>
		{foreach $topusers as $row}
		<tr>
			<td><a href="javascript:getdetailbyIpAndDate('{$row.ip}');" title="Detail utilisateur">{$row.ip}</a></td>
			<td>{$row.cumul|formatBytes}</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{/strip}