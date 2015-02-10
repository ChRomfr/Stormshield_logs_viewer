{strip}
<h3>Resume for <strong>{$date}</strong>&nbsp;<small>(<a href="{$Helper->getLink("stats/full_logs_day/{$date}")}" title="All logs of day">view all</a>)</small></h3>
<hr/>
<div class="container">
    <div class="row">
        <div class="pull-left col-sm-3">
            <a href="{$Helper->getLink("index/Resume_day/{$prev_day}")}" class="btn btn-default"><<&nbsp;{$prev_day}</a>
        </div>
        <div class="col-sm-3">
            <select id="days_list" class="form-control">
            </select>
            <br/>
            <div class="text-center"><a href="{$Helper->getLink("index/index?date={$date_array[0]}-{$date_array[1]}")}" title=""><small>Resume : {$date_array[0]}-{$date_array[1]}</small></a></div>
        </div>
        <div class="pull-right col-sm-3">
            <a href="{$Helper->getLink("index/Resume_day/{$next_day}")}" class="btn btn-default">{$next_day}&nbsp;>></a>
        </div>
    </div>
</div>
<hr/>
<div class="row">
	<div class="col-md-5 col-sm-5 well" id="TopDomainsReceive"><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>
	<div class="col-md-5 col-sm-5 well col-md-offset-1" id="TopDomainsSent"><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>
</div>

<div class="row">
	<div class="col-md-5 col-sm-5 well" id="TopUsers"><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>
    <div class="col-md-5 col-sm-5 well col-md-offset-1" id="TopHosts"><div class="text-center"></div></div>
</div>

<div class="row">
    <div class="col-md-5 col-sm-5 well"><h4>Receive/Sent %</h4><hr/><div class="text-center" id="donut-rcvd-sent"></div></div>
    <div class="col-md-5 col-sm-5 well col-md-offset-1"><h4>Internet/VPN-VLAN %</h4><hr/><div class="text-center" id="donut-internet-internal"></div></div>
</div>
{/strip}

<script type="text/javascript">
var date = "{$date}";
{literal}
$(document).ready(function(){	
	// Recuperation stats domain
	$.get(
        base_url + 'index.php/index/GetTopDomainReceive_day',
        {date:date},
        function(data){
            $("#TopDomainsReceive").html(data);
        }        
    );

    $.get(
        base_url + 'index.php/index/GetTopDomainSent_day',
        {date:date},
        function(data){
            $("#TopDomainsSent").html(data);
        }        
    );

    $.get(
        base_url + 'index.php/index/GetTopUsers_day',
        {date:date},
        function(data){
            $("#TopUsers").html(data);
        }        
    );

    $.get(
        base_url + 'index.php/index/GetTopHosts_day',
        {date:date},
        function(data){
            $("#TopHosts").html(data);
        }        
    );

    $.getJSON(
        base_url + 'index.php/index/GetRcvdSent_day',
        {date:date},
        function(data){
           console.log(data);
           Morris.Donut({
              element: 'donut-rcvd-sent',
              data: data
            });
        }        
    );

    $.getJSON(
        base_url + 'index.php/index/GetInternetInternal_day',
        {date:date},
        function(data){
           console.log(data);
           Morris.Donut({
              element: 'donut-internet-internal',
              data: data
            });
        }        
    );

    // Recuperation des jours du mois ou il y a des stats
    $.get(
        base_url + 'index.php/index/GetDays',
        {date:date},
        function(data){
            $("#days_list").html(data);
        }        
    );   

    $(document).on('change', '#days_list',function(){
        if($('#days_list').val() != ''){
           window.location.href = base_url + 'index.php/index/Resume_day/'+ $('#days_list').val(); 
       }       
    });
})
{/literal}
</script>