{strip}
<h4>Stats live - {$ip}</h4>
<div class="pull-right">
	<a href="javascript:ajaxstatindex();" class="btn">Retour</a>
</div>
<div class="clearfix"></div>
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