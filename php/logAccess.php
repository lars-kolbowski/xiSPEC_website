<?php
	error_reporting(E_ERROR | E_PARSE);

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

	$stmt = $xiSPECdb->prepare("SELECT id FROM dbs WHERE name = :name;");
	$stmt->bindParam(':name', $dbName, PDO::PARAM_STR);
	$stmt->execute();
	$dbid = $stmt->fetchColumn();

	if (!$dbid) {
		header("Location: upload.php");
		exit();
	}
	else{
		$ip = getUserIP();
		$ipInfo = json_decode(file_get_contents("https://ipinfo.io/{$ip}/"));
		$hostname = (property_exists($ipInfo, 'hostname')) ? $ipInfo->hostname : '' ;
		$country = (property_exists($ipInfo, 'country')) ? $ipInfo->country : '' ;
		$region = (property_exists($ipInfo, 'region')) ? $ipInfo->region : '' ;
		$city = (property_exists($ipInfo, 'city')) ? $ipInfo->city : '' ;
		$org = (property_exists($ipInfo, 'org')) ? $ipInfo->org : '' ;

		$date = date('Y-m-d H:i:s');
		$stmt = $xiSPECdb->prepare("INSERT INTO `access_log`(`ip`, `hostname`, `country`, `region`, `city`, `org`, `date`, `db_id`)
																VALUES (:ip, :hostname, :country, :region, :city, :org, :dates, :dbid);");
		$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':hostname', $ipInfo->hostname, PDO::PARAM_STR);
		$stmt->bindParam(':country', $ipInfo->country, PDO::PARAM_STR);
		$stmt->bindParam(':region', $ipInfo->region, PDO::PARAM_STR);
		$stmt->bindParam(':city', $ipInfo->city, PDO::PARAM_STR);
		$stmt->bindParam(':org', $ipInfo->org, PDO::PARAM_STR);
		$stmt->bindParam(':dates', $date, PDO::PARAM_STR);
		$stmt->bindParam(':dbid', $dbid, PDO::PARAM_INT);
		$stmt->execute();
	}
?>
