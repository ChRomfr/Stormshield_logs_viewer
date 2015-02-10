<ul class="breadcrumb">
	<li><a href="{$Helper->getLink("index")}" title="Accueil">Accueil</a><span class="divider">&gt;&gt;</span></li>
	<li class="active">Detail {$date}</li>
</ul>

<div class="well">
	<h3>Statistique du {$date}</h3>
</div>

	<div class="row-fluid">
		<div class="span5">
			<div id="topusers" class="well">
				<div class="text-center">
				    <div class="progress progress-striped active">
				    	<div class="bar" style="width: 0%;"></div>
				    </div>
				    Chargement ...
				</div>
			</div>
			
			<div id="resumesites" class="well">
				<div class="text-center">
				    <div class="progress progress-striped active">
				    	<div class="bar" style="width: 0%;"></div>
				    </div>
				    Chargement ...
				</div>
			</div>
			
		</div><!-- /span5 -->

		<div class="span5 well">
			<div id="topdomains">
				<div class="text-center">
				    <div class="progress progress-striped active">
				    	<div class="bar" style="width: 0%;"></div>
				    </div>
				    Chargement ...
				</div>
			</div>
		</div><!-- /span5 -->
	</div><!-- /row-fluid -->
	
	<div class="row-fluid">
		<div class="span5 well">
			
		</div><!-- /span5 -->

		<!--<div class="span5 well">
			<div id="topdomains">
				<div class="text-center">
				    <div class="progress progress-striped active">
				    	<div class="bar" style="width: 0%;"></div>
				    </div>
				    Chargement ...
				</div>
			</div>-->
		</div><!-- /span5 -->
	</div><!-- /row-fluid -->
	



<script type="text/javascript">
<!--
$(window).load(function(){
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
        '{$Helper->getLink("report/ajaxtopuserday/{$date}")}',{literal}
        {nohtml:'nohtml'},{/literal}
        function(data){
        	$("#topusers").html(data);        	
        }
    );

    $.get(
        '{$Helper->getLink("report/ajaxtopdomainsday/{$date}")}',{literal}
        {nohtml:'nohtml'},{/literal}
        function(data){
        	$("#topdomains").html(data);        	
        }
    );
	
	$.get(
        '{$Helper->getLink("report/ajaxsresumesites/{$date}")}',{literal}
        {nohtml:'nohtml'},{/literal}
        function(data){
        	$("#resumesites").html(data);        	
        }
    );
});

//-->
</script>