<?php

session_start();
if (isset($_SESSION['db']))
	$dbname = $_SESSION['db'];
else
	$dbname = session_id();

$dir = 'sqlite:../../dbs/'.$dbname.'.db';
$dbh = new PDO($dir) or die("cannot open the database");
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query =  "SELECT * FROM jsonReqs WHERE id='".$_GET['i']."';";

foreach ($dbh->query($query) as $row)
{
    $postJSON = $row['json'];
}
print $postJSON;

?>