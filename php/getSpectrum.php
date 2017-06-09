<?php

session_start();

$dir = 'sqlite:../../dbs/'.session_id().'.db';
$dbh = new PDO($dir) or die("cannot open the database");
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$query =  "SELECT * FROM mzids WHERE id=".$_GET['i']." LIMIT 1";
foreach ($dbh->query($query) as $row)
{
    $mzid = $row['mzid'];
}

$query =  "SELECT * FROM jsonReqs WHERE mzid=".$mzid." ORDER BY rank ASC LIMIT 1";

foreach ($dbh->query($query) as $row)
{
    $postJSON = $row['json'];
}
print $postJSON;

?>