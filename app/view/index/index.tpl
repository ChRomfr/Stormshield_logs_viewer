{strip}
<h3>Statistique for <strong>{$date}</strong></h3>
<hr/>

<div class="container">
    <div class="row">
        <div class="pull-left col-sm-3">
            <a href="{$Helper->getLink("index/index?date={$prev_month}")}" class="btn btn-default"><< {$prev_month}</a>
        </div>
        
        <div class="col-sm-3">
            <select id="days_list" class="form-control"></select>
        </div>
        <div class="pull-right col-sm-3">
            <a href="{$Helper->getLink("index/index?date={$next_month}")}" class="btn btn-default">{$next_month} >></a>
        </div>
    </div>
</div>
<hr/>

<div class="row">
    <div class="well" class="col-md-12 col-sm-12">
        <h4>Traffic</h4>
        <hr/>
        <div id="graph-traffic" style="height:300px;"><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>
    </div>{* /.well *}
</div>

<div class="row">
    <div class="col-md-3 col-sm-3 well"><h4>Receive/Sent %</h4><hr/><div class="text-center" style="height:328px;" id="donut-rcvd-sent"></div></div>
    <div class="col-md-3 col-sm-3 well col-md-offset-1"><h4>Internet/VPN-VLAN %</h4><hr/><div class="text-center" style="height:328px;" id="donut-internet-internal"></div></div>
    <div class="col-md-3 col-sm-3 well col-md-offset-1" id="internet-ports"><h4>Ports</h4><hr/><div class="text-center" style="height:328px;" id="graph-internet-ports"></div></div>
</div>

<div class="row">
	<div class="col-md-5 col-sm-5 well" id="TopDomainsReceive" style="max-height:350px; overflow: auto;"><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>
	<div class="col-md-5 col-sm-5 well col-md-offset-1" id="TopDomainsSent" style="max-height:350px; overflow: auto;"><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>
</div>

<div class="row">
	<div class="col-md-5 col-sm-5 well" id="TopUsers" style="max-height:350px; overflow: auto;"><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>
    <div class="col-md-5 col-sm-5 well col-md-offset-1" id="TopHosts" style="max-height:350px; overflow: auto;"><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>
</div>


{/strip}

<script type="text/javascript">var date = "{$date}";</script>