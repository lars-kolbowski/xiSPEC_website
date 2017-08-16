<?php
session_start();
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

    $db_path = "../../dbs/";
    $tmpDB = $db_path."/tmp/".session_id().".db";
    $newDB = $db_path.$dbname.".db";
	if (!copy($tmpDB, $newDB)) {
	    $json['error'] = "Error saving database!";
	}
    //delete tmpDBs with cronjob
	//if (file_exists($tmpDB))
	//	unlink($tmpDB);
    $json['url'] = "http://" . $_SERVER['SERVER_NAME'] . "/xiSPEC/viewSpectrum.php?db=" . $dbname;

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        $json['error'] = "Database name already taken. Please chose another one and try again!";
    } else {
        throw $e;
    }
}


echo json_encode($json);

?>