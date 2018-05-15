<?php
	if (session_status() === PHP_SESSION_NONE){session_start();}

	$dbname = $_GET['db'];

	if(!in_array($dbname, $_SESSION['access'])){
		$json['error'] = "Authentication error occured!";
		die(json_encode($json));
	}
	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser/';

	#this includes a connection string to the sql database
	require('../xiSPEC_sql_conn.php');
	$xiSPECdb = new PDO("mysql:host=localhost;dbname=".$DBname, $DBuser, $DBpass) or die("cannot open the database");
	$xiSPECdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $xiSPECdb->prepare("UPDATE `dbs` SET `share` = left(sha2(rand(), 256), 42) WHERE `name` = :name AND `share` IS NULL");
	$stmt->bindParam(':name', $dbname, PDO::PARAM_STR);
	try {
		$stmt->execute();
		// throw an error if random share string could not be generated
		if($stmt->rowCount() != 1){
			$json['error'] = "An error occured! Link for db could not be created.";
			die(json_encode($json));
		}

		$stmt = $xiSPECdb->prepare("SELECT `share` FROM `dbs` WHERE `name` = :name");
		$stmt->bindParam(':name', $dbname, PDO::PARAM_STR);

		$stmt->execute();
		$shareStr = $stmt->fetchColumn();
		$json['url'] = "http://" . $_SERVER['HTTP_HOST'] . "/viewSpectrum.php?s=" . $shareStr;

		echo json_encode($json);
	} catch (PDOException $e) {
		die(json_encode($e));
	}

?>
