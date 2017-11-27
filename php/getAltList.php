<?php

$mzid = $_GET['id'];
session_start();

if (isset($_SESSION['db']))
	$dbname = "saved/".$_SESSION['db'];
else
	$dbname = "tmp/".session_id();

$dir = 'sqlite:../dbs/'.$dbname.'.db';
$dbh = new PDO($dir) or die("cannot open the database");
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if ($mzid == -1){
	$stmt = $dbh->prepare("SELECT mzid FROM jsonReqs LIMIT 1");
	if ($stmt->execute()) {
		while ($row = $stmt->fetch()) {
			$mzid = $row['mzid'];
		}
	}
}

$JSON = array();

$stmt = $dbh->prepare("SELECT id, mzid, pep1, pep2, linkpos1, linkpos2, charge, isDecoy, scores, protein, passThreshold, rank FROM jsonReqs WHERE mzid=:mzid ORDER BY id,rank;");
$stmt->bindParam(':mzid', $mzid);
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
