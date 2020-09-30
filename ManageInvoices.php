<?php
session_start();
?>

<html manifest="offline.manifest">
	<head>
		<link rel="stylesheet" type="text/css" href="style/menubar.css">
		<title>Invoice History</title>
                <p id="status">Online</p>
		
		<script src="https://code.jquery.com/jquery-3.5.1.min.js"integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="  crossorigin="anonymous"></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
		<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
		<script>
			$(document).ready( function () {
				$('#paginated-table').DataTable();
			} );
		</script>
		
		<style>
			[data-slots] { font-family: monospace }
		</style>
                
                 <script type="text/javascript" src="jquery-1.4.min.js"></script>
                <script type="text/javascript" src="offline.js"></script>
                
		<script type="text/javascript">
			// This code empowers all input tags having a placeholder and data-slots attribute
			document.addEventListener('DOMContentLoaded', () => {
				for (const el of document.querySelectorAll("[placeholder][data-slots]")) 
				{
					const pattern = el.getAttribute("placeholder"),
						slots = new Set(el.dataset.slots || "_"),
						prev = (j => Array.from(pattern, (c,i) => slots.has(c)? j=i+1: j))(0),
						first = [...pattern].findIndex(c => slots.has(c)),
						accept = new RegExp(el.dataset.accept || "\\d", "g"),
						clean = input => {
							input = input.match(accept) || [];
							return Array.from(pattern, c =>
								input[0] === c || slots.has(c) ? input.shift() || c : c
							);
						},
						format = () => {
							const [i, j] = [el.selectionStart, el.selectionEnd].map(i => {
								i = clean(el.value.slice(0, i)).findIndex(c => slots.has(c));
								return i<0? prev[prev.length-1]: back? prev[i-1] || first: i;
							});
							el.value = clean(el.value).join``;
							el.setSelectionRange(i, j);
							back = false;
						};
					let back = false;
					el.addEventListener("keydown", (e) => back = e.key === "Backspace");
					el.addEventListener("input", format);
					el.addEventListener("focus", format);
					el.addEventListener("blur", () => el.value === pattern && (el.value=""));
				}
			});
			
			function showHideAdd() {
			  var x = document.getElementById("addEditInvoice");
			  if (x.style.display === "none") {
				x.style.display = "block";
				document.getElementById("btnShowHide").innerHTML = 'Hide';
			  } else {
				x.style.display = "none";
				document.getElementById("btnShowHide").innerHTML = 'Show';
			  }
			}
		</script>
		<?php
			include "dbConnections.php";
			
			$searchResults = null;
			
			if(!isset($_SESSION['loggedIn']))
			{
				header('Location: ManageSupplements.php');
			}
			
			if(isset($_POST['btnSelect']))
			{
				$_POST['Inv_Num'] = str_replace("_","",$_POST['Inv_Num']);
				if(isset($_POST['Inv_Num'])&& !empty($_POST['Inv_Num']))
				{
					$whereClause = " WHERE Inv_Num ='".$_POST['Inv_Num']."'";
				}
				$sql = "SELECT `Inv_Num`, `Client_id`, `Inv_Date`, `Inv_Paid`, `Inv_Paid_Date`, `Comments` FROM `tblinv_info` ".$whereClause." ORDER BY Inv_Num DESC";
				$searchResults = $conn->query($sql);
			}
			
			$sql = "SELECT `Inv_Num`, `Client_id`, `Inv_Date`, `Inv_Paid`, `Inv_Paid_Date`, `Comments` FROM `tblinv_info` ORDER BY Inv_Date DESC, Inv_Num DESC";
			
			$result = $conn->query($sql);
		?>
	</head>
	
	<body>
	<?php
		include "menu.php";
	?>
	<h1>Invoice</h1>
	<?php
		
			$displayStyle="display:none";
			$showHide="Show";
			
			$Inv_Num = "";
			$Client_id = "";
			$Inv_Date = "";
			$Inv_Paid = "";
			$Inv_Paid_Date = "";
			$Comments = "";
			if(isset($searchResults) && $row = $searchResults->fetch_assoc()) 
			{
				$Inv_Num = $row["Inv_Num"];
				$Client_id = $row["Client_id"];
				$Inv_Date = $row["Inv_Date"];
				$Inv_Paid = $row["Inv_Paid"];
				$Inv_Paid_Date = $row["Inv_Paid_Date"];
				$Comments = $row["Comments"];
				
				$displayStyle="display:block"; 
				$showHide = "Hide";
			}
			
			echo "<h3>Invoice Details:<button id='btnShowHide' onclick='showHideAdd()'>".$showHide."</button></h3>";
			echo "<div id='addEditInvoice' style='".$displayStyle."'>";
	?>
	
	
	<form method="POST" action="ManageInvoices.php">
		<table>
			<?php
			
			
				
				echo "<tr>
							<td>
								<label for='Inv_Num'>Invoice Number:</label><br>
								<input type='text' name='Inv_Num'  value='".$Inv_Num."'/>
							</td>
							<td><br>
								<input type='Submit' name='btnSelect' value='Search' formnovalidate/>
							</td>
						</tr></table>";
						
				if(isset($Inv_Num) && !empty($Inv_Num))
				{
					echo "<div name='invoiceContent' id='invoiceContent'>
						<table>	
						<tr><th>Invoice Number:</th><td>".$Inv_Num."</td></tr>
						<tr><th>Client ID:</th><td>".$Client_id."</td></tr>
						<tr><th>Date:</th><td>".$Inv_Date."</td></tr>
						<tr>
						<td><h3>Invoice Items</h3></td>
						</tr>";
						
					echo "</table>";
					echo "<table border='1'>";
					echo "<th>Supplement name</th><th>Items to checkout</th>";
					echo "</tr>";
					if(isset($Inv_Num)&&!empty($Inv_Num))
					{
						$totalCount=0;
						$invoiceTotalInclVat = 0;
						$invoiceTotalExclVat = 0;
						
						echo "<tr>";
						echo "<td>Supplement Id</td><td>Item_Price</td><td>Item_Quantity</td>";
						echo "</tr>";
					
					
						echo "<tr>";
						$sql = "SELECT `Supplement_id`, `Item_Price`, `Item_Quantity` FROM `tblinv_items` WHERE Inv_Num='".$Inv_Num."'";
						$resultInvoiceItems = $conn->query($sql);
						if (isset($resultInvoiceItems) && isset($resultInvoiceItems->num_rows) && $resultInvoiceItems->num_rows > 0) 
						{
							while($row = $resultInvoiceItems->fetch_assoc()) 
							{		
								$totalCount=$totalCount+$row['Item_Quantity'];
								$sumInclVat = ($row['Item_Price']*$row['Item_Quantity']);
								//$sumExclVat = ($row['Cost_excl']*$value);	

								$invoiceTotalInclVat=$invoiceTotalInclVat+$sumInclVat;					
								//$invoiceTotalExclVat=$invoiceTotalExclVat+$sumExclVat;					
								echo "<tr>";
								echo "<td>".$row['Supplement_id']."</td><td>R".$row['Item_Price']."</td><td>".$row['Item_Quantity']."</td>";
								echo "</tr>";
							}
						}
						else{
							echo "<tr>";
							echo "<td colspan='3'>No Items in the invoice</td>";
							echo "</tr>";
						}
						
						echo "<tr></tr>";
						echo "</table>";
						echo "<table>";
						echo "<tr>";
						echo "<th>Quantity:</th><td>".$totalCount."</td>";
						echo "</tr>";
						//echo "<tr>";
						//echo "<th>Total Excluding Vat:</th><td>R".$invoiceTotalExclVat."</td>";
						//echo "</tr>";
						echo "<tr>";
						echo "<th>Total Including Vat:</th><td>R".$invoiceTotalInclVat."</td>";
						echo "</tr>";
					}
					else
					{
						echo "<tr>";
							echo "<td colspan='3'>No Items in the invoice</td>";
						echo "</tr>";
					}
					echo "</table>
					</div>";
				}
			
				
			?>
		<?php
		
		?>
		
		
		<input type="Submit" name ="update invoice" value="update"/>
	</form>
	</div>
	
	
	<h3>Invoice List</h3>
	<?php
		if (isset($result) && isset($result->num_rows) && $result->num_rows > 0) {
		  // output data of each row
		  echo "<table  name='paginated-table' id='paginated-table' class='display' border='1'>";
		  echo "<thead>";
		  echo "<tr>";
		  
		  echo "<th></th><th>Inv_Num</th><th>Client_id</th><th>Inv_Date</th><th>Inv_Paid</th><th>Inv_Paid_Date</th><th>Comments</th>";
		  echo "</tr>";
		  echo "</thead>";
		  echo "<tbody>";
		  while($row = $result->fetch_assoc()) {
			echo "</tr>";
			echo "	<td><form action='ManageInvoices.php' method='POST'><input type='hidden' name='Inv_Num' value='".$row["Inv_Num"]."'/><input type='Submit' name='btnSelect' value='Select' formnovalidate/></form></td>
					<td>".$row["Inv_Num"]."</td>
					<td>".$row["Client_id"]."</td>
					<td>".$row["Inv_Date"]."</td>
					<td>".$row["Inv_Paid"]."</td>
					<td>".$row["Inv_Paid_Date"]."</td>
					<td>".$row["Comments"]."</td>";
			echo "</tr>";
		  }
		  echo "</tbody>";
		  echo "</table>";
		} else {
		  echo "0 Clients";
		}
		$conn->close();
	?>
	</body>
</html>