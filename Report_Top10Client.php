<?php
session_start();
?>

<html manifest="offline.manifest">
	<head>
		<title>Clients Report</title>
                <p id="status">Online</p>
		<link rel="stylesheet" type="text/css" href="style/menubar.css">
		<style>
			[data-slots] { font-family: monospace }
		</style>
                
                 <script type="text/javascript" src="jquery-1.4.min.js"></script>
                <script type="text/javascript" src="offline.js"></script>
                
		<?php
			include "dbConnections.php";

			$sql = "SELECT tblinv_info.client_id,clientInfo.c_name,clientInfo.c_surname,count(tblinv_info.Client_id) as FREQUENCY FROM `tblinv_info`
					Inner join tblclientinfo clientInfo on clientInfo.client_Id = tblinv_info.Client_id
					WHERE tblinv_info.Inv_Date>='2018-01-01' and tblinv_info.Inv_Date<'2020-01-01'
					GROUP By tblinv_info.Client_id
					Order By FREQUENCY DESC
					LIMIT 10";
			
			
			$result = $conn->query($sql);
			  
			
		?>
	</script>
	</head>
	
	<body>
	<?php
		if(!isset($_SESSION['loggedIn']))
		{	
			header('Location: ManageSupplement.php');
		}
		include "menu.php";
	?>
	<center>
	<h1>Top 10 Clients Report</h1>
        <h2>These are our most frequently buying clients!!</h2>
	
	<?php
	
		$stats = "";
		if (isset($result) && isset($result->num_rows) && $result->num_rows > 0) {
		  // output data of each row
		  echo "<table border='1'>";
		  echo "<tr>";
		  echo "<th>CLIENT</th><th>FREQUENCY</th>";
		  echo "</tr>";
		  while($row = $result->fetch_assoc()) {
			if(!empty($stats))
			{
				$stats= $stats.',';
			}
			
			$stats=$stats."['".$row["c_name"]."',".$row["FREQUENCY"]."]";
			echo "</tr>";
			echo "<td>".$row["client_id"]." ".$row["c_name"]." ".$row["c_surname"]."</td><td>".$row["FREQUENCY"]."</td>";
			echo "</tr>";
		  }
		  echo "</table>";
		} else {
		  echo "0 Clients";
		}
		$conn->close();
	?>
	
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
		// Load the Visualization API and the corechart package.
		google.charts.load('current', {'packages':['corechart']});
		// Set a callback to run when the Google Visualization API is loaded.
		google.charts.setOnLoadCallback(drawChart);
		// Callback that creates and populates a data table,
		// instantiates the bar chart, passes in the data and
		// draws it.
		function drawChart() {
		// Create the data table.
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Date');
		data.addColumn('number', '# of Purchase');
		data.addRows([
		<?php
			echo $stats;
		?>
		]);
		// Set chart options
		var options = {
		'title':'Top 10 Clients',
		'width':600,
		'height':500};
		// Instantiate and draw our chart, passing in some options.
		var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
		chart.draw(data, options);
		}
		</script>
	<div id="chart_div"></div>
	</center>
	</body>
</html>