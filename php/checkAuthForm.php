<?php

	#this includes a connection string to the sql database
	require('../xiSPEC_sql_conn.php');
	$xiSPECdb = new PDO("mysql:host=localhost;dbname=".$DBname, $DBuser, $DBpass) or die("cannot open the database");
	$xiSPECdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if (session_status() === PHP_SESSION_NONE){session_start();}

	if (!isset($_POST['dbPass']) || !isset($_POST['dbName'])) {
		header("Location: /upload.php");
		exit();
	}

	$stmt = $xiSPECdb->prepare("SELECT pass FROM dbs WHERE name = :name;");
	$stmt->bindParam(':name', $_POST['dbName'], PDO::PARAM_STR);
	$stmt->execute();
	$passHash = $stmt->fetchColumn();

	if (password_verify($_POST['dbPass'], $passHash)){
		if(!isset($_SESSION['access'])) $_SESSION['access'] = array();

		if(!in_array($_POST['dbName'], $_SESSION['access'])){
			$_SESSION['access'][] = $_POST['dbName'];
		}
		header("Location: /viewSpectrum.php?db=".$_POST['dbName']);
	}
	else
		header("Location: /auth.php?db=".$_POST['dbName']."&e=-1");

?>
