<?php

function getUserIP(){
	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];

	if(filter_var($client, FILTER_VALIDATE_IP))
	{
		$ip = $client;
	}
	elseif(filter_var($forward, FILTER_VALIDATE_IP))
	{
		$ip = $forward;
	}
	else
	{
		$ip = $remote;
	}

	return $ip;
}

	if (session_status() === PHP_SESSION_NONE){session_start();}
	if(!isset($xiSPECdb))
		die('No database connection!');

	$stmt = $xiSPECdb->prepare("SELECT id FROM databases WHERE name = :name;");
	$stmt->bindParam(':name', $dbName, PDO::PARAM_STR);
	$stmt->execute();
	$dbid = $stmt->fetchColumn();

	if (!$dbid) {
		header("Location: upload.php");
		exit();
	}
	else{
		$ip = getUserIP();
		$date = date('Y-m-d H:i:s');

		$stmt = $xiSPECdb->prepare("INSERT INTO access_log ('db_id', 'ip', 'date') VALUES (:dbid, :ip, :dates)");
		$stmt->bindParam(':dbid', $dbid, PDO::PARAM_INT);
		$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':dates', $date, PDO::PARAM_STR);

		$stmt->execute();
	}
?>
