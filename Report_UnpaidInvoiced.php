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

			$sql = "SELECT inv.Client_Id as 'CLIENT ID',concat(cl.C_name,' ',cl.C_surname) as 'CLIENT', inv.Inv_Num as 'INVOICE NUMBER', inv.Inv_Date as 'INVOICE DATE' FROM `tblinv_info` inv
					INNER JOIN tblclientinfo cl on inv.Client_id = cl.Client_Id 
					WHERE Inv_Date<= '2020-01-01' AND Inv_Paid='N'
					ORDER BY inv.Client_Id ASC, `Client` ASC, inv.Inv_Num ASC";
			
			
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
	<h1>Unpaid Invoices Report</h1>
	<?php
		if (isset($result) && isset($result->num_rows) && $result->num_rows > 0) {
		  // output data of each row
		  echo "<table border='1'>";
		  echo "<tr>";
		  echo "<th>CLIENT ID</th><th>CLIENT</th><th>INVOICE NUMBER</th><th>INVOICE DATE</th>";
		  echo "</tr>";
		  while($row = $result->fetch_assoc()) {
			echo "</tr>";
			echo "<td>".$row["CLIENT ID"]."</td><td>".$row["CLIENT"]."</td><td>".$row["INVOICE NUMBER"]."</td><td>".$row["INVOICE DATE"]."</td>";
			echo "</tr>";
		  }
		  echo "</table>";
		} else {
		  echo "0 Clients";
		}
		$conn->close();
	?></center>
	</body>
</html>