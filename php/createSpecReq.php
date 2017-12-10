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

	$query = "SELECT * FROM identifications as i JOIN peakLists AS pl ON i.peakList_id = pl.id WHERE i.id ='".$_GET['id']."';";

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
	$fragTol = explode(' ', $result['fragTolerance'], 2);
	$tol = array("tolerance" => $fragTol[0], "unit" => $fragTol[1]);
	$cl = array('modMass' => $result['crosslinker_modMass']);
	$preCharge = intval($result['charge']);

	$ions = array();
	$ionTypes = explode(';', $result['ionTypes']);
	foreach ($ionTypes as $ion) {
		array_push($ions, array('type' => strtoupper($ion).'Ion'));
	}
	//ToDo: get DB modifications only once from DB and save in model
	$query =  "SELECT * FROM modifications ;";
	$modifications = array();
	foreach ($dbh->query($query) as $row)
	{
		array_push($modifications, array('aminoAcids' => str_split($row['residues']), 'id' => $row['name'], 'mass' => $row['mass']));

	}


	$annotation = array('fragmentTolerance' => $tol, 'modifications' => $modifications, 'ions' => $ions, 'cross-linker' => $cl, 'precursorCharge' => $preCharge, 'custom' => "LOWRESOLUTION:false"); //ToDo: LOWRESOLUTION: true setting

	// $annotation = json_decode($result['annotation']);

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
