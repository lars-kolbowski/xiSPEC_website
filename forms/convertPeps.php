<?php

$pepsStr = $_POST["peps"];
$peps = explode(";", $pepsStr);

function get_link_sites($pep, $pep_index){
	$linkSitesArr = array();

	$pep_nomods = preg_replace ( '/[a-z]+/' , '' , $pep);	
	preg_match_all( "/#[0-9]?/" , $pep_nomods, $matches, PREG_OFFSET_CAPTURE);

	foreach ($matches[0] as $matchgroup) {
		//extract cl number
		$cl_index = (preg_match("/[0-9]+/", $matchgroup[0], $match) != 0) ? $match : 0;

		array_push($linkSitesArr, array('id' => $cl_index, 'peptideId' => $pep_index, 'linkSite' => $matchgroup[1]));
	}	

	return $linkSitesArr;
}


function pep_to_array($pep){
	$mods = array();
	$pep = preg_replace( '/#[0-9]?/' , '' , $pep);	
	$pep_nomods = str_split(preg_replace ( '/[a-z]+/' , '' , $pep));
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


$linkSites = array();
$peptides = array();

$i = 0;
foreach ($peps as $pep) {
	array_push($peptides, pep_to_array($pep));
	//var_dump(get_link_sites($pep, $i));
	//array_push($linkSites, get_link_sites($pep, $i));
	$linkSites = array_merge($linkSites, get_link_sites($pep, $i));
	$i++;
}


//final array
$arr = array('Peptides' => $peptides, 'LinkSite' => $linkSites);

//var_dump($arr);
echo json_encode($arr);
?>