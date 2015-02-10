{strip}
<h3>Detail du : {$date}</h3>
<div class="row">
	<div class="span5 well">
		<h4>Utilisateurs/Postes</h4>
		<table class="table table-striped table-condensed">
		{foreach $top_users as $row}
		<tr>
			<td><a href="{$Helper->getLink("report/detail?ip={$row.ip}&amp;date={$date}")}" title="">{$row.ip}</a></td>
			<td>{$row.cumul}</td>
			<td>{$row.cumul_formated}</td>
		</tr>
		{/foreach}
		</table>
	</div>
	<div class="span5 well">
		<h4>Domaines/sites</h4>
		<table class="table table-striped table-condensed">
		{foreach $top_sites as $row}
		<tr>
			<td>{$row.url}</td>
			<td>{$row.hits}</td>
			<td>{$row.cumul_formated}</td>
		</tr>
		{/foreach}
		</table>
	</div>
</div>
{/strip}