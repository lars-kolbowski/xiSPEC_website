<?php
	if (session_status() === PHP_SESSION_NONE){session_start();}
	if(!isset($xiSPECdb))
		die('No database connection!');

	$dbname = $_SESSION['db'];

	$stmt = $xiSPECdb->prepare("SELECT pass FROM databases WHERE name = :name;");
	$stmt->bindParam(':name', $dbname, PDO::PARAM_STR);
	$stmt->execute();
	$dbpass = $stmt->fetchColumn();

	if (!$dbpass) {
		header("Location: upload.php");
		exit();
	}
	else{
		if ($dbpass === 'public')
			$_SESSION['access'] = $dbname;
		else{
			$_SESSION['pwHash'] = $dbpass;
		}
	}

?>
