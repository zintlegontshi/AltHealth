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
			
			$sql = "SELECT Client_ID as 'CLIENT', C_Tel_H AS 'HOME', C__Tel_W AS 'WORK', C_Tel_Cell AS 'CELL', C_Email AS 'EMAIL'  
					FROM `tblclientinfo`
					Where C_Email ='' and C_Tel_Cell =''";
			
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
	<h1>Client Information Report</h1>
        <h2>This is a list of all clients with incomplete contact information!</h2> 
	<?php
		if (isset($result) && isset($result->num_rows) && $result->num_rows > 0) {
		  // output data of each row
		  echo "<table border='1'>";
		  echo "<tr>";
		  echo "<th>CLIENT</th><th>HOME</th><th>WORK</th><th>CELL</th><th>EMAIL</th>";
		  echo "</tr>";
		  while($row = $result->fetch_assoc()) {
			echo "</tr>";
			echo "<td>".$row["CLIENT"]."</td><td>".$row["HOME"]."</td><td>".$row["WORK"]."</td><td>".$row["CELL"]."</td><td>".$row["EMAIL"]."</td>";
			echo "</tr>";
		  }
		  echo "</table>";
		} else {
		  echo "0 Clients";
		}
		$conn->close();
	?>
	</center>
	</body>
</html>