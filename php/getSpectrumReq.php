<?php

session_start();
if (isset($_SESSION['db']))
	$dbname = "saved/".$_SESSION['db'];
else
	$dbname = "tmp/".session_id();

$dir = 'sqlite:../dbs/'.$dbname.'.db';
$dbh = new PDO($dir) or die("cannot open the database");
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query =  "SELECT * FROM jsonReqs WHERE id='".$_GET['i']."';";

foreach ($dbh->query($query) as $row)
{
    $postJSON = $row['json'];
}
$jsonArr = json_decode($postJSON, true);
//die();
if (array_key_exists('custom', $jsonArr['annotation'])){
	if (strpos($jsonArr['annotation']['custom'], "LOWRESOLUTION:false"))
		$jsonArr['annotation']['custom'] += "LOWRESOLUTION:false\n";
}
else{
	$jsonArr['annotation']['custom'] = "LOWRESOLUTION:false\n";
}

print json_encode($jsonArr);

?>
