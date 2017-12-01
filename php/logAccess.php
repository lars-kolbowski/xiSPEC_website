<?php
	if (session_status() === PHP_SESSION_NONE){session_start();}
	if(!isset($xiSPECdb))
		die('No database connection!');

	$dbname = $_SESSION['db'];
	$stmt = $xiSPECdb->prepare("SELECT id FROM databases WHERE name = :name;");
	$stmt->bindParam(':name', $dbname, PDO::PARAM_STR);
	$stmt->execute();
	$dbid = $stmt->fetchColumn();

	if (!$dbid) {
		header("Location: upload.php");
		exit();
	}
	else{
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d H:i:s');

		$stmt = $xiSPECdb->prepare("INSERT INTO access_log ('db_id', 'ip', 'date') VALUES (:dbid, :ip, :dates)");
		$stmt->bindParam(':dbid', $dbid, PDO::PARAM_INT);
		$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':dates', $date, PDO::PARAM_STR);

		$stmt->execute();
	}
?>
