<table class="table table-striped">
	<thead>
		<tr>
			<th>Utilisateur</th>
			<th>Bande passante</th>
		</tr>
	</thead>
	<tbody>
		{foreach $topusers as $row}
		<tr>
			<td><a href="javascript:getdetailbyIpAndDate('{$row.ip}','{$date}');" title="Detail utilisateur">{$row.ip}</a></td>
			<td>{$row.cumul|formatBytes}</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{if count($topusers) < 11}
	<div class="pull-right">
		<a href="javascript:getallusers();" title="" class="btn">+</a>
	</div>
	<div class="clearfix"></div>
{/if}

<script type="text/javascript">
<!--
function getdetailbyIpAndDate(ip, date){
	$.get(
        '{$Helper->getLink("report/detail")}',{literal}
        {nohtml:'nohtml', ip:ip, date:date, ajax:'ajax'},{/literal}
        function(data){
        	$("#squidreportcontent").html(data);        	
        }
    );
}

function getallusers(){
	$.get(
        '{$Helper->getLink("report/ajaxuserday/{$date}")}',{literal}
        {nohtml:'nohtml'},{/literal}
        function(data){
        	$("#topusers").html(data);        	
        }
    );
}
//-->
</script>