<?php
session_start();

if (isset($_SESSION['db'])){
	$dbname = $_SESSION['db'];
}
else
	$dbname = "tmp/".session_id();

$dir = 'sqlite:../../dbs/'.$dbname.'.db';

$dbh = new PDO($dir) or die("cannot open the database");
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$query =  "SELECT MIN(id) as id, count(id) as alt_count, mzid, pep1, pep2, linkpos1, linkpos2, isDecoy, scores, passThreshold, file, scanID FROM jsonReqs WHERE rank = 1 GROUP BY mzid ORDER BY id;";

$JSON = array();

foreach ($dbh->query($query) as $row)
{
	array_push($JSON, $row);
}

$arr = array('data' => $JSON, 'db' => $dbname);

echo json_encode($arr);

?>


