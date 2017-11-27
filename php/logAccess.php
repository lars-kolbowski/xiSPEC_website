<?php

	$dbname = $_GET['db'];
	$dir = 'sqlite:dbs/xiSPEC.db';
	$dbh = new PDO($dir) or die("cannot open the database");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $dbh->prepare("SELECT id FROM databases WHERE name = :name;");
	$stmt->bindParam(':name', $dbname, PDO::PARAM_STR);


	$stmt->execute();

	if (!$stmt->fetchColumn()) {
		header("Location: upload.php");
		exit();
	}
	else{
		$dbid = $stmt->fetchColumn();
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d H:i:s');

		session_start();
		$_SESSION['db'] = $dbname;

		$stmt = $dbh->prepare("INSERT INTO access_log ('db_id', 'ip', 'date') VALUES (:dbid, :ip, :dates)");
		$stmt->bindParam(':dbid', $dbid, PDO::PARAM_INT);
		$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':dates', $date, PDO::PARAM_STR);

		$stmt->execute();
	}
?>
