<?php
	if (session_status() === PHP_SESSION_NONE){session_start();}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>xiSPEC - authenticate</title>
		<link rel="icon" type="image/ico" href="/images/logos/favicon.ico">
		<link rel="stylesheet" href="/css/auth.css" />
		<link rel="stylesheet" href="/css/style2.css" />
		<link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css"/>
		<link rel="stylesheet" href="/css/validationPage.css">
	</head>
	<body>

		<div id="mainView">
			<div class="mainContent">
				<div id="spectrumControls">
					<i class="btn btn-1a btn-topNav fa fa-home fa-xi" style='margin: -2px 2px 2px 2px;' onclick="window.location = 'index.php';" title="Home"></i>
					<i class="btn btn-1a btn-topNav fa fa-github fa-xi" onclick="window.open('https://github.com/Rappsilber-Laboratory/xiSPEC/issues', '_blank');" title="GitHub issue tracker" style="cursor:pointer;"></i>
				</div>
				<div class="container">

					<?php if(isset($_GET['e']) && $_GET['e'] == -1) echo 'wrong password!'; ?>
					<form action='/php/checkAuthForm.php' method='POST'>
						The database <?php echo $_GET['db']; ?> is private and password protected!</br>
						<input style="display: none;" name="dbName" value="<?php echo $_GET['db']; ?>">
						<label class="flex-row label">
							Password: <div class="flex-grow"><input style="max-width: 20em;" class="form-control" required length=30 name="dbPass" type="password" placeholder="Enter password"></div>
						</label>
						<div style="text-align:center;">
							<input type="submit" class="btn btn-2" value="submit">
						</div>
					</form>

				</div> <!-- container -->
			</div> <!-- mainContent -->
		</div> <!-- mainView -->
	</body>
</html>
