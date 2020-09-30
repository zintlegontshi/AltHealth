<?php
session_start();
?>
<html manifest="offline.manifest">
	<head>
		<title>Supplier Info</title>
                <p id="status">Online</p>
		<link rel="stylesheet" type="text/css" href="style/menubar.css">
		
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
                
		<?php
			include "dbConnections.php";

			$sql = "";
			if(isset($_SESSION['loggedIn']))
			{
				if (isset($_POST['action']) && $_POST['action']=="Add New Supplier")
				{
					$sql = "INSERT INTO `tblsupplier_info`(`Supplier_Id`, `Contact_Person`, `Supplier_Tel`, `Supplier_Email`, `Bank`, `Bank_Code`, `Supplier_BankNum`, `Supplier_Type_Bank_Annount`)".
						" VALUES ('".$_POST['Supplier_Id']."','".$_POST['Contact_Person']."','".$_POST['Supplier_Tel']."','".$_POST['Supplier_Email']."','".$_POST['Bank']."','".$_POST['Bank_Code']."','".$_POST['Supplier_BankNum']."','".$_POST['Supplier_Type_Bank_Annount']."')";
					if ($conn->query($sql) === TRUE) {
					  echo "New record created successfully";
					} else {
					  echo "Error: " . $sql . "<br>" . $conn->error;
					}
				}
			}
			else
			{
				header('Location: ManageClients.php');
			}
			
			$searchResult = null;
			
			if (isset($_POST['action']) && $_POST['action']=="Search")
			{
				$whereClause = null;
				
				if(isset($_POST['Supplier_Id'])&& !empty($_POST['Supplier_Id']))
				{
					$whereClause = " WHERE Supplier_Id ='".$_POST['Supplier_Id']."'";
				}
				
				$sql = "SELECT Supplier_Id,Contact_Person,Supplier_Tel,Supplier_Email,Bank,Bank_Code,Supplier_BankNum,Supplier_Type_Bank_Annount
						 FROM tblsupplier_info ".$whereClause;
				
				$searchResult = $conn->query($sql);
			}
			
			$sql = "SELECT Supplier_Id,Contact_Person,Supplier_Tel,Supplier_Email,Bank,Bank_Code,Supplier_BankNum,Supplier_Type_Bank_Annount
					 FROM tblsupplier_info 
					 ORDER BY Supplier_Id DESC";
			
			$result = $conn->query($sql);
		?>
		<script type="text/javascript">
			// This Bank empowers all input tags having a placeholder and data-slots attribute
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
		  var x = document.getElementById("addEditSupplier");
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
	
	<body>
	<?php
		include "menu.php";
	?>
		<h1>Manage Suppliers</h1>
		<?php
			$displayStyle="display:none";
			$showHide="Show";
			
			$Supplier_Id = "";
			$Contact_Person = "";
			$Supplier_Tel = "";
			$Supplier_Email = "";
			$Bank = "";
			$Bank_Code = "";
			$Supplier_BankNum = "";
			$Supplier_Type_Bank_Annount = "";
			if (isset($searchResult) && isset($searchResult->num_rows) && $searchResult->num_rows > 0) 
			{
				if($row = $searchResult->fetch_assoc()) 
				{
					$Supplier_Id = $row['Supplier_Id'];
					$Contact_Person = $row['Contact_Person'];
					$Supplier_Tel = $row['Supplier_Tel'];
					$Supplier_Email = $row['Supplier_Email'];
					$Bank = $row['Bank'];
					$Bank_Code = $row['Bank_Code'];
					$Contact_Person = $row['Contact_Person'];
					$Supplier_BankNum = $row['Supplier_BankNum'];
					$Supplier_Type_Bank_Annount = $row['Supplier_Type_Bank_Annount'];
					
					$displayStyle="display:block"; 
					$showHide = "Hide";
				}
			
			}
			echo "<h3>Supplier Details<button id='btnShowHide' onclick='showHideAdd()'>".$showHide."</button></h3>";
			echo "<div id='addEditSupplier' style='".$displayStyle."'>";
		
		?>
			<form method="POST" action="ManageSuppliers.php">
			
			<table>
			<?php
				
				echo "
				<tr>
					<td>
						<label for='Supplier_Id'>Supplier_Id:</label><br>
						<input type='text' name='Supplier_Id' required='required' value='".$Supplier_Id."'/>
					</td>
					<td>
						<input type='Submit' name='action' value='Search' formnovalidate />
					</td>
				</tr>
				<tr>
					<td>
						<label for='Contact_Person'>Contact_Person:</label><br>
						<input type='text' name='Contact_Person' required='required' value='".$Contact_Person."'/>
					</td>
				</tr>
				<tr>
					<td>
						<label for='Supplier_Tel'>Supplier_Tel:</label><br>
						<input type='text' name='Supplier_Tel' value='".$Supplier_Tel."' placeholder='(___)-(___)-(____)' data-slots='_' required='required' title='The provided contact number is invalid' />
					</td>
					<td>
						<label for='Supplier_Email'>Supplier_Email:</label><br>
						<input type='email' name='Supplier_Email' value='".$Supplier_Email."' required='required'/>
					</td>
				</tr>
				<tr>
					<td>
						<label for='Bank'>Bank:</label><br>
						<input type='text' name='Bank' value='".$Bank."' required='required'/>
					</td>
					<td>
						<label for='Bank_Code'>Branch Code:</label><br>
						<input type='text' name='Bank_Code' value='".$Bank_Code."' required='required'/>
					</td>
					<td>
						<label for='Supplier_BankNum'>Account Number:</label><br>
						<input type='text' name='Supplier_BankNum' value='".$Supplier_BankNum."' data-slots='_' required='required'/>
					</td>
					<td>
						<label for='Supplier_Type_Bank_Annount'>Account Type:</label><br>
						<input type='text' name='Supplier_Type_Bank_Annount' value='".$Supplier_Type_Bank_Annount."' required='required'/>
					</td>
				</tr>
				<tr>
					<td>
						<input type='Submit' name='action'  value='Add New Supplier' />
					</td>
				</tr>";
			?>
			</table>
		</form>
		</div>
		<h3>Supplier List</h3>
		<?php
		
			if (isset($result) && isset($result->num_rows) && $result->num_rows > 0) {
			  // output data of each row
			  echo "<table name='paginated-table' id='paginated-table' class='display' border='1'>";
			  echo "<thead>";
			  echo "<tr>";
			  echo "<th>Supplier_Id</th><th>Contact_Person</th><th>Supplier_Tel</th><th>Supplier_Email</th><th>Bank</th><th>Bank_Code</th><th>Supplier_BankNum</th><th>Supplier_Type_Bank_Annount</th>";
			  echo "</tr>";
			  echo "</thead>";
			  echo "<tbody>";
			  while($row = $result->fetch_assoc()) {
				echo "</tr>";
				echo "<td>".$row["Supplier_Id"]."</td><td>".$row["Contact_Person"]."</td><td>".$row["Supplier_Tel"]."</td><td>".$row["Supplier_Email"]."</td><td>".$row["Bank"]."</td><td>".$row["Bank_Code"]."</td><td>".$row["Supplier_BankNum"]."</td><td>".$row["Supplier_Type_Bank_Annount"]."</td>";
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