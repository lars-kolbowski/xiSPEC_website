<?php
	if (session_status() === PHP_SESSION_NONE){session_start();}

	$dbname = $_GET['db'];

	if(!in_array($dbname, $_SESSION['access'])){
		$json['error'] = "Authentication error occured!";
		die(json_encode($json));
	}
	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser/';
	$dir = 'sqlite:'.$xiSPEC_ms_parser_dir.'dbs/xiSPEC.db';
	$dbh = new PDO($dir) or die("cannot open the database");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $dbh->prepare("UPDATE databases SET share = lower(hex(randomblob(16))) WHERE name = :name AND share IS null");
	$stmt->bindParam(':name', $dbname, PDO::PARAM_STR);
	try {
		$stmt->execute();
		// throw an error if random share string could not be generated
		if($stmt->rowCount() != 1){
			$json['error'] = "An error occured! Link for db could not be created.";
			die(json_encode($json));
		}

		$stmt = $dbh->prepare("SELECT share FROM databases WHERE name = :name");
		$stmt->bindParam(':name', $dbname, PDO::PARAM_STR);

		$stmt->execute();
		$shareStr = $stmt->fetchColumn();
		$json['url'] = "http://" . $_SERVER['SERVER_NAME'] . "/xiSPEC/viewSpectrum.php?sid=" . $shareStr;

		echo json_encode($json);
	} catch (PDOException $e) {
		die(json_encode($e));
	}

?>
