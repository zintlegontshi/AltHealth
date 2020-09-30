<?php
session_start();
?>
<html manifest="offline.manifest">
	<head>
                <meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Supplement</title>
                <p id="status">Online</p>
		<link rel="stylesheet" type="text/css" href="style/menubar.css">
		<style>
			[data-slots] { font-family: monospace }
		</style>
                
                <script type="text/javascript" src="jquery-1.4.min.js"></script>
                <script type="text/javascript" src="offline.js"></script>
	
		<script src="https://code.jquery.com/jquery-3.5.1.min.js"integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="  crossorigin="anonymous"></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
		<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
		<script>
			$(document).ready( function () {
				$('#paginated-table').DataTable();
			} );
			
			
		</script>
		
		<script type="text/javascript">
			// This Cost_incl empowers all input tags having a placeholder and data-slots attribute
			document.addEventListener('DOMContentLoaded', () => {
				for (const el of document.querySelectorAll("[placeholder][data-slots]")) {
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
	</head>
		<?php
			include "dbConnections.php";

			if(isset($_POST['btnCartAction']))
			{
				$action = $_POST['btnCartAction'];
				if($action=="Clear")
				{
					unset($_SESSION['cart']);
				}
				else if($action=="Checkout")
				{
					$_SESSION['currentStep']="checkout_step1";
					header('Location: ManageClients.php');
				}
			}
			
			$sql = "";
			if (isset($_POST['action']) && $_POST['action']== "Add New Supplement")
			{
				
				$sql = "INSERT INTO `tblsupplements`(`Supplement_id`, `Supplier_Id`, `Supplement_Description`, `Cost_excl`, `Cost_incl`, `Min_level`, `Current_stock_levels`, `Nappi_code`)".
					" VALUES ('".$_POST['Supplement_id']."','".$_POST['Supplier_Id']."','".$_POST['Supplement_Description']."','".$_POST['Cost_excl']."','".$_POST['Cost_incl']."','".$_POST['Min_level']."','".$_POST['Current_stock_levels']."','".$_POST['Nappi_code']."')
					ON DUPLICATE KEY UPDATE Current_stock_levels='".$_POST['Current_stock_levels']."'";
				if ($conn->query($sql) === TRUE) {
				  echo "New record created successfully";
				} else {
				  echo "Error: " . $sql . "<br>" . $conn->error;
				}
			}
			
			if(isset($_GET['supplementId']))
			{				
				if(isset($_SESSION['cart'][$_GET['supplementId']]))
				{
					$_SESSION['cart'][$_GET['supplementId']] = $_SESSION['cart'][$_GET['supplementId']]+1;
				}
				else
				{
					$_SESSION['cart'][$_GET['supplementId']] = 1;
				}
				header('Location: ManageSupplement.php');
				
			}
				
			$searchResults = null;
			if (isset($_POST['action']) && $_POST['action']== "Search")
			{
				$whereClause = "";
				
				if(isset($_POST['Supplement_id'])&& !empty($_POST['Supplement_id']))
				{
					$whereClause = " WHERE Supplement_id ='".$_POST['Supplement_id']."'";
				}
				
				$sql = "SELECT Supplement_id,Supplier_Id,Supplement_Description,Cost_excl,Cost_incl,Min_level,Current_stock_levels,Nappi_code
						 FROM tblsupplements ".$whereClause;
				$searchResults = $conn->query($sql);
			}
			
			$sql = "SELECT Supplement_id,Supplier_Id,Supplement_Description,Cost_excl,Cost_incl,Min_level,Current_stock_levels,Nappi_code
						 FROM tblsupplements 
						 ORDER BY Supplement_id DESC";
			 
			$result = $conn->query($sql);
			  
			
		?>
	<body>
	<?php
		include "menu.php";
	?>
	<h1>Supplements</h3>
	<?php
		if(isset($_SESSION['loggedIn']))
		{
			$displayStyle="display:none";
			$showHide="Show";
			
			$Supplement_id = "";
			$Supplier_Id = "";
			$Supplement_Description = "";
			$Cost_excl = "";
			$Cost_incl = "";
			$Min_level = "";
			$Current_stock_levels = "";
			$Nappi_code = "";
			
			if (isset($searchResults) && isset($searchResults->num_rows) && $searchResults->num_rows > 0) 
			{
				if($row = $searchResults->fetch_assoc()) 
				{
					$Supplement_id =  $row["Supplement_id"];
					$Supplier_Id =  $row["Supplier_Id"];
					$Supplement_Description =  $row["Supplement_Description"];
					$Cost_excl =  $row["Cost_excl"];
					$Cost_incl =  $row["Cost_incl"];
					$Min_level =  $row["Min_level"];
					$Current_stock_levels =  $row["Current_stock_levels"];
					$Nappi_code =  $row["Nappi_code"];
					
					$displayStyle="display:block"; 
					$showHide = "Hide";
				}
			}
			
			echo "<h3>Supplement Details:<button id='btnShowHide' onclick='showHideAdd()'>".$showHide."</button></h3>";
			echo "<div id='addEditInvoice' style='".$displayStyle."'>";
		
		
	?>
	<h3>Supplement Details</h3>
	
	<form method="POST" action="ManageSupplement.php">
		<table>
		<?php
		
		
		echo "
		<tr>
			<td>
				<label for='Supplement_id'>Supplement_id:</label><br>
				<input type='text' name='Supplement_id' value='".$Supplement_id."' data-slots='_' required='required'/>
				<input type='Submit' name='action' value='Search' formnovalidate/>
			</td>
		</tr>
		<tr>	
			<td>
				<label for='Supplier_Id'>Supplier_Id:</label><br>
				<select name='Supplier_Id' id='Supplier_Id' required='required'>";
				
					$sql = "SELECT Supplier_Id FROM `tblsupplier_info`";
					
					$supplierResult = $conn->query($sql);
					if (isset($supplierResult) && isset($supplierResult->num_rows) && $result->num_rows > 0) {
					  
					  while($row = $supplierResult->fetch_assoc()) 
					  {
						  $selected = "";
						  if($Supplier_Id==$row['Supplier_Id'])
						  {
							  $selected = "selected";
						  }
						  echo "<option value='".$row['Supplier_Id']."' ".$selected.">".$row['Supplier_Id']."</option>";
					  }
					}
				
		echo "</select>
			</td>
		</tr>
		<tr>
			<td>
				<label for='Supplement_Description'>Supplement_Description:</label><br><input type='text' name='Supplement_Description' value='".$Supplement_Description."' required='required'/><br>
			</td>
		</tr>
		<tr>
			<td>
				<label for='Cost_excl'>Cost_excl:</label><br><input type='text' name='Cost_excl' value='".$Cost_excl."'  required='required'/>
			</td>
			<td>
				<label for='Cost_incl'>Cost_incl:</label><br><input type='text' name='Cost_incl' value='".$Cost_incl."' required='required'/><br>
			</td>
		</tr>
		<tr>
			<td>
				<label for='Min_level'>Min_level:</label><br>
				<input type='text' name='Min_level' value='".$Min_level."' required='required'/>
			</td>
			<td>
				<label for='Current_stock_levels'>Current_stock_levels:</label><br>
				<input type='text' name='Current_stock_levels' value='".$Current_stock_levels."' required='required'/>
			</td>
		</tr>
		<tr>
			<td>
				<label for='Nappi_code'>Nappi_code:</label><br>
				<input type='text' name='Nappi_code' value='".$Nappi_code."'/>
			<td>
		</tr>";
		
			?>
		</table>
		<input type='hidden' name='addNewSupplement'/>
		<input type='Submit' name='action' value='Add New Supplement'/>
	</form>
	</div>
	<?php
	}
	?>
	
	<div style="width: 100%; overflow: hidden;">
		<div style="float: left;">
			<h3>Supplements List</h3>
		<?php
		if (isset($result) && isset($result->num_rows) && $result->num_rows > 0) {
		  // output data of each row
		  echo "<table name='paginated-table' id='paginated-table' class='display' border='1'>";
		  echo "<thead>";
		  echo "<tr>";
		  echo "<th></th><th>Supplement_id</th><th>Supplier_Id</th><th>Supplement_Description</th><th>Cost_excl</th><th>Cost_incl</th>";
		  if(isset($_SESSION['loggedIn']))
		  {
			echo "<th>Min_level</th>";
		  }
		  echo "<th>Current_stock_levels</th><th>Nappi_code</th>";
		  echo "</tr>";
		  echo "</thead>";
		  echo "<tbody>";
		  while($row = $result->fetch_assoc()) {
			$currentLevel = $row["Current_stock_levels"];
			
			if(isset($_SESSION['cart'][$row["Supplement_id"]]))
			{
				$currentLevel = $currentLevel - $_SESSION['cart'][$row["Supplement_id"]];
				
				//echo "<script>alert('".$row["Supplement_id"]." already in basket');</script>";
			}
			$addLink = ""; 
			if($currentLevel>0)
			{
				$addLink = "<a href='ManageSupplement.php?supplementId=".$row["Supplement_id"]."'>Add to Cart</a>"; 
			}
			else
			{
				$addLink = "Out of stock";
			}
			
			echo "</tr>";
			echo "<td>".$addLink."</td><td>".$row["Supplement_id"]."</td><td>".$row["Supplier_Id"]."</td><td>".$row["Supplement_Description"]."</td><td>".$row["Cost_excl"]."</td><td>".$row["Cost_incl"]."</td>";
			if(isset($_SESSION['loggedIn']))
			{
				echo "<td>".$row["Min_level"]."</td>";
			}
			echo "<td>".$currentLevel."</td><td>".$row["Nappi_code"]."</td>";
			echo "</tr>";
		  }
		  
		  echo "</tbody>";
		  echo "</table>";
		  
		} else {
		  echo "0 Supplements";
		}
		$conn->close();
		?>
		</div>
		<div style="padding-left: 200px;">
			<h3>Cart</h3>
			<form action="ManageSupplement.php" method="POST">
				<table border='1' >
					<tr><th>Supplement</th><th>Items in Cart</th></tr>
				<?php
					if(isset($_SESSION['cart']))
					{
						foreach ($_SESSION['cart'] as $key => $value) 
						{
							echo "<tr>";
							echo "<td>".$key."</td><td>".$value."</td>";
							echo "</tr>";
						}
						
						echo "<tr><td></td><td><input type='submit' name='btnCartAction' value='Clear'/><input type='submit' name='btnCartAction' value='Checkout'/></td></tr>";
					}
					else
					{
						echo "<tr><td colspan='2'>No Items In The Cart</td></tr>";
					}
				?>
				</table>
			</form>
		</div>
	</div>
	
	</body>
</html>