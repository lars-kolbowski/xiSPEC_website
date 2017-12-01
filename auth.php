<?php
	if (session_status() === PHP_SESSION_NONE){session_start();}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>xiSPEC - authenticate</title>
	</head>
	<body>
			<?php if(isset($_GET['e']) && $_GET['e'] == -1) echo 'wrong password!'; ?>
		<form action='php/checkAuth.php' method='POST'>
			The database <?php echo $_SESSION['db']; ?> is private and password protected. Please enter the password below:</br>
			<label class="flex-row label">
				Password: <div class="flex-grow"><input class="form-control" required length=30 name="dbPass" type="password" placeholder="Enter password"></div>
			</label>
			<input type="submit" class="btn btn-1 btn-1a" value="save">
		</form>
	</body>
</html>
