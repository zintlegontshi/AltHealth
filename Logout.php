<?php
	session_start();
	if(isset($_SESSION['loggedIn']))
	{	
		unset($_SESSION['loggedIn']);
		unset($_SESSION['role']);
		session_destroy(); 
	}
	header('Location: ManageSupplement.php');
?>