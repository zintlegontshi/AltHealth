<?php
ob_start();
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['emailContent']))
{
	
	$emailBody = "<html>
					<head>
					</head>
					<body>Good Day ".$_SESSION['clientDetails']['C_name']." ".$_SESSION['clientDetails']['C_surname']."
					<br><br>
					See below for your invoice:
					<br><br>
					".$_POST['emailContent']."
					<br><br>
					Regards,<br>
					Alt Health					
					</body></html>";
	include "dbConnections.php";


	//include the composer generaded Autoload php file
	//require 'C:\xampp\composer\vendor\autoload.php';

	require 'phpMailer/Exception.php';
	require 'phpMailer/PHPMailer.php';
	require 'phpMailer/SMTP.php';

	//create a new PHP mailer object
	$mail = new PHPMailer(TRUE);
	$mail->IsSMTP();
	$mail->Mailer = "smtp";
	$mail->isHTML(true);
	$mail->SMTPDebug  = 1;  
	$mail->SMTPAuth   = TRUE;
	$mail->SMTPSecure = "tls";
	$mail->Host       = "tls://smtp.gmail.com";
	$mail->Username   = "mukonimsmtp@gmail.com";
	$mail->Password   = "ComplicatedPassword@1";
	$mail->Port       = 587;

	/* Open the try/catch block. */
	try {
	   /* Set the mail sender. */
	   $mail->setFrom('Info@althealth.com', 'AltHealth Sales');

	   /* Add a recipient. */
	   $mail->addAddress('mukonim@gmail.com'); //here i want to use a place holder and generate client's email

	   /* Set the subject. */
	   $mail->Subject = 'Invoice:'.$_SESSION['invoiceDetails']['invoiceNumber'];

	   /* Set the mail message body. */
	   $mail->Body = $emailBody;
	   
	   /*Add an attachment, assuming we will be sending the invoice as an attachment 
	   $mail->addAttachment('C:\xampp\htdocs\AltHealth\sampleinvoice.txt' //ideally use a place holder for the attachment
		  */     
	   
	   /*creating an attachment from a databasa data */
	   // $mysql_data = $mysql_row['Inv_Num'];
		//$mail->addStringAttachment($mysql_data, 'the actual invoice.png');

	   //$mail->addAttachment("Assign_V6.zip");
	   /* Finally send the mail. */
	   $mail->send();
	   
	   
	   //update the database
	   foreach ($_SESSION['cart'] as $key => $value) 
	   {
		   $sql = "SELECT Supplement_id,Supplier_Id,Supplement_Description,Cost_excl,Cost_incl,Min_level,Current_stock_levels,Nappi_code
					FROM tblsupplements  WHERE Supplement_id ='".$key."'";
			$result = $conn->query($sql);
			$itemPrice = 0;
			while($row = $result->fetch_assoc()) 
			{		
				$itemPrice = $row['Cost_incl'];
			}
			$sql = "INSERT INTO `tblinv_items`(`Inv_Num`, `Supplement_id`, `Item_Price`, `Item_Quantity`) VALUES ('".$_SESSION['invoiceDetails']['invoiceNumber']."','".$key."','".$itemPrice."','".$value."')";
			$conn->query($sql);
			$sql = "UPDATE `tblsupplements` 
					 SET `Current_stock_levels`=`Current_stock_levels`-".$value.
					" WHERE `Supplement_id`='".$key."'";
			$conn->query($sql);
			
	   }
	   
	   unset($_SESSION['clientDetails']);
	   unset($_SESSION['cart']);
	   unset($_SESSION['invoiceDetails']);
	   header('Location: ManageClients.php');
	}
	catch (Exception $e)
	{
	   /* PHPMailer exception. */
	   echo $e->errorMessage();
	   $_SESSION['emailError']=$e->errorMessage();
	   header('Location: ViewInvoice.php');
	}
	catch (\Exception $e)
	{
	   echo $e->getMessage();
	   $_SESSION['emailError']=$e->getMessage();
	   header('Location: ViewInvoice.php');
	}

}
else
{
	$_SESSION['emailError']="Could not send an empty email";
	header('Location: ViewInvoice.php');
}	

?>    