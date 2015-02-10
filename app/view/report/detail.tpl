<!-- view/report/detail.tpl -->
{strip}
<ul class="breadcrumb">
	<li><a href="{$Helper->getLink("index")}" title="Accueil">Accueil</a><span class="divider">&gt;&gt;</span></li>
	<li><a href="{$Helper->getLink("report/index?date={$date}")}" title="Detail">Detail</a><span class="divider">&gt;&gt;</span></li>
	<li class="active">Detail des accès de {$ip} le {$date}</li>
</ul>

<div class="well">
	<h3>Detail des accès de {$ip} le {$date}</h3>
</div>

<div class="well">
	<table class="table table-condensed table-striped" id="tdetailbyip">
		<thead>
			<tr>
				<th>Site</th>
				<th>Hits</th>
				<th>Bytes</th>
				<th>Bytes<th>
		</thead>
		<tbody>
			{foreach $logs as $row}
				<tr>
					<td>{$row.url}</td>
					<td>{$row.hits}</td>
					<td>{$row.bytes|formatBytes}</td>
					<td><small>{$row.bytes}</small></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>
{/strip}
<script type="text/javascript">
<!--
	$(document).ready(function() 
	    { 
	        $("#tdetailbyip").tablesorter(); 
	    } 
	);
//-->
</script>