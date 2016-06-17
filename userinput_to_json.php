<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function pep_to_array($pep){
	$mods = array();
	$pep = preg_replace( '/#[0-9]?/' , '' , $pep);	
	$pep_nomods = str_split(preg_replace( '/[a-z]+/' , '' , $pep));
	$pep_array = array();

	foreach ($pep_nomods as $letter) {
		array_push($pep_array, array('aminoAcid' => $letter, 'Modification' => ''));
	}

	preg_match_all('/[a-z]+/', $pep, $matches, PREG_OFFSET_CAPTURE);

	$offset = 1;
	foreach ($matches[0] as $matchgroup) {
		$pep_array[$matchgroup[1] - $offset]['Modification'] = $matchgroup[0];
		$offset += strlen($matchgroup[0]);
	}
	return array('sequence' => $pep_array);
}


$pepsStr = $_POST["peps"];
$clPos1 = intval($_POST["clPos1"])-1;
$clPos2 = intval($_POST["clPos2"])-1;
$clModMass = floatval($_POST['clModMass']);
//$ms2Tol = floatval($_POST['ms2Tol'])." ".$_POST['tolUnit'];
$ms2Tol = floatval($_POST['ms2Tol']);
$tolUnit = $_POST['tolUnit'];
$peaklist = $_POST['peaklist'];
$method = $_POST['fragMethod'];
$preCharge = intval($_POST['preCharge']);

//$peaklist = explode('<br />',nl2br($peaklist));
$peaklist = explode("\r\n", $peaklist);

//peptides block
$peps = explode(";", $pepsStr);
$peptides = array();
foreach ($peps as $pep) {
	array_push($peptides, pep_to_array($pep));
}


//linkSite block
$linkSite = array();
array_push($linkSite, array('peptideId' => 0, 'linkSite' => $clPos1));
array_push($linkSite, array('peptideId' => 1, 'linkSite' => $clPos2));

//peak block
$peaks = array();
foreach ($peaklist as $peak) {
	$parts = preg_split('/\s+/', $peak);
	array_push($peaks, array('mz' => floatval($parts[0]), 'intensity' => floatval($parts[1])));
}

//annotation block
$tol = array("tolerance" => $ms2Tol, "unit" => $tolUnit);
$modifications = array();
array_push($modifications, array('aminoacid' => 'C', 'id' => 'cm', 'mass' => 160.030648));

$ions = array();
array_push($ions, array('type' => 'PeptideIon'));
if ($method == "HCD" or $method == "CID") {
	array_push($ions, array('type' => 'BIon'));
	array_push($ions, array('type' => 'YIon'));	
};
if ($method == "EThcD" or $method == "ETciD") {
	array_push($ions, array('type' => 'BIon'));
	array_push($ions, array('type' => 'CIon'));
	array_push($ions, array('type' => 'YIon'));
	array_push($ions, array('type' => 'ZIon'));		
};
if ($method == "ETD") {
	array_push($ions, array('type' => 'CIon'));
	array_push($ions, array('type' => 'ZIon'));	
};

$cl = array('modMass' => $clModMass);

$annotation = array('fragmentTolerance' => $tol, 'modifications' => $modifications, 'ions' => $ions, 'cross-linker' => $cl, 'precursorCharge' => $preCharge);

//final array
$arr = array('Peptides' => $peptides, 'LinkSite' => $linkSite, 'peaks' => $peaks, 'annotation' => $annotation);

//var_dump($arr);
echo json_encode($arr);



/*"annotation":{
	"xiVersion":"1.5.632",
	"fragementTolerance":"20.0 ppm",
	"modifications":[
		{"aminoacid":"C", "id":"cm", "mass":160.030648, "massDifference":57.02146400000001}
	],
	"ions":[
		{"type":"PeptideIon"},
		{"type":"BIon"},
		{"type":"YIon"}
	]
}*/

?>