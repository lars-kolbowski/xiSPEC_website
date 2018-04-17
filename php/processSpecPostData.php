<?php
//ToDo: duplication formToJson.php amd processSpecPostData.php
	require("functions.php");

	$mods = [];
	if(isset($_POST['mods'])){
			$mods = $_POST['mods'];
			$modMasses = $_POST['modMasses'];
			$modSpecificities = $_POST['modSpecificities'];
	}

	$pepsStr = $_POST["peps"];
	$clModMass = floatval($_POST['clModMass']);
	$ms2Tol = floatval($_POST['ms2Tol']);
	$tolUnit = $_POST['tolUnit'];
	$peaklist = $_POST['peaklist'];
	$preCharge = intval($_POST['preCharge']);

	$peaklist = explode("\r\n", $peaklist);

	//peptides linksites block
	$peps = explode(";", $pepsStr);
	$linkSites = array();
	$peptides = array();

	$i = 0;
	foreach ($peps as $pep) {
			array_push($peptides, pep_to_array($pep));
			$linkSites = array_merge($linkSites, get_link_sites($pep, $i));
			$i++;
	}


	//peak block
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
	$tol = array("tolerance" => $ms2Tol, "unit" => $tolUnit);
	$modifications = array();
	$i = 0;
	//var_dump(str_split($modSpecificities[$i]))
	//var_dump(implode(",", str_split($modSpecificities[$i]));
	//die();
	foreach ($mods as $mod) {
			array_push($modifications, array('aminoAcids' => str_split($modSpecificities[$i]), 'id' => $mod, 'mass' => $modMasses[$i]));
			$i++;
	}

	$ions = array();
	foreach ($_POST['ions'] as $iontype) {
		$iontype = ucfirst($iontype)."Ion";
		array_push($ions, array('type' => $iontype));
	}

	$cl = array('modMass' => $clModMass);

	// if ($tolUnit == "Da"){
	// 	$customCfg = "LOWRESOLUTION:true\n";
	// }
	// else {
	// 	$customCfg = "LOWRESOLUTION:false\n";
	// }

	$annotation = array(
		'fragmentTolerance' => $tol,
		'modifications' => $modifications,
		'ions' => $ions,
		'cross-linker' => $cl,
		'precursorCharge' => $preCharge,
		'custom' => ['']
	);

	//final array
	$postData = array(
		'Peptides' => $peptides,
		'LinkSite' => $linkSites,
		'peaks' => $peaks,
		'annotation' => $annotation
	);

	$postJSON = json_encode($postData);
	//var_dump(json_encode($postData));
	//die();


	// The data to send to the API
	$url = 'http://xi3.bio.ed.ac.uk/xiAnnotator/annotate/FULL';
	// Setup cURL
	$ch = curl_init($url);
	curl_setopt_array($ch, array(
		CURLOPT_POST => TRUE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
		),
		CURLOPT_POSTFIELDS => $postJSON
	));


	// Send the request
	$response = curl_exec($ch);

	// Check for errors
	if($response === FALSE){
			die(curl_error($ch));
	}
	$errorQuery = "java.lang.NullPointerException";
	if ($response === "" || substr($response, 0, strlen(($errorQuery))) === $errorQuery){
		var_dump($response);

		echo ("<p>xiAnnotator experienced a problem. Please try again later!</p><br/>");
		var_dump($postJSON);
		die();
	}
?>
