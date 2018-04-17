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
	$query =  "SELECT * FROM modifications;";

	$res = array();

	foreach ($dbh->query($query) as $row)
	{
		$mod = [
			"id" => $row['mod_name'],
			"mass" => floatval($row['mass']),
			"aminoAcids" => str_split($row['residues']),
		];
		array_push($res, $mod);
	}

	$arr = array('modifications' => $res);
	header('Content-type: application/json');
	echo json_encode($arr);

?>
