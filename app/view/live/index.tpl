<ul class="breadcrumb">
    <li><a href="{$Helper->getLink("index")}" title="Accueil">Accueil</a><span class="divider">&gt;&gt;</span></li>
    <li class="active">Stats temp reel : {$site.name}</li>
</ul>

<div class="well">
	<h3>{$site.name}</h3>
	<div id="status">
    </div>
</div>

<script type="text/javascript">
<!--
$(window).load(function(){
    $("#status").html('<div class="text-center"><div class="progress progress-striped active"><div class="bar" style="width: 25%;"></div></div>Recuperation des logs ...</div>');
	$.get(
        '{$Helper->getLink("live/getfile/{$site.id}")}',{literal}
        {nohtml:'nohtml'},
        function(data){
        	$("#status").html('<div class="text-center"><div class="progress progress-striped active"><div class="bar" style="width: 50%;"></div></div>Construction de la table temporaire</div>');
        	ajaxconstructdb();
        }
    );
});
{/literal}

function ajaxconstructdb(){
	$.get(
        '{$Helper->getLink("live/constructTableTmp")}',{literal}
        {nohtml:'nohtml'},
        function(data){
        	console.log("Construction Db OK");
            $('#status').html('<div class="text-center"><div class="progress progress-striped active"><div class="bar" style="width: 75%;"></div></div>Generation des stats ...</div>');
            ajaxstatindex();
        }
    );
    {/literal}
}

function ajaxstatindex(){    
    $.get(
        '{$Helper->getLink("live/stats")}',{literal}
        {nohtml:'nohtml'},
        function(data){
            console.log("Generation OK");
            $('#status').html(data);
        }
    );
    {/literal}
}

function detailuserbyip(ip){
    $('#status').html('Recuperation des donnees pour '+ ip);

    $.get(
        '{$Helper->getLink("live/detailbyip")}',{literal}
        {nohtml:'nohtml', ip:ip},
        function(data){
                     $('#status').html('<h4>Detail de : '+ip+'</h4><div class="pull-right"><a href="javascript:ajaxstatindex();" class="btn">Retour</a></div><div class="clearfix"></div><table class="table table-condensed table-striped" id="tdetailbyip"><thead><tr><th>Site</th><th>Hits</th><th>Bytes</th><th>Bytes</th></thead><tbody></tbody></table>');
           
            var tpl = '<tr><td><a href="#" title="">{{url}}</td><td>{{hits}}</td><td>{{cumul_formated}}</td><td>{{bytes}}</tr>';
            for( var i in data ){      
                $('#tdetailbyip').append( Mustache.render(tpl, data[i]) );
            }

            $("#tdetailbyip").tablesorter();
            
        },'json'
    );
    {/literal} 
    
}
//-->
</script>

