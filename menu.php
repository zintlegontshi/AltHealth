
		<link rel="stylesheet" type="text/css" href="style/menubar.css">
<table>
    <tr>
        <td><img src= 'images/althealth-logo.png'/></td>
        <td><h2>Give your body what it needs</h2></td>
    </tr>
</table>	
	<?php
		if(!isset($_SESSION['loggedIn']))
		{	
				echo 
			"<div class='navbar'>".
			"  <a href='ManageSupplement.php'>Home</a>".
			
			"	  <a href='SignIn.php'>Login</a>".
			  
			"</div>";
		}
		else
		{
			echo
			"<div class='navbar'>".
			"  <a href='ManageSupplement.php'>Home</a>".
			"  <div class='dropdown'>".
			"	<button class='dropbtn'>Reports".
			"	  <i class='fa fa-caret-down'></i>".
			"	</button>".
			"	<div class='dropdown-content'>".
			"	  <a href='Report_UnpaidInvoiced.php'>Unpaid Invoices Report</a>".
			"	  <a href='Report_Birthday.php'>Today's Birthdays Report</a>".
			"	  <a href='Report_Minimum_Stock.php'>Minimum Stock Report</a>";
			
			if(isset($_SESSION['role']) && $_SESSION['role'] == 'hcp')
			{
				echo "	  <a href='Report_Top10Client.php'>Top 10 Clients Report</a>".
				"	  <a href='Report_Purchase_Statistics.php'>Purchase Statistics Report</a>".
				"	  <a href='Report_Client_Information.php'>Client Information Report</a>";
			}
			
			echo "	</div>".
			"  </div>".
			"  <div class='dropdown'>".
			"	<button class='dropbtn'>Supplements".
			"	  <i class='fa fa-caret-down'></i>".
			"	</button>".
			"	<div class='dropdown-content'>".
			"	  <a href='ManageSupplement.php'>Manage Supplements</a>".
			"	</div>".
			"  </div>".
			"  <div class='dropdown'>".
			"	<button class='dropbtn'>Clients".
			"	  <i class='fa fa-caret-down'></i>".
			"	</button>".
			"	<div class='dropdown-content'>".
			"	  <a href='ManageClients.php'>Manage Clients</a>".
			"	</div>".
			"  </div>".
			"  <div class='dropdown'>".
			"	<button class='dropbtn'>Supplier".
			"	  <i class='fa fa-caret-down'></i>".
			"	</button>".
			"	<div class='dropdown-content'>".
			"	  <a href='ManageSuppliers.php'>Manage Suppliers</a>".
			"	</div>".
			"  </div>".
			"  <div class='dropdown'>".
			"	<button class='dropbtn'>Invoices".
			"	  <i class='fa fa-caret-down'></i>".
			"	</button>".
			"	<div class='dropdown-content'>".
			"	  <a href='ViewInvoice.php'>Generate Invoice</a>".
			"	  <a href='ManageInvoices.php'>Manage Invoice</a>".
			"	</div>".
			"  </div>".
			"	  <a href='Logout.php'>Logout</a>".
			  
			"</div>";
		}
	?>
	
