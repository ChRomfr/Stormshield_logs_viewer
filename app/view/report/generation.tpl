{* app/view/report/generation.tpl *}
<ul class="breadcrumb">
	<li><a href="{$Helper->getLink("index")}" title="Accueil">Accueil</a><span class="divider">&gt;&gt;</span></li>
	<li class="active">Gestion</li>
</ul>

<div class="well">
	<h4>Top sites</h4>
	<div>
		<input type="text" id="date" placeholder="2013-07" />
		<button type="button" onclick="regeneratetopsites();" class="btn">Regenerer top sites</button>
	</div>
	<div id="action-result"></div>
</div>

<script type="text/javascript">
<!--
function regeneratetopsites(){
	var date = $("#date").val();

	if( date != ""){
		$('#action-result').html('<div class="text-center" style="width:400px;"><div class="progress progress-striped active"><div class="bar" style="width:0px;"></div></div>Chargement ...</div>');
		$.get(
	        '{$Helper->getLink("report/ajaxregeneratetopsite/'+ date +'")}',{literal}
	        {nohtml:'nohtml'},
	        function(data){
	        	$("#action-result").html("Generation terminée");        	
	        }
	    );
	}else{
		alert('Veuillez indiquer la date');
	}
	
}{/literal}

jQuery(function($){
   $("#date").mask("9999-99");

   var progress = setInterval(function() {
	    var $bar = $('.bar');
	    
	    if ($bar.width()==400) {
	        //clearInterval(progress);
	        $bar.width(0);
	        //$('.progress').removeClass('active');
	    } else {
	        $bar.width($bar.width()+40);
	    }
	    //$bar.text($bar.width()/4 + "%");
	}, 800);

});
//-->
</script>