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
