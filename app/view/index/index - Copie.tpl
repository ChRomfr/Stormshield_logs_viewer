<div style="height:10px;"></div>

<div class="row-fluid">
	<div class="span5 well">
		<div id="stats-1">
			<div class="text-center">
			    <div class="progress progress-striped active">
			    	<div class="bar" style="width: 0%;"></div>
			    </div>
			    Chargement ...
			</div>
		</div>
	</div>
	
	<div class="span5 well">
		<div id="stats-2">
			<div class="text-center">
			    <div class="progress progress-striped active">
			    	<div class="bar" style="width: 0%;"></div>
			    </div>
			    Chargement ...
			</div>
		</div>
	</div>
</div>

<div class="row-fluid">
	<div class="span5 well">
		<div id="stats-3">
			<div class="text-center">
			    <div class="progress progress-striped active">
			    	<div class="bar" style="width: 0%;"></div>
			    </div>
			    Chargement ...
			</div>
		</div>
	</div>

	{* Calendrier *}
	<div class="span5 well">
		<div id="stats-4"></div>
	</div>
</div>

<script type="text/javascript">
<!--
var date = "{$date_of_php}";

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
        '{$Helper->getLink("index/ajaxGeMonthDayCumul")}',
        {
        	nohtml:'nohtml',
        	date:'{$date_of_php}',
        },{literal}        
        function(data){
        	$('#stats-1').html('<table id="table-stats-1" class="table table-condensed table-striped"><thead><tr><th>Date</th><th>Traffic</th><th>Hits</th></tr></thead><tbody></tbody></table>');
        	var tpl = '<tr><td><a href="javascript:goto_url(\'report/index?date={{date}}\');" title="Detail {{date}}">{{date}}</td><td>{{cumul_format}}</td><td>{{hits}}</td></tr>';
        	for( var i in data ){      
            	$('#table-stats-1').append( Mustache.render(tpl, data[i]) );
        	}
			get_top_site_month();
        },'json'
    );{/literal} 
});

function goto_url(url_param){
	window.location.href = '{$Helper->getLink("'+url_param+'")}';
}

function get_top_user_month(){
	$.get(
        '{$Helper->getLink("index/ajaxGetTopUserOfMonth")}',
        {
        	nohtml:'nohtml',
        	date:'{$date_of_php}',
        },{literal}        
        function(data){
        	$('#stats-2').html('<table id="table-stats-2" class="table table-condensed table-striped"><thead><tr><th>Utilisateur</th><th>Traffic</th></tr></thead><tbody></tbody></table>');
        	var tpl = '<tr><td><a href="#" title="">{{ip}}</td><td>{{cumul_formated}}</td></tr>';
        	for( var i in data ){      
            	$('#table-stats-2').append( Mustache.render(tpl, data[i]) );
        	}
			get_calendar()
        },'json'
    );{/literal}
}

function get_top_site_month(){
	 $.get(
        '{$Helper->getLink("index/ajaxGetTopSiteOfMonth2")}',
        {
        	nohtml:'nohtml',
        	date:'{$date_of_php}',
        },{literal}        
        function(data){
        	$('#stats-3').html('<table id="table-stats-3" class="table table-condensed table-striped"><thead><tr><th>Url</th><th>Traffic</th><th>Hits</th></tr></thead><tbody></tbody></table>');
        	var tpl = '<tr><td><a href="#" title="">{{url}}</td><td>{{cumul_formated}}</td><td>{{hits}}</td></tr>';
        	for( var i in data ){      
            	$('#table-stats-3').append( Mustache.render(tpl, data[i]) );
        	}
			get_top_user_month()
        },'json'
    );{/literal}
}

function get_calendar(){
	$.getJSON(
		'{$Helper->getLink("report/getdataforcalendar")}', {literal}
		{nohtml:'nohtml'},{/literal}   
		function(data){ 
			
			$(document).ready(function() {
		
				$('#stats-4').fullCalendar({
					header: {
						left: 'prev',
						center: 'title',
						right: 'next'
					},
					firstDay:1,
					editable: false,
					events:data,		
				});	
			}); 		
		}
	);
}

//-->
</script>