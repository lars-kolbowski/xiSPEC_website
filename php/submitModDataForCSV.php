<?php

	if (session_status() === PHP_SESSION_NONE){session_start();}

	if(!isset($_SESSION['tmpDB']))
		die('dataset not found');

	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser/';

	$dir = 'sqlite:'.$xiSPEC_ms_parser_dir.'/dbs/tmp/'.$_SESSION['tmpDB'].'.db';

	$dbh = new PDO($dir) or die("cannot open the database");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$i = 0;
	foreach ($_POST['mods'] as $modname) {
		$stmt = $dbh->prepare("INSERT INTO modifications ('id', 'name', 'mass', 'residues') VALUES (:id, :modname, :modmass, '*');");
		$stmt->bindParam(':id', $i, PDO::PARAM_INT);
		$stmt->bindParam(':modname', $modname, PDO::PARAM_STR);
		$stmt->bindParam(':modmass', $_POST['modMasses'][$i], PDO::PARAM_STR);
		try {
			$stmt->execute();
		}
		catch (PDOException $e) {
			die(json_encode($e));
		}
	}

	header("Location: ../viewSpectrum.php");
	die();

?>
