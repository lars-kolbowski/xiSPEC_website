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
	$_SESSION['tmpDB'] = session_id();


	if( isset($_POST['res_fn']) && isset($_POST['peakFile_fn']) ){
		$id_file = $_POST['res_fn'];
		$pl_file = $_POST['peakFile_fn'];

		$target_dir = "../uploads/".session_id()."/";
		$id_arg = $target_dir . escapeshellarg($id_file);
		$pl_arg = $target_dir . escapeshellarg($pl_file);
		$upload_arg = session_id();
		$ftp_arg = '';
	}
	elseif ( isset($_POST['res_ftp']) && isset($_POST['peakFile_ftp']) ) {
		$id_file = substr($_POST['res_ftp'], strrpos($_POST['res_ftp'], "/") + 1) ;
		$pl_file = substr($_POST['peakFile_ftp'], strrpos($_POST['peakFile_ftp'], "/") + 1) ;

		$id_arg = escapeshellarg($_POST['res_ftp']);
		$pl_arg = escapeshellarg($_POST['peakFile_ftp']);
		$upload_arg = session_id();
		$ftp_arg = '-f';
	}
	else {
		die('error: invalid post data!');
	}

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
	require('../xiSPEC_sql_conn.php');
	$xiSPECdb = new PDO("mysql:host=localhost;dbname=".$DBname, $DBuser, $DBpass) or die("cannot open the database");
	$xiSPECdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $xiSPECdb->prepare("INSERT INTO `upload_log`(`id_file`, `pl_file`, `ip`, `hostname`, `country`, `region`, `city`, `org`, `date`)
															VALUES (:id_file, :pl_file, :ip, :hostname, :country, :region, :city, :org, :dates);");
	$stmt->bindParam(':id_file', $id_file, PDO::PARAM_STR);
	$stmt->bindParam(':pl_file', $pl_file, PDO::PARAM_STR);
	$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
	$stmt->bindParam(':hostname', $ipInfo->hostname, PDO::PARAM_STR);
	$stmt->bindParam(':country', $ipInfo->country, PDO::PARAM_STR);
	$stmt->bindParam(':region', $ipInfo->region, PDO::PARAM_STR);
	$stmt->bindParam(':city', $ipInfo->city, PDO::PARAM_STR);
	$stmt->bindParam(':org', $ipInfo->org, PDO::PARAM_STR);
	$stmt->bindParam(':dates', $date, PDO::PARAM_STR);

	$stmt->execute();

	$argStr = implode(' ', [$ftp_arg, $id_arg, $pl_arg, $upload_arg]);

	$command = $xiSPEC_ms_parser_dir.'python_env/bin/python '.$xiSPEC_ms_parser_dir.'parser.py '.$argStr;
	// die($command);
	// echo "<br/>";
	$output = shell_exec($command);
	echo $output;

?>
