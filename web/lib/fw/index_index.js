$(document).ready(function(){	
	// Recuperation stats domain
	$.get(
        base_url + 'index.php/index/GetTopDomainReceive',
        {nohtml:'nohtml', date:date},
        function(data){
            $("#TopDomainsReceive").html(data);
        }        
    );

    $.get(
        base_url + 'index.php/index/GetTopDomainSent',
        {nohtml:'nohtml', date:date},
        function(data){
            $("#TopDomainsSent").html(data);
        }        
    );

    $.get(
        base_url + 'index.php/index/GetTopUsers',
        {nohtml:'nohtml', date:date},
        function(data){
            $("#TopUsers").html(data);
        }        
    );

    $.get(
        base_url + 'index.php/index/GetTopHosts',
        {date:date},
        function(data){
            $("#TopHosts").html(data);
        }        
    );

    // Generation graph rcdv/sent
    $.getJSON(
        base_url + 'index.php/index/GetRcvdSent',
        {date:date},
        function(data){
           //console.log(data);
           $.plot('#donut-rcvd-sent', data, {
                series: {
                    pie: {
                        innerRadius: 0.5,
                        show: true,
                        label:{
                            show:true,
                            radius:3/4,
                            formatter: labelFormatter,
                            threshold: 0.1,
                            background:{opacity:0.5}
                        },
                    }
                },
                legend: {
                    show: false
                }
            });
        }// end function(data)        
    );

    $.getJSON(
        base_url + 'index.php/index/GetInternetInternal',
        {date:date},
        function(data){
           //console.log(data);
           $.plot('#donut-internet-internal', data, {
                series: {
                    pie: {
                        innerRadius: 0.5,
                        show: true,
                        label:{
                            show:true,
                            radius:3/4,
                            formatter: labelFormatter,
                            threshold: 0.1,
                            background:{opacity:0.5}
                        },
                    }
                },
                legend: {
                    show: false
                }
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

    $.getJSON(
        base_url + 'index.php/index/GetDataTraffic',
        {date:date},
        function(data){

            $(function() {
                // Delete loader
                $("#graph-traffic").html('');
                // Generation du graph
                $.plot("#graph-traffic", [
                     { data: data[0], label: "Traffic total" } ,
                     { data: data[1], label: "Traffic internet" },
                     { data: data[2], label: "Traffic interne (VPN,VLAN,...)"},
                ],{
                    series:{
                        lines:{
                            show:true,
                        },
                        points:{
                            show:true,
                        }
                    },
                    grid:{
                        hoverable: true,
                        clickable: true
                    },
                    xaxis:{
                        mode:"categories",
                        tickLength:0
                    }
                });

                $("<div id='tooltip'></div>").css({
                    position: "absolute",
                    display: "none",
                    border: "1px solid #fdd",
                    padding: "2px",
                    "background-color": "#fee",
                    opacity: 0.80
                }).appendTo("body");

                $("#graph-traffic").bind("plothover", function (event, pos, item) {
                    
                        var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";
                        $("#hoverdata").text(str);                
                    
                        if (item) {
                            var x = item.datapoint[0].toFixed(2),
                                y = item.datapoint[1].toFixed(0);

                            $("#tooltip").html(item.series.label + " " + y + " MO")
                                .css({top: item.pageY+5, left: item.pageX+5})
                                .fadeIn(200);
                        } else {
                            $("#tooltip").hide();
                        }                    
                });

                $("#graph-traffic").bind("plotclick", function (event, pos, item) {
                    if (item) {
                        $("#clickdata").text(" - click point " + item.dataIndex + " in " + item.series.label);
                        plot.highlight(item.series, item.datapoint);
                    }
                });


            });// end function        
        }        
    );// end getJSON

    $.getJSON(
        base_url + 'index.php/index/GetPortInternet',
        {date:date},
        function(data){
           //console.log(data);
           $.plot('#graph-internet-ports', data, {
                series: {
                    pie: {
                        show: true,
                        //radius:1,
                        innerRadius: 0.5,
                        label:{
                            show:true,
                            radius:3/4,
                            formatter: labelFormatter,
                            threshold: 0.1,
                            background:{opacity:0.5}
                        },
                    }
                },
                legend: {
                    show: false
                }
            });
        }             
    );// end getjson

    // Recuperation des stats traffic pour ligne (graph)

    $(document).on('change', '#days_list',function(){
        date = $('#days_list').val();
        if(date !=''){
            window.location.href = base_url + 'index.php/index/Resume_day/'+ date;
        }
       
    });
})

function labelFormatter(label, series) {
    return "<div style='font-size:8pt; text-align:center; padding:2px; color:black;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
}