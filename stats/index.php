<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Dendory Network</title>
		<link href="bootstrap.min.css" rel="stylesheet" />
		<script src="jquery-min.js"></script>
		<script src="bootstrap.min.js"></script>
		<link href="font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="jquery.dataTables.css">
		<script type="text/javascript" charset="utf8" src="jquery.dataTables.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="navbar-nav-scroll">
				<h3>Web stats</h3>
			</div>

			<table class='table table-striped' id='tracking'>
				<thead><tr><th>Time</th><th>Source</th><th>Destination</th><th>Referer</th><th><center>Ad</center></th></tr></thead>
				<tbody>
<?php
$db = new mysqli("localhost", "my_user", "my_password", "tracking") or die("Could not connect to database!");
$results = $db->query("SELECT * FROM log ORDER BY date DESC;");

$days = array();
$curday = "";
$curcount = 0;
$src = array('google' => 0, 'twitter' => 0, 'facebook' => 0, 'linkedin' => 0, 'other' => 0, 'internal' => 0, 'organic' => 0);
$dst = array('the-voip-pack' => 0, 'the-data-pack' => 0, 'the-cloud-audit-pack' => 0, 'the-devops-pack' => 0, 'the-startup-pack' => 0, 'main' => 0, 'other' => 0);
while($result = $results->fetch_assoc())
{
	if($curday != explode(" ", $result['date'])[0])
	{
		if($curday != "")
		{
			array_push($days, array('date' => $curday, 'hits' => $curcount));
		}
		$curcount = 0;
		$curday = explode(" ", $result['date'])[0];
	}
	$curcount = $curcount + 1;
	echo "  <tr><td>" . $result['date'] . "</td><td><i class='fa fa-" . $result['browser'] . "'></i> " . $result['ip'] . "</td><td>" . $result['url'] . "</td><td>" . $result['ref'] . "</td><td><center>";
	if(strpos($result['query'], 'gclid=') !== false) { echo "<i class='fa fa-google'></i>"; }
	if(strpos($result['query'], 'lrsc=') !== false) { echo "<i class='fa fa-linkedin'></i>"; }
	if(strpos($result['query'], 'fbclid=') !== false) { echo "<i class='fa fa-facebook'></i>"; }
	echo "</center></td></tr>\n";
	if(strpos($result['ref'], 'google.com') !== false) { $src['google'] = $src['google'] + 1; }
	else if(strpos($result['ref'], 'linkedin.com') !== false) { $src['linkedin'] = $src['linkedin'] + 1; }
	else if(strpos($result['ref'], 't.co') !== false) { $src['twitter'] = $src['twitter'] + 1; }
	else if(strpos($result['ref'], 'facebook') !== false) { $src['facebook'] = $src['facebook'] + 1; }
	else if(strpos($result['ref'], 'dendory.ca') !== false) { $src['internal'] = $src['internal'] + 1; }
	else if($result['ref'] == "") { $src['organic'] = $src['organic'] + 1; }
	else { $src['other'] = $src['other'] + 1; }
	if(strpos($result['url'], 'the-startup-pack') !== false) { $dst['the-startup-pack'] = $dst['the-startup-pack'] + 1; }
	else if(strpos($result['url'], 'the-voip-pack') !== false) { $dst['the-voip-pack'] = $dst['the-voip-pack'] + 1; }
	else if(strpos($result['url'], 'the-data-pack') !== false) { $dst['the-data-pack'] = $dst['the-data-pack'] + 1; }
	else if(strpos($result['url'], 'the-devops-pack') !== false) { $dst['the-devops-pack'] = $dst['the-devops-pack'] + 1; }
	else if(strpos($result['url'], 'the-cloud-audit-pack') !== false) { $dst['the-cloud-audit-pack'] = $dst['the-cloud-audit-pack'] + 1; }
	else if($result['url'] == "/") { $dst['main'] = $dst['main'] + 1; }
	else {$dst['other'] = $dst['other'] + 1; }
}
array_push($days, array('date' => $curday, 'hits' => $curcount));
$db->close();
?>
				</tbody>
			</table>
			<script>$(document).ready(function(){$('#tracking').DataTable({'order':[[0,'desc']]});});</script>
			<br>
			<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
			<div id="chart_div"></div>
			<script>
				google.charts.load('current', {packages: ['corechart', 'line']});
				google.charts.setOnLoadCallback(drawCurveTypes);
				function drawCurveTypes()
				{
					var data = google.visualization.arrayToDataTable([
					['Date', 'Hits'],
<?php
foreach($days as $record)
{
	echo "[new Date(" . explode('-', $record['date'])[0] . ", " . intval(explode('-', $record['date'])[1]-1) . ", " . intval(explode('-', $record['date'])[2]) . "), " . $record['hits'] . "],\n";
}
?>
					]);
					var options = { title: 'Number of hits per day' };
					var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
					chart.draw(data, options);
				}
			</script>
			<br>
			<div class="row">
				<div class="col-lg-6">
					<div id="src_div"></div>
					<script>
						google.charts.load('current', {'packages':['corechart']});
						google.charts.setOnLoadCallback(drawChartSrc);
						function drawChartSrc()
						{
							var data2 = google.visualization.arrayToDataTable([
							['Source', 'Hits'],
<?php
foreach($src as $k => $v)
{
	echo "['" . $k . "', " . $v . "],\n";
}
?>
							]);
							var options2 = { title: 'Traffic sources' };
							var chart2 = new google.visualization.PieChart(document.getElementById('src_div'));
							chart2.draw(data2, options2);
						}
					</script>
				</div>
				<div class="col-lg-6">
					<div id="dst_div"></div>
					<script>
						google.charts.load('current', {'packages':['corechart']});
						google.charts.setOnLoadCallback(drawChartDst);
						function drawChartDst()
						{
							var data3 = google.visualization.arrayToDataTable([
							['Destination', 'Hits'],
<?php
foreach($dst as $k => $v)
{
	echo "['" . $k . "', " . $v . "],\n";
}
?>
							]);
							var options3 = { title: 'Destination pages' };
							var chart3 = new google.visualization.PieChart(document.getElementById('dst_div'));
							chart3.draw(data3, options3);
						}
					</script>
				</div>
				<div class="row">
					<p><center><font size=-1><i>This content is confidential. Unauthorized use is prohibited.</i></font></center></p>
				</div>
			</div>
		</div>
	</body>
</html>


