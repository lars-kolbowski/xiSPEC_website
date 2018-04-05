<?php
	if (session_status() === PHP_SESSION_NONE){session_start();}

	if(!isset($_SESSION['tmpDB']))
		die('dataset not found');

	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser/';

	$dir = 'sqlite:'.$xiSPEC_ms_parser_dir.'/dbs/tmp/'.$_SESSION['tmpDB'].'.db';

	$dbh = new PDO($dir) or die("cannot open the database");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$ions = implode(';', $_POST['ions']);

	//ToDo: only update rows which could not be parsed -> probably edge case either all fail or none
	$stmt = $dbh->prepare("UPDATE spectrum_identifications SET 'ions'=:ions;");
	// $stmt->bindParam(':id', $i, PDO::PARAM_INT);
	$stmt->bindParam(':ions', $ions, PDO::PARAM_STR);
	try {
		$stmt->execute();
	}
	catch (PDOException $e) {
		die(json_encode($e));
	}

	// header("Location: ../viewSpectrum.php");
	// die();

?>
