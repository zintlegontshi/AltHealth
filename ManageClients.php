<?php
session_start();
?>

<html manifest="offline.manifest">
	<head>
	
	
		<link rel="stylesheet" type="text/css" href="style/menubar.css">
		
		<script src="jquery-3.5.1.js"></script>
		<title>Clients</title>
                 <p id="status">Online</p>
		<style>
			[data-slots] { font-family: monospace }
		</style>
                
                 <script type="text/javascript" src="jquery-1.4.min.js"></script>
                <script type="text/javascript" src="offline.js"></script>
                
		<style src='jquery-3.5.1.js'></style>
		
		<script src="https://code.jquery.com/jquery-3.5.1.min.js"integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="  crossorigin="anonymous"></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
		<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
		<script>
			$(document).ready( function () {
				$('#paginated-table').DataTable();
			} );
			
			
		</script>
		
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
		<?php
			include "dbConnections.php";
			
			if(!isset($_SESSION['loggedIn']))
			{	
				header('Location: ManageSupplement.php');
			}

			if(isset($_SESSION['currentStep']) && $_SESSION['currentStep']=="checkout_step1")
			{
				//echo "<script>alert('Checkout in progress');</script>";
			}
			
			if(isset($_POST['action']) && $_POST['action']=='GenerateInvoice')
			{
				$_SESSION['currentStep']=="checkout_step2";
				
				$_SESSION['clientDetails']['Client_id'] = $_POST['Client_id'];
				$_SESSION['clientDetails']['C_name'] = $_POST['C_name'];
				$_SESSION['clientDetails']['C_surname'] = $_POST['C_surname'];
				$_SESSION['clientDetails']['Address'] = $_POST['Address'];
				$_SESSION['clientDetails']['Code'] = $_POST['Code'];
				$_SESSION['clientDetails']['C_Tel_H'] = $_POST['C_Tel_H'];
				$_SESSION['clientDetails']['C__Tel_W'] = $_POST['C__Tel_W'];
				$_SESSION['clientDetails']['C_Tel_Cell'] = $_POST['C_Tel_Cell'];
				$_SESSION['clientDetails']['C_Email'] = $_POST['C_Email'];
				$_SESSION['clientDetails']['Reference_ID'] = $_POST['Reference_ID'];
				unset($_SESSION['invoiceDetails']);
				
				header('Location: ViewInvoice.php');
			}
			
			
			$searchResults = null;
			if(isset($_POST['action']))
			{
				if($_POST['action']=='Add New Client')
				{
					$insertSql = "INSERT INTO `tblclientinfo`(`Client_id`, `C_name`, `C_surname`, `Address`, `Code`, `C_Tel_H`, `C__Tel_W`, `C_Tel_Cell`, `C_Email`, `Reference_ID`)".
							" VALUES ('".
								$_POST['Client_id']."','".
								$_POST['C_name']."','".
								$_POST['C_surname']."','".
								$_POST['Address']."','".
								$_POST['Code']."','".
								$_POST['C_Tel_H']."','".
								$_POST['C__Tel_W']."','".
								$_POST['C_Tel_Cell']."','".
								$_POST['C_Email']."',".
								$_POST['Reference_ID'].")";
					
					if ($conn->query($insertSql) === TRUE) 
					{
					  echo "New record created successfully";
					} 
					else 
					{
					  echo "Error: " . $insertSql . "<br>" . $conn->error;
					}
				}
				
				$whereClause = "";
				$_POST['Client_id'] = str_replace("_","",$_POST['Client_id']);
				if(isset($_POST['Client_id'])&& !empty($_POST['Client_id']))
				{
					
					$whereClause = " WHERE Client_id ='".$_POST['Client_id']."'";
				}
				$sql = "SELECT Client_id,C_name,C_surname,Address,Code,C_Tel_H,C__Tel_W,C_Tel_Cell,C_Email,Reference_ID
						FROM tblclientinfo ".$whereClause." ORDER BY Client_id DESC";
				$searchResults = $conn->query($sql);
			}
			
			$sql = "SELECT Client_id,C_name,C_surname,Address,Code,C_Tel_H,C__Tel_W,C_Tel_Cell,C_Email,Reference_ID
						 FROM tblclientinfo 
						 ORDER BY Client_id DESC";
			
			$result = $conn->query($sql);
		?>
	</head>
	
	<body>
	<?php
		include "menu.php";
	?>
	
	<h1>Clients</h1>
	<div>
	
		<?php
			$displayStyle="display:none";
			$showHide="Show";
			
			$Client_id = "";
			$C_name = "";
			$C_surname = "";
			$Address = "";
			$Code = "";
			$C_Tel_H = "";
			$C__Tel_W = "";
			$C_Tel_Cell = "";
			$C_Email = "";
			$Reference_ID = "";
			if(isset($searchResults) && $row = $searchResults->fetch_assoc()) 
			{
				$Client_id = " value='".$row["Client_id"]."' ";
				$C_name = " value='".$row["C_name"]."' ";
				$C_surname = " value='".$row["C_surname"]."' ";
				$Address = " value='".$row["Address"]."' ";
				$Code = " value='".$row["Code"]."' ";
				$C_Tel_H = " value='".$row["C_Tel_H"]."' ";
				$C__Tel_W = " value='".$row["C__Tel_W"]."' ";
				$C_Tel_Cell = " value='".$row["C_Tel_Cell"]."' ";
				$C_Email = " value='".$row["C_Email"]."' ";
				$Reference_ID = " value='".$row["Reference_ID"]."' ";	
				
				$displayStyle="display:block"; 
				$showHide = "Hide";
				
			}
			
			echo "<h3>Client Details:<button id='btnShowHide' onclick='showHideAdd()'>".$showHide."</button></h3>";
			echo "<div id='addEditSupplier' style='".$displayStyle."'>";
		?>
		
		
		<form method="POST" action="ManageClients.php">
			<table>
				<?php
				
					
					echo "	<tr>
								<td>
									<label for='Client_id'>South Africa ID Number:</label><br>
									<input type='text' name='Client_id'  ".$Client_id." placeholder='_____________' data-slots='_' required='required' pattern='^(\d{13})?$' title='Ensure that you put in a 13 Digit ID Number with the correct format'/>
								</td>
								<td><br>
									<input type='Submit' name='action' value='Search' formnovalidate/>
								</td>
							</tr>
							<tr>
								<td>
									<label for='C_name'>Name:</label><br>
									<input type='text' name='C_name'  ".$C_name." required='required'/>
								</td>
								<td>
									<label for='C_surname'>Surname:</label><br>
									<input type='text' name='C_surname'  ".$C_surname." required='required'/>
								</td>
							</tr>
							<tr>
								<td>
									<label for='Address'>Address:</label><br>
									<input type='text' name='Address'  ".$Address." required='required'/>
								</td>
								<td>
									<label for='Code'>Code:</label><br>
									<input type='text' name='Code'  ".$Code." placeholder='____' data-slots='_' required='required' pattern='^(\d{4})?$' title='Ensure that your code is 4 digits long'/>
								</td>
							</tr>
							<tr>
								<td>
									<label for='C_Tel_H'>C_Tel_H:</label><br>
									<input type='text' name='C_Tel_H' placeholder='(___)-(___)-(____)'  ".$C_Tel_H." data-slots='_' required='required' title='The provided contact number is invalid'/>
								</td>
								<td>
									<label for='C__Tel_W'>C__Tel_W:</label><br>
									<input type='text' name='C__Tel_W' placeholder='(___)-(___)-(____)'  ".$C__Tel_W." data-slots='_' required='required' title='The provided contact number is invalid'/>
								</td>
								<td>
									<label for='C_Tel_Cell'>C_Tel_Cell:</label><br>
									<input type='text' name='C_Tel_Cell' placeholder='(___)-(___)-(____)'  ".$C_Tel_Cell." data-slots='_' required='required' title='The provided contact number is invalid'/>
								</td>
							</tr>
							<tr>
								<td>
									<label for='C_Email'>C_Email:</label><br>
									<input type='email' name='C_Email' ".$C_Email." required='required'/></td>
								</tr>
							<tr>
								<td>
									<label for='Reference_ID'>Reference ID:</label><br>";
									$reference = $Reference_ID;
									$sql = "SELECT `Reference_ID`, `Description` FROM `tblreference`";
									echo "<select name='Reference_ID'>";
									
									$referencesResult = $conn->query($sql);
									if (isset($referencesResult) && isset($referencesResult->num_rows) && $referencesResult->num_rows > 0) 
									{
									  while($row = $referencesResult->fetch_assoc()) 
									  {
										$selected = "";
										if($row["Reference_ID"]==$reference)
										{
											$selected = "selected";
										}
										echo "<option value='".$row['Reference_ID']."' ".$selected.">".$row['Description']."</option>";
									  }
									}
									echo "</select>
								</td>
							</tr>";
					
					if(isset($_SESSION['cart']) && isset($Client_id) && !empty($_POST['Client_id']))
					{
						echo "<tr><td><input type='Submit' name ='action' value='GenerateInvoice' formnovalidate/></td></tr>";
					}
					
				?>
			</table>
			<input type="Submit" name ="action" value="Add New Client"/>
		</form>
		</div>
	</div>
	<h3>Client List</h3>
	<?php
		if (isset($result) && isset($result->num_rows) && $result->num_rows > 0) {
		  // output data of each row
		  echo "<table  name='paginated-table' id='paginated-table' class='display' border='1'>";
		  echo "<thead><tr>";
		  echo "<th></th><th>Client_id</th><th>C_name</th><th>C_surname</th><th>Address</th><th>Code</th><th>C_Tel_H</th><th>C__Tel_W</th><th>C_Tel_Cell</th><th>C_Email</th><th>Reference_ID</th>";
		  echo "</tr></thead>";
		  echo "<tbody>";
		  while($row = $result->fetch_assoc()) {
			echo "</tr>";
			echo "<td><form action='ManageClients.php' method='POST'><input type='hidden' name='Client_id' value='".$row["Client_id"]."'/><input type='Submit' name='action' value='Select' formnovalidate/></form></td><td>".$row["Client_id"]."</td><td>".$row["C_name"]."</td><td>".$row["C_surname"]."</td><td>".$row["Address"]."</td><td>".$row["Code"]."</td><td>".$row["C_Tel_H"]."</td><td>".$row["C__Tel_W"]."</td><td>".$row["C_Tel_Cell"]."</td><td>".$row["C_Email"]."</td><td>".$row["Reference_ID"]."</td>";
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