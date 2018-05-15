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
	$date = date('Y-m-d H:i:s');
	$ip = getUserIP();
	$ipInfo = json_decode(file_get_contents("https://ipinfo.io/{$ip}/"));
	$hostname = (property_exists($ipInfo, 'hostname')) ? $ipInfo->hostname : '' ;
	$country = (property_exists($ipInfo, 'country')) ? $ipInfo->country : '' ;
	$region = (property_exists($ipInfo, 'region')) ? $ipInfo->region : '' ;
	$city = (property_exists($ipInfo, 'city')) ? $ipInfo->city : '' ;
	$org = (property_exists($ipInfo, 'org')) ? $ipInfo->org : '' ;

	$dbname = $_POST['dbName'];
	if($dbname == "") die("no name specified!");

	if(preg_match('/[^A-Za-z0-9_\-()]/', $dbname)){
		$json['error'] = "name contains invalid chars! Valid chars are: A-Z a-z 0-9 _ - ( )";
		die(json_encode($json));
	}

	if (isset($_POST['public']))
		$passHash = 'public';
	else
		$passHash = password_hash($_POST['dbPass'], PASSWORD_DEFAULT);

	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser/';

	#this includes a connection string to the sql database
	require('../xiSPEC_sql_conn.php');
	$xiSPECdb = new PDO("mysql:host=localhost;dbname=".$DBname, $DBuser, $DBpass) or die("cannot open the database");
	$xiSPECdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $xiSPECdb->prepare("INSERT INTO `dbs`(`name`, `pass`, `email`, `ip`, `hostname`, `country`, `region`, `city`, `org`, `date`)
															VALUES (:name, :pass, :email, :ip, :hostname, :country, :region, :city, :org, :dates);");
	$stmt->bindParam(':name', $dbname, PDO::PARAM_STR);
	$stmt->bindParam(':pass', $passHash, PDO::PARAM_STR);
	$stmt->bindParam(':email', $_POST['dbEmail'], PDO::PARAM_STR);
	$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
	$stmt->bindParam(':hostname', $ipInfo->hostname, PDO::PARAM_STR);
	$stmt->bindParam(':country', $ipInfo->country, PDO::PARAM_STR);
	$stmt->bindParam(':region', $ipInfo->region, PDO::PARAM_STR);
	$stmt->bindParam(':city', $ipInfo->city, PDO::PARAM_STR);
	$stmt->bindParam(':org', $ipInfo->org, PDO::PARAM_STR);
	$stmt->bindParam(':dates', $date, PDO::PARAM_STR);

	try {
		$stmt->execute();

		$db_path = 	$xiSPEC_ms_parser_dir.'/dbs/';
		$tmpDB = $db_path."tmp/".session_id().".db";
		$newDB = $db_path."saved/".$dbname.".db";
		if (!copy($tmpDB, $newDB)) {
				$json['error'] = "Error saving database!";
		}
		if(!isset($_SESSION['access'])) $_SESSION['access'] = array();
		if(!in_array($dbname, $_SESSION['access'])){
			$_SESSION['access'][] = $dbname;
		}
		$_SESSION[$dbname] = 'saved';
		$json['name'] = $dbname;

	} catch (PDOException $e) {
			if ($e->getCode() == 23000) {
					$json['error'] = "Database name already taken. Please chose another one and try again!";
			} else {
					throw $e;
			}
	}


	echo json_encode($json);

?>
