<?php

	if (session_status() === PHP_SESSION_NONE){session_start();}

	if(!isset($_SESSION['access']))
		die('dataset not found');

	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser/';
	$dbName = $_SESSION['access'][0];
	$dir = 'sqlite:'.$xiSPEC_ms_parser_dir.'/dbs/saved/'.$dbName.'.db';

	$dbh = new PDO($dir) or die("cannot open the database");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$cfg = $_POST['custom_config'];
	$stmt = $dbh->prepare("UPDATE meta_data SET 'custom_config' = :cfg WHERE upload_id == 0;");
	$stmt->bindParam(':cfg', $cfg, PDO::PARAM_STR);
	try {
		$stmt->execute();
	} catch (PDOException $e) {
		die(json_encode($e));
	}

?>
