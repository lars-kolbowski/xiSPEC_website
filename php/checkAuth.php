<?php

	$allowAccess = false;

	$xiSPECdb = new PDO("mysql:host=localhost;dbname=".$DBname, $DBuser, $DBpass) or die("cannot open the database");
	$xiSPECdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if (session_status() === PHP_SESSION_NONE){session_start();}

	// get database name
	if (isset($_GET['s'])) {
		$stmt = $xiSPECdb->prepare("SELECT name FROM dbs WHERE share = :share;");
		$stmt->bindParam(':share', $_GET['s'], PDO::PARAM_STR);
		$stmt->execute();
		$dbName = $stmt->fetchColumn();

		if(!$dbName)
			die("authentication failure: no such database");
			// header('Location: index.php');

			$allowAccess = true;
	}
	elseif (isset($_GET['db'])) {
			$dbName = $_GET['db'];
	}
	else{
		die("authentication failure: no dbname given");
		// header('Location: index.php');
	}

	//check if it's a public database
		$stmt = $xiSPECdb->prepare("SELECT pass FROM dbs WHERE name = :name;");
		$stmt->bindParam(':name', $dbName, PDO::PARAM_STR);
		$stmt->execute();
		$passHash = $stmt->fetchColumn();

		if($passHash == 'public'){
			$allowAccess = true;
			$public = true;
		}
		// if not check if the use is authentified to see it
		else{
			//otherwise redirect him to the password authentication page
			if(!in_array($dbName, $_SESSION['access']))
				header("Location: /auth.php?db=".$dbName);
			else {
				$allowAccess = true;
			}
		}

		//finally add database name to SESSION if valid authentication was provided
		if($allowAccess){
			if(!isset($_SESSION['access'])) $_SESSION['access'] = array();
			if(!in_array($dbName, $_SESSION['access'])){
				$_SESSION['access'][] = $dbName;
			}
		}
		else{
			die("authentication failure!");
			// header('Location: index.php');
		}
	// }



?>
