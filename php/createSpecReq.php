<?php

	require('functions.php');

	if (session_status() === PHP_SESSION_NONE){session_start();}

	if (isset($_SESSION['tmpDB'])){
		$dbname = "tmp/".session_id();
	}
	else {
		$dbname = "saved/".$_GET['db'];
	}

	if(!in_array($_GET['db'], $_SESSION['access'])){
		$json['error'] = "Authentication error occured!";
		die(json_encode($json));
	}
	
	$dir = 'sqlite:../dbs/'.$dbname.'.db';
	$dbh = new PDO($dir) or die("cannot open the database");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$query = "SELECT * FROM jsonReqs as jr JOIN peakLists AS pl ON jr.peakList_id = pl.id WHERE jr.id ='".$_GET['id']."';";

	foreach ($dbh->query($query) as $row)
	{
		$result = $row;
	}

	// peptides
	$peptides = array();
	array_push($peptides, pep_to_array($result['pep1']));
	if ($result['pep2'] != ''){
		array_push($peptides, pep_to_array($result['pep2']));
	}

	// $preCharge = intval($result['charge']);

	$linkSites = array();
	if ($result['linkpos1'] != -1){
		array_push($linkSites, array('id' => 0, 'peptideId' => 0, 'linkSite' => (intval($result['linkpos1']) - 1) ));
		array_push($linkSites, array('id' => 0, 'peptideId' => 1, 'linkSite' => (intval($result['linkpos2']) - 1) ));
	}


	//peak block
	$peaklist = explode("\n", $result['peakList']);

	$peaks = array();
	foreach ($peaklist as $peak) {
			$peak = trim($peak);
			if ($peak != ""){
					$parts = preg_split('/\s+/', $peak);
					if(count($parts) > 1)
							array_push($peaks, array('mz' => floatval($parts[0]), 'intensity' => floatval($parts[1])));
			}
	}
	//annotation block
	// $tol = array("tolerance" => $ms2Tol, "unit" => $tolUnit);
	// $modifications = array();
	// $i = 0;
	// //var_dump(str_split($modSpecificities[$i]))
	// //var_dump(implode(",", str_split($modSpecificities[$i]));
	// //die();
	// foreach ($mods as $mod) {
	// 		array_push($modifications, array('aminoAcids' => str_split($modSpecificities[$i]), 'id' => $mod, 'mass' => $modMasses[$i]));
	// 		$i++;
	// }
	//
	// $ions = array();
	// foreach ($_POST['ions'] as $iontype) {
	// 	$iontype = ucfirst($iontype)."Ion";
	// 	array_push($ions, array('type' => $iontype));
	// }
	//
	// $cl = array('modMass' => $clModMass);
	//
	// $annotation = array('fragmentTolerance' => $tol, 'modifications' => $modifications, 'ions' => $ions, 'cross-linker' => $cl, 'precursorCharge' => $preCharge, 'custom' => "LOWRESOLUTION:false"); //ToDo: LOWRESOLUTION: true setting

	$annotation = json_decode($result['annotation']);

	$annotation->custom = "LOWRESOLUTION:false";

	//final array
	$postData = array('Peptides' => $peptides, 'LinkSite' => $linkSites, 'peaks' => $peaks, 'annotation' => $annotation);

	$postJSON = json_encode($postData);
	//var_dump(json_encode($postData));
	//die();


print $postJSON;



// $jsonArr = json_decode($peakList, true);
//die();
// if (array_key_exists('custom', $jsonArr['annotation'])){
// 	if (strpos($jsonArr['annotation']['custom'], "LOWRESOLUTION:false"))
// 		$jsonArr['annotation']['custom'] += "LOWRESOLUTION:false\n";
// }
// else{
// 	$jsonArr['annotation']['custom'] = "LOWRESOLUTION:false\n";
// }

// print json_encode($jsonArr);

?>
