<?php
	session_start();
	if($_SESSION['failedLogin'])
	{
		unset($_SESSION['failedLogin']);
	}
	
	if(isset($_POST['username']) && isset($_POST['password']))
	{	
		if(($_POST['username']=='hcp') && ($_POST['password']=='hcpPassword'))
		{
			$_SESSION['loggedIn'] = 'true';
			$_SESSION['role'] = 'hcp';
			header('Location: ManageSupplement.php');
		}
		else if(($_POST['username']=='ga') && ($_POST['password']=='gaPassword'))
		{
			$_SESSION['loggedIn'] = 'true';
			$_SESSION['role'] = 'ga';
			header('Location: ManageSupplement.php');
		}
		else
		{
			$_SESSION['failedLogin'] = true;
			header('Location: SignIn.php');
		}
	}
	else
	{
		$_SESSION['failedLogin'] = true;
		header('Location: SignIn.php');
	}
?>