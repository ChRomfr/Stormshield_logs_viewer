{strip}
<ul class="breadcrumb">
	<li><a href="{$Helper->getLink("index")}" title="Accueil">Accueil</a><span class="divider">&gt;&gt;</span></li>
	<li><a href="{$Helper->getLink("report/index?date={$date}")}" title="Detail {$date}">Détail {$date}</a><span class="divider">&gt;&gt;</span></li>
	<li class="active">{$domain}</li>
</ul>

<h4 class="well">{$domain}</h4>

<div class="row-fluid">
	<div class="span5 well" id="detail-domain-cumul">
		<div class="text-center">
		    <div class="progress progress-striped active">
		    	<div class="bar" style="width: 0%;"></div>
		    </div>
		    Chargement ...
		</div>
	</div>{* /span5 *}

	<div class="span5 well" id="detail-domain-user">
		<div class="text-center">
		    <div class="progress progress-striped active">
		    	<div class="bar" style="width: 0%;"></div>
		    </div>
		    Chargement ...
		</div>
	</div>{* /span5 *}

</div>{* /row-fluid *}
{/strip}
<script type="text/javascript">
<!--
jQuery(document).ready(function(){
	var progress = setInterval(function() {
	    var $bar = $('.bar');
	    
	    if ($bar.width()==400) {
	        clearInterval(progress);
	        $('.progress').removeClass('active');
	    } else {
	        $bar.width($bar.width()+40);
	    }
	    //$bar.text($bar.width()/4 + "%");
	}, 800);

	$.get(
        '{$Helper->getLink("report/ajaxGetDomainUrlCumul")}',
        {
        	nohtml:'nohtml',
        	date:'{$date}',
        	domain:'{$domain}'
        },{literal}        
        function(data){
        	$('#detail-domain-cumul').html('<table id="table-detail-domain-cumul" class="table table-condensed table-striped"><thead><tr><th>Url</th><th>Bytes</th><th>Volume</th></tr></thead><tbody></tbody></table>');
        	var tpl = '<tr><td><a href="{{url}}" targer="_blank">{{url_short}}</a></td><td>{{cumul}}</td><td>{{cumul_formated}}</td></tr>';
        	for( var i in data ){      
            	$('#table-detail-domain-cumul').append( Mustache.render(tpl, data[i]) );
        	}
        },'json'
    );
	{/literal}

	$.get(
        '{$Helper->getLink("report/ajaxGetDomainUserCumul")}',
        {
        	nohtml:'nohtml',
        	date:'{$date}',
        	domain:'{$domain}'
        },{literal}        
        function(data){
        	$('#detail-domain-user').html('<table id="table-detail-domain-user" class="table table-condensed table-striped"><thead><tr><th>IP</th><th>Bytes</th><th>Volume</th></tr></thead><tbody></tbody></table>');
        	var tpl = '<tr><td>{{ip}}</td><td>{{cumul}}</td><td>{{cumul_formated}}</td></tr>';
        	for( var i in data ){      
            	$('#table-detail-domain-user').append( Mustache.render(tpl, data[i]) );
        	}
        },'json'
    );
	{/literal}
});
//->
</script>