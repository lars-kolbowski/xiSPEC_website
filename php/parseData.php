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
	$_SESSION['tmpDB'] = session_id();

	$target_dir = "../uploads/".session_id()."/";
	$mzid_file = $target_dir . $_POST['mzid_fn'];
	$mzml_file = $target_dir . $_POST['mzml_fn'];

	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser/';

	$date = date('Y-m-d H:i:s');
	$ip = getUserIP();
	$ipInfo = json_decode(file_get_contents("https://ipinfo.io/{$ip}/"));
	$hostname = (property_exists($ipInfo, 'hostname')) ? $ipInfo->hostname : '' ;
	$country = (property_exists($ipInfo, 'country')) ? $ipInfo->country : '' ;
	$region = (property_exists($ipInfo, 'region')) ? $ipInfo->region : '' ;
	$city = (property_exists($ipInfo, 'city')) ? $ipInfo->city : '' ;
	$org = (property_exists($ipInfo, 'org')) ? $ipInfo->org : '' ;

	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser/';

	#this includes a connection string to the sql database
	require('../../xiSPEC_sql_conn.php');
	$xiSPECdb = new PDO("mysql:host=localhost;dbname=".$DBname, $DBuser, $DBpass) or die("cannot open the database");
	$xiSPECdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $xiSPECdb->prepare("INSERT INTO `upload_log`(`id_file`, `pl_file`, `ip`, `hostname`, `country`, `region`, `city`, `org`, `date`)
															VALUES (:id_file, :pl_file, :ip, :hostname, :country, :region, :city, :org, :dates);");
	$stmt->bindParam(':id_file', $_POST['mzid_fn'], PDO::PARAM_STR);
	$stmt->bindParam(':pl_file', $_POST['mzml_fn'], PDO::PARAM_STR);
	$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
	$stmt->bindParam(':hostname', $ipInfo->hostname, PDO::PARAM_STR);
	$stmt->bindParam(':country', $ipInfo->country, PDO::PARAM_STR);
	$stmt->bindParam(':region', $ipInfo->region, PDO::PARAM_STR);
	$stmt->bindParam(':city', $ipInfo->city, PDO::PARAM_STR);
	$stmt->bindParam(':org', $ipInfo->org, PDO::PARAM_STR);
	$stmt->bindParam(':dates', $date, PDO::PARAM_STR);

	$stmt->execute();


	$command = escapeshellcmd($xiSPEC_ms_parser_dir.'python_env/bin/python '.$xiSPEC_ms_parser_dir.'parser.py '.$mzid_file.' '.$mzml_file. ' '.session_id());
	// die($command);
	// echo "<br/>";
	$output = shell_exec($command);
	echo $output;

?>
