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

			$sql = "SELECT Supplement_Description as 'SUPPLEMENT',concat(supplier.Supplier_Id,' ',supplier.Contact_Person,' ',supplier.Supplier_Tel) as 'SUPPLIER INFORMATION', Min_level as 'MIN LEVEL', Current_stock_levels as 'CURRENT STOCK' 
					FROM `tblsupplements` supplement
					Inner Join tblsupplier_info supplier on supplier.Supplier_Id = Supplement.Supplier_Id 
					WHERE Current_stock_levels<Min_level
					ORDER BY Supplement_Description DESC, 'SUPPLIER INFORMATION' ASC, 'MIN LEVEL' ASC, 'CURRENT STOCK' ASC";
			
			
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
	<h1>Critically Low Supplements Report</h1>
	<?php
		if (isset($result) && isset($result->num_rows) && $result->num_rows > 0) {
		  // output data of each row
		  echo "<table border='1'>";
		  echo "<tr>";
		  echo "<th>SUPPLEMENT</th><th>SUPPLIER INFORMATION</th><th>MIN LEVEL</th><th>CURRENT STOCK</th>";
		  echo "</tr>";
		  while($row = $result->fetch_assoc()) {
			echo "</tr>";
			echo "<td>".$row["SUPPLEMENT"]."</td><td>".$row["SUPPLIER INFORMATION"]."</td><td>".$row["MIN LEVEL"]."</td><td>".$row["CURRENT STOCK"]."</td>";
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