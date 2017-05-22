<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("functions.php");

$mods = [];
if(isset($_POST['mods'])){
	$mods = $_POST['mods'];
	$modMasses = $_POST['modMasses'];
	$modSpecificities = $_POST['modSpecificities'];
}

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
$postData = array('Peptides' => $peptides, 'LinkSite' => $linkSites, 'peaks' => $peaks, 'annotation' => $annotation);

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
//var_dump($response);

// Check for errors
if($response === FALSE){
    die(curl_error($ch));
}
if ($response === ""){
	var_dump($postJSON);
	die("xiAnnotator experienced a problem. Please try again later!");
}
//var_dump($postJSON);
//var_dump($response);
//die();
?>

<!doctype html>
<html>

<head>
	<?php include("xiSPEC_scripts.php");?>
	
	<!-- not needed in CLMS-UI -->
	<script type="text/javascript" src="vendor/download.js"></script>
	<!-- not needed in CLMS-UI -->

	<script type="text/javascript" src="src/model.js"></script>
	<script type="text/javascript" src="src/SpectrumView2.js"></script>
	<script type="text/javascript" src="src/FragmentationKeyView.js"></script>
	<!-- <script type="text/javascript" src="js/PrecursorInfoView.js"></script>	-->
	<script type="text/javascript" src="src/FragKey/KeyFragment.js"></script>
	<script type="text/javascript" src="src/graph/Graph.js"></script>
	<script type="text/javascript" src="src/graph/Peak.js"></script>
	<script type="text/javascript" src="src/graph/Fragment.js"></script>
	<!--  <script type="text/javascript" src="src/graph/IsotopeCluster.js"></script> -->
	<link rel="stylesheet" href="css/spectrumViewWrapper.css" />
	<link rel="stylesheet" href="css/spectrum.css" />

	<script>


	SpectrumModel = new AnnotatedSpectrumModel();


	$(function() {

		//selects everything in input field on click
		$("input[type='text']").on("click", function () {
			console.log("test");
   			$(this).val("");
		});
		//Restrict input to mzrange fields 
		$(".mzrange").keydown(function (e) {
	        // Allow: backspace, delete, tab, escape, enter and .
	        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
	             // Allow: Ctrl+A
	            (e.keyCode == 65 && e.ctrlKey === true) ||
	             // Allow: Ctrl+C
	            (e.keyCode == 67 && e.ctrlKey === true) ||
	             // Allow: Ctrl+X
	            (e.keyCode == 88 && e.ctrlKey === true) ||
	             // Allow: home, end, left, right
	            (e.keyCode >= 35 && e.keyCode <= 39)) {
	                 // let it happen, don't do anything
	                 return;
	        }
	        // Ensure that it is a number and stop the keypress
	        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
	            e.preventDefault();
	        }
	    });

		_.extend(window, Backbone.Events);
		window.onresize = function() { window.trigger('resize') };


		window.Spectrum = new SpectrumView({model: SpectrumModel, el:"#spectrumDiv"});
		window.FragmentationKey = new FragmentationKeyView({model: SpectrumModel, el:"#spectrumDiv"});
		//window.precursorInfoView = new PrecursorInfoView({model: SpectrumModel, el:"#precursorInfo"});
		var json_data = <?php echo $response; ?>;
		var json_req = <?php echo $postJSON ?>;
		//console.log("json:" + json_data);
		SpectrumModel.set({JSONdata: json_data, JSONrequest: json_req});
		//SpectrumModel.set({JSONdata: json_data});

});
	</script>
</head>

<body>
	<div id="spectrumPanelWrapper">
		<div>
		<a href="#" style="visibility:hidden">edit data</a>
		</div>
		<div id='spectrumDiv'>
			<div id='spectrumControls'>
				<label>lossy labels
					<input id="lossyChkBx" type="checkbox">
				</label>
				<button class="btn btn-1 btn-1a" id="reset">reset zoom</button>
				<button class="btn btn-1 btn-1a" id="clearHighlights">clear highlights</button>
				<button class="btn btn-1 btn-1a" id="downloadSVG">Download SVG</button>
				<label class="btn">measure
					<input id="measuringTool" type="checkbox">
				</label>
				<label class="btn">move labels
					<input id="moveLabels" type="checkbox">
				</label>
				</br>
				<span id="precursorInfo" style="font-size:small">Precursor</span>
				<label for="colorSelector">Change color scheme:</label>
				<select id="colorSelector">
					<option value="RdBu">Red&Blue</option>
					<option value="BrBG">Brown&Teal</option>
					<option value="PiYG">Pink&Green</option>
					<option value="PRGn">Purple&Green</option>
					<option value="PuOr">Orange&Purple</option>
				</select>
				<form id="setrange">
					m/z Range:
					<input type="text" class="mzrange" id="xleft" size="5">
					<input type="text" class="mzrange" id="xright" size="5">
					<input type="submit" value="set range">
					<span id="range-error"></span>
				</form>
			</div>
			<svg id="spectrumSVG" style="width:100%; height:100%"></svg>
			<div id="measureTooltip"></div>
			
		</div>
	</div>

</body>

</html>
