<?php

	$sid = $_GET['id'];
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
		require('../../xiSPEC_sql_conn.php');
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


	if ($sid == -1){
		$stmt = $dbh->prepare("SELECT sid FROM identifications LIMIT 1");
		if ($stmt->execute()) {
			while ($row = $stmt->fetch()) {
				$sid = $row['sid'];
			}
		}
	}

	$JSON = array();

	if (isset($_GET['sname'])){
		$sql = 	"SELECT identifications.id, sid, pep1, pep2, linkpos1, linkpos2, charge, isDecoy, atom AS score, allScores, protein1, protein2, passThreshold, rank
			FROM identifications, json_each(identifications.allScores)
			WHERE json_each.key LIKE :scoreName AND sid=:sid
			ORDER BY identifications.id,rank";
			$stmt = $dbh->prepare($sql);
			$stmt->bindParam(':sid', $sid);
			$stmt->bindParam(':scoreName', $_GET['sname']);
	}

	else {
		$sql = "SELECT identifications.id, sid, pep1, pep2, linkpos1, linkpos2, charge, isDecoy, atom AS score, allScores, protein1, protein2, passThreshold, rank
			FROM identifications, json_each(identifications.allScores)
			WHERE sid=:sid
			ORDER BY identifications.id,rank";
			$stmt = $dbh->prepare($sql);
			$stmt->bindParam(':sid', $sid);
	}

	// echo $sid;

	if ($stmt->execute()) {

		$result = $stmt->fetchAll();
		foreach ($result as $row) {
			$row['alt_count'] = count($result);
			array_push($JSON, $row);
		}
	}

	$arr = array('data' => $JSON);

	echo json_encode($arr);

?>
