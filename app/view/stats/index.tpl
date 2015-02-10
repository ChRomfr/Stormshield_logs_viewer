<div class="row">	
	<div id="TopDomains" class="col-md-5">Recuperation des stats ...</div>
	<div id="TopPorts"></div>
</div>

<script type="text/javascript">
var u = {$user.id};
var date = "{$date_view}";
{literal}
$(document).ready(function(){	
	// Recuperation stats domain
	$.get(
        base_url + 'index.php/stats/GetTopDomains',
        {nohtml:'nohtml', u:u, date:date},
        function(data){
            $("#TopDomains").html(data);
        }        
    );
})
{/literal}
</script>