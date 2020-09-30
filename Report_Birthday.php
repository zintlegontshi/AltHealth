<?php
session_start();
?>

<html manifest="offline.manifest">
	<head>
		<title>Clients</title>
                <p id="status">Online</p>
		<link rel="stylesheet" type="text/css" href="style/menubar.css">
		<style>
			[data-slots] { font-family: monospace }
		</style>
                
                <script type="text/javascript" src="jquery-1.4.min.js"></script>
                <script type="text/javascript" src="offline.js"></script>
                
		<?php
			include "dbConnections.php";

			$sql = "SELECT Client_Id AS 'CLIENT ID', concat(c_name,' ',c_surname) AS 'CLIENT NAME' 
					FROM `tblclientinfo` 
					WHERE substring(Client_id,3,4) = DATE_FORMAT(now(),'%m%d')";
			
			
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
	<h1>Today's Birthdays Report</h1>
	<?php
		if (isset($result) && isset($result->num_rows) && $result->num_rows > 0) {
		  // output data of each row
		  echo "<table border='1'>";
		  echo "<tr>";
		  echo "<th>CLIENT ID</th><th>CLIENT NAME</th>";
		  echo "</tr>";
		  while($row = $result->fetch_assoc()) {
			echo "</tr>";
			echo "<td>".$row["CLIENT ID"]."</td><td>".$row["CLIENT NAME"]."</td>";
			echo "</tr>";
		  }
		  echo "</table>";
		} else {
		  echo "0 Clients with birthdays today";
		}
		$conn->close();
	?>
	</center>
	</body>
</html>