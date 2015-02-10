{strip}
<h4>Aperçu mois</h4>
<table class="table table-condensed table-striped">
	<thead>
		<tr>
			<th>Date</th>
			<th>Traffic</th>
			<th>Hits</th>
		</tr>
	</thead>
	<tbody>
		{foreach $jours as $row}
		<tr>
			<td><a href="{$Helper->getLink("report/index?date={$row.date}")}" title="Detail jours">{$row.date}</a></td>
			<td>{$row.cumul_format}</td>
			<td>{$row.hits}</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{/strip}