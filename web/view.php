<html>
  <head>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript">
	
	var data = null;
	var chart = null;
	var options = null;

    google.charts.load('current', {'packages':['corechart', 'line']});
    google.charts.setOnLoadCallback(drawChart);
      
    function drawChart() {
		var jsonData = $.ajax({
			url: "getData.php",
			dataType: "json",
			async: false
			}).responseText;
          
		data = new google.visualization.DataTable(jsonData);
		var date_formatter = new google.visualization.DateFormat({ pattern: "yyyy-MM-dd HH:mm" });
		date_formatter.format(data, 0);

        options = {
			aggregationTarget: 'category',
			legend: 'none',
			title: 'Today',
			connectSteps: false,
			vAxis: {
				minValue: 0
			},
			hAxis: {
				format: 'MM-dd HH:mm',
				slantedText: true,
				gridlines: {
					count: 24
				}
			},
			theme: 'material',
			explorer: { 
				actions: ['dragToZoom', 'rightClickToReset'],
				axis: 'horizontal',
				keepInBounds: true,
				maxZoomIn: 10.0
			}
        };

        chart = new google.visualization.SteppedAreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
	
	$( function() {
		$( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd', firstDay: 1 });
		
		$(document).on('click', '#go', function() {
			var qry = "getData.php?from=" + $("#from").val() + "&to=" + $("#from").val();
			var jsonData = $.ajax({
				url: qry,
				dataType: "json",
				async: false
				}).responseText;
			data = new google.visualization.DataTable(jsonData);
			var date_formatter = new google.visualization.DateFormat({ pattern: "yyyy-MM-dd HH:mm" });
			date_formatter.format(data, 0);
			options.title = "" + $("#from").val();
			chart.draw(data, options);
		});
	} );


    </script>
  </head>

  <body>
    <p>From: <input type="text" id="from" class="datepicker"><!-- till: <input type="text" id="to" class="datepicker">--><button id="go">show</button></p>
    <div id="chart_div" style="width: 100%; height: 400px;"></div>
	<p>Zoom in by dragging or using the scroll wheel, zoom out by right-click on the chart (or with the scroll-wheel).</p>
  </body>
</html>
