<?php

$servername = "localhost";
$username = "root";
$password = "Hlumisa002";
$dbname = "althealth";
			
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
	}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//include the composer generaded Autoload php file
require 'C:\xampp\composer\vendor\autoload.php';

//create a new PHP mailer object
$email = new PHPMailer(TRUE);

/* Open the try/catch block. */
try {
   /* Set the mail sender. */
   $mail->setFrom('Info@althealth.com', 'AltHealth Sales');

   /* Add a recipient. */
   $mail->addAddress('Zintle.Gontshi01@gmail.com'); //here i want to use a place holder and generate client's email

   /* Set the subject. */
   $mail->Subject = 'New Invoice';

   /* Set the mail message body. */
   $mail->Body = 'Dear Client, Please find the attached invoice for your attention.';
   
   /*Add an attachment, assuming we will be sending the invoice as an attachment 
   $mail->addAttachment('C:\xampp\htdocs\AltHealth\sampleinvoice.txt' //ideally use a place holder for the attachment
      */     
   
   /*creating an attachment from a databasa data */
    $mysql_data = $mysql_row['Inv_Num'];
    $mail->addStringAttachment($mysql_data, 'the actual invoice.png');

   /* Finally send the mail. */
   $mail->send();
}
catch (Exception $e)
{
   /* PHPMailer exception. */
   echo $e->errorMessage();
}
catch (\Exception $e)
{
   echo $e->getMessage();
}

?>    