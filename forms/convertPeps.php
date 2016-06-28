<?php

$pepsStr = $_POST["peps"];
$peps = explode(";", $pepsStr);

require("../functions.php");


$linkSites = array();
$peptides = array();

$i = 0;
foreach ($peps as $pep) {
	array_push($peptides, pep_to_array($pep));
	$linkSites = array_merge($linkSites, get_link_sites($pep, $i));
	$i++;
}

// $command = escapeshellcmd('../python/calc_pep_mass.py -s '.$pepsStr);
// $output = shell_exec($command);
// var_dump($output);



//final array
$arr = array('Peptides' => $peptides, 'LinkSite' => $linkSites);

//var_dump($arr);
echo json_encode($arr);
?>