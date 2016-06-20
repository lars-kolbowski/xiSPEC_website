<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("functions.php");


$pepsStr = $_POST["peps"];
$clModMass = floatval($_POST['clModMass']);
//$ms2Tol = floatval($_POST['ms2Tol'])." ".$_POST['tolUnit'];
$ms2Tol = floatval($_POST['ms2Tol']);
$tolUnit = $_POST['tolUnit'];
$peaklist = $_POST['peaklist'];
$method = $_POST['fragMethod'];
$preCharge = intval($_POST['preCharge']);

//$peaklist = explode('<br />',nl2br($peaklist));
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
$arr = array('Peptides' => $peptides, 'LinkSite' => $linkSites, 'peaks' => $peaks, 'annotation' => $annotation);

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