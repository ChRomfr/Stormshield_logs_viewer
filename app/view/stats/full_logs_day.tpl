<div class="well">
	<div id="fulllogs">
		<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div>
	</div>
</div>
<script type="text/javascript">
var date = '{$date}'
$(document).ready(function(){
	paramUrl = getParameters();
	$.get(base_url + 'index.php/stats/GetFullLogsDay/'+date,paramUrl, function(data){
    $('#fulllogs').html(data);
  });
});
</script>