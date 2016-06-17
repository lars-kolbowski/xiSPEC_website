<?php

$pepsStr = $_GET["peps"];

$peps = explode(";", $pepsStr);

function pep_to_array($pep){
	$mods = array();
	$pep_nomods = str_split(preg_replace ( '/([a-z]+)/' , '' , $pep));
	$pep_array = array();

	foreach ($pep_nomods as $letter) {
		array_push($pep_array, array('aminoAcid' => $letter, 'modification' => ''));
	}

	preg_match_all('/[a-z]+/', $pep, $matches, PREG_OFFSET_CAPTURE);

	$offset = 1;
	foreach ($matches[0] as $matchgroup) {
		$pep_array[$matchgroup[1] - $offset]['Modification'] = $matchgroup[0];
		$offset += strlen($matchgroup[0]);
	}
	return array('sequence' => $pep_array);
}


//peptides block
$peptides = array();
foreach ($peps as $pep) {
	array_push($peptides, pep_to_array($pep));
}



echo json_encode($peptides);
?>