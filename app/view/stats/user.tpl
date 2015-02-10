{strip}
<h3>Statistique of <strong>{$user.name}</strong> for <strong>
	{if strlen({$date}) == 7}
	<a href="{$Helper->getLink("index/index?date={$date}")}" title="">{$date}</a>
	{else}
	<a href="{$Helper->getLink("index/Resume_day/{$date}")}" title="">{$date}</a>
	{/if}
</strong></h3>
<hr/>
<div class="container-fluid">
	<div class="col-sm-5 col-md-5 well nanos" id="TopDomains" style="height:500px; overflow: auto;"><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>
	<div class="col-sm-5 col-md-5 well col-md-offset-1" id="TopPorts" style="height:500px; overflow: auto;"><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>
</div>
{/strip}
<script type="text/javascript">
	var uid = {$user.id};
	var date = '{$date}';
	{literal}
	$(document).ready(function(){	
		// Recuperation stats domain
		$.get(
	        base_url + 'index.php/stats/GetTopDomains',
	        {nohtml:'nohtml', u:uid, date:date},
	        function(data){
	            $("#TopDomains").html(data);
	        }        
	    );

	    $.get(
	        base_url + 'index.php/stats/GetTopPort_user',
	        {nohtml:'nohtml', u:uid, date:date},
	        function(data){
	            $("#TopPorts").html(data);
	        }        
	    );
	})
	{/literal}
</script>