<table class="table table-condensed table-striped" id="tdetailbyip">
	<thead>
		<tr>
			<th>Site</th>
			<th>Bytes</th>
	</thead>
	<tbody>
		{foreach $results as $row}
			<tr>
				<td>{$row.site}</td>
				<td>{$row.cumul|formatBytes}</td>
			</tr>
		{/foreach}
	</tbody>
</table>