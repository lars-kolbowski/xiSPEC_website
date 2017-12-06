<?php
	if (session_status() === PHP_SESSION_NONE){session_start();}
	$date = date('Y-m-d H:i:s');
	$ip = $_SERVER['REMOTE_ADDR'];

	$dbname = $_POST['dbName'];
	if($dbname == "") die("no name specified!");

	if (isset($_POST['public']))
		$passHash = 'public';
	else
		$passHash = password_hash($_POST['dbPass'], PASSWORD_DEFAULT);



	$dir = 'sqlite:../dbs/xiSPEC.db';
	$dbh = new PDO($dir) or die("cannot open the database");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $dbh->prepare("INSERT INTO databases ('name', 'pass', 'ip', 'date') VALUES (:name, :pass, :ip, :dates)");
	$stmt->bindParam(':name', $dbname, PDO::PARAM_STR);
	$stmt->bindParam(':pass', $passHash, PDO::PARAM_STR);
	$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
	$stmt->bindParam(':dates', $date, PDO::PARAM_STR);

	try {
			$stmt->execute();

			$db_path = "../dbs/";
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
		// "http://" . $_SERVER['SERVER_NAME'] . "/xiSPEC/viewSpectrum.php?db=" . $dbname;

			//delete tmpDBs with cronjob
		// if (file_exists($tmpDB))
		// 	unlink($tmpDB);


	} catch (PDOException $e) {
			if ($e->getCode() == 23000) {
					$json['error'] = "Database name already taken. Please chose another one and try again!";
			} else {
					throw $e;
			}
	}


	echo json_encode($json);

?>
