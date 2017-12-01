<?php
	if (session_status() === PHP_SESSION_NONE){session_start();}

	if (!isset($_SESSION['pwHash']) || !isset($_POST['dbPass']) || !isset($_SESSION['db'])) {
		header("Location: ../upload.php");
		exit();
	}

	if (password_verify($_POST['dbPass'], $_SESSION['pwHash'])){
		$_SESSION['access'] = $_SESSION['db'];
		header("Location: ../viewSpectrum.php?db=".$_SESSION['db']);
	}
	else
		header("Location: ../auth.php?e=-1");

?>
