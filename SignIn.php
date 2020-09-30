<?php
session_start();
?>

<html>
	<head>
	
	
		<link rel="stylesheet" type="text/css" href="style/menubar.css">
		
		<script src="jquery-3.5.1.js"></script>
		<title>Login</title>
		<style>
			[data-slots] { font-family: monospace }
		</style>
		<style src='jquery-3.5.1.js'></style>
	</head>
	
	<body>
		<?php
			include "menu.php";
		?>
		<center>
			<h1>Welcome</h1>
			<div>
				<form method="POST" action="Login.php">
					<table>
						<tr>
							<td>
								<label for='username'>Username:</label><br>
								<input type='text' name='username' required='required'/>
							</td>
						</tr>
						<tr>
							<td>
								<label for='password'>Password:</label><br>
								<input type='password' name='password' required='required'/>
							</td>
						</tr>
						<?php
							if(isset($_SESSION['failedLogin']))
							{
								echo "<tr>
										<td align='center' style='color:red'>Login Failed: Try Again</td>
									</tr>";
							}
						?>
					</table>
					<input type="Submit" name ="action" value="Login"/>
				</form>
			</div>
		</center>
	</body>
</html>