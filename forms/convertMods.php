<?php
$pepsStr = urldecode($_GET["peps"]);

$pepsStr_noCL = preg_replace( '/#[0-9]?/' , '' , $pepsStr);	
preg_match_all('/[a-z0-9]+/', $pepsStr_noCL, $matches, PREG_OFFSET_CAPTURE);

$modifications = array();
$aminoAcids = array();
$i = 0;
foreach ($matches[0] as $matchgroup) {

	//var_dump(in_array($matchgroup[0], $modifications, TRUE));
	$index = array_search($matchgroup[0], $modifications);

	///var_dump($modifications);
	//var_dump($matchgroup[0]);
	//var_dump(array_search($matchgroup[0], $modifications));
	if ($index === FALSE){
		array_push($modifications, $matchgroup[0]);
		array_push($aminoAcids, $pepsStr_noCL[$matchgroup[1]-1]);
	}
	else{
		if (strpos($aminoAcids[$index], $pepsStr_noCL[$matchgroup[1]-1]) === FALSE)
			$aminoAcids[$index] .= ",".$pepsStr_noCL[$matchgroup[1]-1];

	}
	$i++;
}


//known modifications

$json = file_get_contents('http://129.215.14.63/xiAnnotator/annotate/knownModifications');
$obj = json_decode($json);
$knownModifications = $obj->modifications;


$modificationsJSON = array();
$i = 0;
foreach ($modifications as $mod) {
	$mass = "";
	foreach($knownModifications as $kmod) {
	    if ($mod == $kmod->id) {
	        $mass = $kmod->mass;
	        break;
	    }
	}

	array_push($modificationsJSON, array('name' => $mod, 'mass' => $mass, 'aminoAcid' => $aminoAcids[$i]));
	$i++;
}

//array_push($modificationsJSON, array('id' => $i,'name' => $matchgroup[0], 'mass' => "", 'aminoAcid' => $pepsStr_noCL[$matchgroup[1]-1]));
//final array
$arr = array('data' => $modificationsJSON);

//var_dump($arr);
echo json_encode($arr);
?>