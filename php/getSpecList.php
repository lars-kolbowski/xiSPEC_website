<?php

session_start();

$dir = 'sqlite:../../dbs/'.session_id().'.db';
$dbh = new PDO($dir) or die("cannot open the database");
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$query =  "SELECT * FROM mzids";

$JSON = array();

foreach ($dbh->query($query) as $row)
{
	array_push($JSON, array('id' => $row['id'], 'mzid' => $row['mzid']));
}

$arr = array('data' => $JSON);

echo json_encode($arr);

?>