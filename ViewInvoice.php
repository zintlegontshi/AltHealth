<?php
session_start();
?>

<html manifest="offline.manifest">
	
	<head>
		<title>Invoice</title>
                <p id="status">Online</p>
		<link rel="stylesheet" type="text/css" href="style/menubar.css">
                
                <script type="text/javascript" src="jquery-1.4.min.js"></script>
                <script type="text/javascript" src="offline.js"></script>
                
		<?php
			if(!isset($_SESSION['cart']) || empty($_SESSION['cart']))
			{
				header('Location: ManageSupplement.php');
			}
			else if(!isset($_SESSION['clientDetails']))
			{
				header('Location: ManageClients.php');
			}
			
			include "dbConnections.php";

			if(!isset($_SESSION['invoiceDetails']))
			{
				$sql = "SELECT `Inv_Num` FROM `tblinv_info` ORDER BY Inv_Num DESC LIMIT 1";
				$result = $conn->query($sql);
				$invoiceNumber = "";
				
				if($row = $result->fetch_assoc()) 
				{		
					$invoiceNumber = $row['Inv_Num'];
					
					$numberPart =  str_replace("INV","",$invoiceNumber );
					$numberPart = (int)$numberPart;
					$numberPart = $numberPart+1;
					
					$_SESSION['invoiceDetails']['invoiceNumber']= 'INV'.$numberPart;
				}
				else
				{
					$_SESSION['invoiceDetails']['invoiceNumber'] = "INV0001";
				}
				$_SESSION['invoiceDetails']['invoiceDate'] = date_create('now')->format('Y-m-d');
			
			
				$sql = "INSERT INTO `tblinv_info`(`Inv_Num`,`Client_id`, `Inv_Date`, `Inv_Paid`, `Inv_Paid_Date`, `Comments`)".
					" VALUES ('".$_SESSION['invoiceDetails']['invoiceNumber']."','".$_SESSION['clientDetails']['Client_id']."','".$_SESSION['invoiceDetails']['invoiceDate']."','N','".$_SESSION['invoiceDetails']['invoiceDate']."','')";
				if ($conn->query($sql) === TRUE) {
				  echo "New record created successfully";
				} else {
				  echo "Error: " . $sql . "<br>" . $conn->error;
				}
			}
		?>
	</head>
	
	<body>
	<?php
		include "menu.php";
	?>
	
		<?php
			if(isset($_SESSION['emailError']))
			{
				echo "<h3 stlye='color:red'>Error Sending Email</h3>";
				unset($_SESSION['emailError']);
			}
		?>
		<form name="frmEmail" id="frmEmail" method="POST" action="processInvoice.php" onSubmit="document.getElementById('emailContent').value=document.getElementById('invoiceContent').innerHTML;return true;">
			<div name="invoiceContent" id="invoiceContent">
				<h1>Invoice</h1>

				<?php
					echo "<table>"; 
					echo "<tr><th>Invoice Number:</th><td>".$_SESSION['invoiceDetails']['invoiceNumber']."</td></tr>";
					echo "<tr><th>Client ID:</th><td>".$_SESSION['clientDetails']['Client_id']."</td></tr>";
					echo "<tr><th>Name:</th><td>".$_SESSION['clientDetails']['C_name']." ".$_SESSION['clientDetails']['C_surname']."</td></tr>";
					echo "<tr><th>Date:</th><td>".date_create('now')->format('Y-m-d')."</td></tr>";
					echo "<tr>";
					echo "<td><h3>Invoice Items</h3></td>";
					echo "</tr>";
					echo "<tr>";
					echo "</table>";
					echo "<table border='1'>";
					echo "<th>Supplement name</th><th>Items to checkout</th>";
					echo "</tr>";
					if(isset($_SESSION['cart']))
					{
						$totalCount=0;
						$invoiceTotalInclVat = 0;
						$invoiceTotalExclVat = 0;
						
						echo "<tr>";
						echo "<td>Supplement Id</td><td>Supplement Description</td><td>Cost Excluding Vat</td><td>Cost Including Vat</td><td>Cost Excluding Vat</td><td>Total Excluding Vat</td><td>Quantity</td><td>Total Including Vat</td>";
						echo "</tr>";
						foreach ($_SESSION['cart'] as $key => $value) 
						{
							echo "<tr>";
							$sql = "SELECT Supplement_id,Supplier_Id,Supplement_Description,Cost_excl,Cost_incl,Min_level,Current_stock_levels,Nappi_code
									FROM tblsupplements  WHERE Supplement_id ='".$key."'";
							$result = $conn->query($sql);
							
							$totalCount=$totalCount+$value;
							while($row = $result->fetch_assoc()) 
							{		
								$sumInclVat = ($row['Cost_incl']*$value);
								$sumExclVat = ($row['Cost_excl']*$value);	

								$invoiceTotalInclVat=$invoiceTotalInclVat+$sumInclVat;					
								$invoiceTotalExclVat=$invoiceTotalExclVat+$sumExclVat;					
								echo "<tr>";
								echo "<td>".$key."</td><td>".$row['Supplement_Description']."</td><td>".$row['Cost_excl']."</td><td>".$row['Cost_incl']."</td><td>".$row['Cost_excl']."</td><td>R".$sumExclVat."</td><td>".$value."</td><td>R".$sumInclVat."</td>";
								echo "</tr>";
							}
						}
						echo "<tr></tr>";
						echo "</table>";
						echo "<table>";
						echo "<tr>";
						echo "<th>Quantity:</th><td>".$totalCount."</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<th>Total Excluding Vat:</th><td>R".$invoiceTotalExclVat."</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<th>Total Including Vat:</th><td>R".$invoiceTotalInclVat."</td>";
						echo "</tr>";
					}
					else
					{
						echo "<tr>";
							echo "<td colspan='2'>No Items in the cart</td>";
						echo "</tr>";
					}
					echo "</table>";
					$conn->close();
				?>
			</div>
			<input type="hidden" name="emailContent" id="emailContent" value="noValue"/>
			<input type="submit" name="sendInvoice" id="sendInvoice" value="Send Invoice"/>
		</form>
	</body>
</html>