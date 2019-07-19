<?php

	if (session_status() === PHP_SESSION_NONE){session_start();}

	if ($_GET['tmp'] == '1'){
		$dbname = "tmp/".$_GET['db'];
	}
	elseif (isset($_GET['db'])){
		$dbname = "saved/".$_GET['db'];
	}
	else {
		die();
	}

	//check authentication
	if(!isset($_SESSION['access'])) $_SESSION['access'] = array();
	if(!in_array($_GET['db'], $_SESSION['access'])){
		//if no valid authentication re-test authentication
		//this includes a connection string to the sql database
		require('../xiSPEC_sql_conn.php');
		require('checkAuth.php');
	}
	// re-check authentication
	if(!in_array($_GET['db'], $_SESSION['access'])){
		$json['error'] = "Authentication error occured!";
		die(json_encode($json));
	}

	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser/';
	$dir = 'sqlite:'.$xiSPEC_ms_parser_dir.'/dbs/'.$dbname.'.db';
	$dbh = new PDO($dir) or die("cannot open the database");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



	$sql = "SELECT * FROM meta_data WHERE upload_id == 0";

	$stmt = $dbh->prepare($sql);

	if ($stmt->execute()) {

		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$result){
			$result = array();
			$result['sid_meta1_name'] = '-1';
			$result['sid_meta2_name'] = '-1';
			$result['sid_meta3_name'] = '-1';
			$result['contains_crosslink'] = 1;
		}

		echo json_encode($result);
	}

?>
