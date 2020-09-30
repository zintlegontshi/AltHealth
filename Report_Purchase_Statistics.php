<?php
session_start();
?>

<html manifest="offline.manifest">
	<head>
		<title>Purchase Stats</title>
                <p id="status">Online</p>
		<link rel="stylesheet" type="text/css" href="style/menubar.css">
                
		 <script type="text/javascript" src="jquery-1.4.min.js"></script>
                <script type="text/javascript" src="offline.js"></script>
                
		<?php
			include "dbConnections.php";

			$sql = "SELECT COUNT(Inv_Num) AS 'NUM OF PURCHASE', MONTHNAME(Inv_Date) AS 'MONTH' 
					FROM `tblinv_info`
					WHERE Inv_Date>='2012-01-01'
					GROUP BY MONTH
					ORDER BY DATE_FORMAT(Inv_Date,'%m') ASC";
			
			
			$result = $conn->query($sql);
			  
			
		?>
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
	<h1>Purchase Statistics Report</h1>
	<?php
		$stats = "";
		if (isset($result) && isset($result->num_rows) && $result->num_rows > 0) {
		  // output data of each row
		  echo "<table border='1'>";
		  echo "<tr>";
		  echo "<th>NUM OF PURCHASE</th><th>MONTH</th>";
		  echo "</tr>";
		  while($row = $result->fetch_assoc()) {
			if(!empty($stats))
			{
				$stats= $stats.',';
			}
			
			$stats=$stats."['".$row["MONTH"]."',".$row["NUM OF PURCHASE"]."]";
			
			echo "</tr>";
			echo "<td>".$row["NUM OF PURCHASE"]."</td><td>".$row["MONTH"]."</td>";
			echo "</tr>";
		  }
		  echo "</table>";
		} else {
		  echo "0 Clients";
		}
		$conn->close();
	?>
	<br>
	
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
		// This is where you will need to pass your SQL data to JavaScript
		// I have not included this information, if needed, ask
		data.addRows([
		<?php
			echo $stats;
		?>
		]);
		// Set chart options
		var options = {
		'title':'Purchase Per Month',
		'width':600,
		'height':500};
		// Instantiate and draw our chart, passing in some options.
		var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
		chart.draw(data, options);
		}
		</script>
	<div id="chart_div"></div>
	</center>
	</body>
</html>