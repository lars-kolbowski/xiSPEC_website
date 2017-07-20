<?php

$date = date('Y-m-d H:i:s');
$ip = $_SERVER['REMOTE_ADDR'];

$dbname = $_GET['name'];
if($dbname == "") die("no name specified!");

$dir = 'sqlite:../../dbs/xiSPEC.db';
$dbh = new PDO($dir) or die("cannot open the database");
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $dbh->prepare("INSERT INTO databases ('name', 'ip', 'date') VALUES (:name, :ip, :dates)");
$stmt->bindParam(':name', $dbname, PDO::PARAM_STR);
$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
$stmt->bindParam(':dates', $date, PDO::PARAM_STR);

try {
    $stmt->execute();
    echo "url...";
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "Database name already taken. Please chose another one and try again!";
    } else {
        throw $e;
    }
}

?>