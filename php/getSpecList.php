<?php
	if (session_status() === PHP_SESSION_NONE){session_start();}

	if (isset($_SESSION['tmpDB'])){
		$dbname = "tmp/".session_id();
	}
	else {
		$dbname = "saved/".$_GET['db'];
	}

	if(!in_array($_GET['db'], $_SESSION['access'])){
		$json['error'] = "Authentication error occured!";
		die(json_encode($json));
	}

	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser/';
	$dir = 'sqlite:'.$xiSPEC_ms_parser_dir.'/dbs/'.$dbname.'.db';

	$dbh = new PDO($dir) or die("cannot open the database");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$query =  "SELECT MIN(id) as id, count(id) as alt_count, sid, pep1, pep2, linkpos1, linkpos2, charge, isDecoy, scores, protein1, protein2, passThreshold, file, scanID FROM identifications WHERE rank = 1 GROUP BY mzid ORDER BY id;";

	$JSON = array();

	foreach ($dbh->query($query) as $row)
	{
		array_push($JSON, $row);
	}

	$arr = array('data' => $JSON, 'db' => $dbname);

	echo json_encode($arr);

?>
