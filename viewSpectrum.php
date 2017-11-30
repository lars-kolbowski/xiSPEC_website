<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("php/functions.php");

if(isset($_GET['db']) && !empty($_GET['db']))
	require("php/logAccess.php");


if (empty($_POST)){
	$dbView = TRUE;
}
else{
	$dbView = FALSE;
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

	//$method = $_POST['fragMethod'];
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

	$annotation = array('fragmentTolerance' => $tol, 'modifications' => $modifications, 'ions' => $ions, 'cross-linker' => $cl, 'precursorCharge' => $preCharge, 'custom' => "LOWRESOLUTION:false"); //ToDo: LOWRESOLUTION: true setting

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
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>xiSPEC</title>
			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
			<meta name="description" content="common platform for downstream analysis of CLMS data" />
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="apple-mobile-web-app-capable" content="yes">
			<meta name="apple-mobile-web-app-status-bar-style" content="black">
			<link rel="icon" type="image/ico" href="images/logos/favicon.ico">
			<link rel="stylesheet" href="./css/style.css" />
			<link rel="stylesheet" href="./css/style2.css" />
			<link rel="stylesheet" href="./css/settings.css" />
			<link rel="stylesheet" href="./css/tooltip.css">
			<link rel="stylesheet" href="./css/spectrumViewWrapper.css">
			<link rel="stylesheet" href="./css/validationPage.css">
			<link rel="stylesheet" href="./css/dropdown.css">
			<link rel="stylesheet" type="text/css" href="./vendor/bootstrap/css/bootstrap.min.css"/>
			<link rel="stylesheet" type="text/css" href="./css/font-awesome.min.css"/>
			<?php include("xiSPEC_scripts.php");?>

			<script type="text/javascript" src="./vendor/jscolor.min.js"></script>
			<script type="text/javascript" src="./vendor/c3.js"></script>
			<script type="text/javascript" src="./vendor/split.js"></script>
			<script type="text/javascript" src="./vendor/svgexp.js"></script>
			<script type="text/javascript" src="./vendor/spin.js"></script>
			<script type="text/javascript" src="./vendor/byrei-dyndiv_1.0rc1.js"></script>
			<script type="text/javascript" src="./vendor/download.js"></script>
			<script type="text/javascript" src="./vendor/bootstrap/js/bootstrap.min.js"></script>
			<script type="text/javascript" src="./vendor/dataTables.bootstrap.min.js"></script>


			<!-- Spectrum view .js files -->
			<script type="text/javascript" src="./js/app.js"></script>
			<script type="text/javascript" src="./src/model.js"></script>
			<script type="text/javascript" src="./src/SpectrumView2.js"></script>
			<script type="text/javascript" src="./src/FragmentationKeyView.js"></script>
			<script type="text/javascript" src="./src/PrecursorInfoView.js"></script>
			<script type="text/javascript" src="./src/SpectrumSettingsView.js"></script>
			<script type="text/javascript" src="./js/PeptideView.js"></script>
			<script type="text/javascript" src="./src/PepInputView.js"></script>
			<script type="text/javascript" src="./src/ErrorIntensityPlotView.js"></script>
			<script type="text/javascript" src="./src/FragKey/KeyFragment.js"></script>
			<script type="text/javascript" src="./src/graph/Graph.js"></script>
			<script type="text/javascript" src="./src/graph/Peak.js"></script>
			<script type="text/javascript" src="./src/graph/Fragment.js"></script>
<?php if($dbView)
echo 	'<script type="text/javascript" src="./js/specListTable.js"></script>
		<script type="text/javascript" src="./js/altListTable.js"></script>';
?>
			<script>

		var model_vars = {baseDir: "", xiAnnotatorBaseURL: "http://xi3.bio.ed.ac.uk/xiAnnotator/"};

		SpectrumModel = new AnnotatedSpectrumModel(model_vars);
		SettingsSpectrumModel = new AnnotatedSpectrumModel(model_vars);

		SpectrumModel.otherModel = SettingsSpectrumModel;
		SettingsSpectrumModel.otherModel = SpectrumModel;
		$(function() {

		<?php
			if($dbView){
				echo 'window.dbView = true;';
			}
			else{
				echo 'window.dbView = false;';
				echo 'var json_data = '.$response.';';
				echo 'var json_req = '.$postJSON.';';
			}
		?>

		if(dbView){
			window.SpectrumModel.requestId = "0";
			$('#bottomDiv').show();
			window.initSpinner = new Spinner({scale: 5}).spin (d3.select("#topDiv").node());
		}
		else{
			console.log(json_req);
			$("#topDiv-overlay").css("z-index", -1);
			$('#dbControls').hide();
			$('#bottomDiv').hide();
			$('#altDiv').hide();
		}


		_.extend(window, Backbone.Events);
		window.onresize = function() { window.trigger('resize') };

		window.Spectrum = new SpectrumView({model: SpectrumModel, el:"#spectrumPanel"});
		window.FragmentationKey = new FragmentationKeyView({model: SpectrumModel, el:"#spectrumPanel"});
		window.InfoView = new PrecursorInfoView ({model: SpectrumModel, el:"#spectrumPanel"});
		// window.ErrorIntensityPlot = new ErrorIntensityPlotView({
		// 	model: SpectrumModel,
		// 	el:"#spectrumPanel",
		// 	margin: {top: 110, right: 60, bottom: 50, left: 65}
		// });

		window.ErrorIntensityPlot = new ErrorIntensityPlotView({
			model: SpectrumModel,
			el:"#errIntDiv",
			margin: {top: 10, right: 60, bottom: 40, left: 65},
			svg: "#errIntSVG",
			alwaysShow: true,
		});

		window.SettingsView = new SpectrumSettingsView({model: SettingsSpectrumModel, el:"#settingsWrapper"});

		if(!dbView){
			SpectrumModel.set({JSONdata: json_data, JSONrequest: json_req});
			var json_data_copy = jQuery.extend({}, json_data);
			//SpectrumModel.otherModel = SettingsSpectrumModel;
			SettingsSpectrumModel.set({JSONdata: json_data_copy, JSONrequest: json_req});
			//SettingsSpectrumModel.otherModel = SpectrumModel;
			//window.SettingsView.render();
		}
		else {
			window.specListTable = new specListTableView({model: SpectrumModel, el:"#specListWrapper"});
			window.altListTable = new altListTableView({model: SpectrumModel, el:"#altListWrapper"});
		}

});

// ToDo: change to BB handling
function loadSpectrum(rowdata){

	console.log(rowdata['alt_count']);
	var id = rowdata['id'];
	var mzid = rowdata['mzid'];

	$("#altListId").html("Alternatives for "+rowdata['mzid']);

	if(rowdata['alt_count'] > 1){

		$('#nav-altListTable').removeClass('disabled');
		$('#altExpNum').text("(" + rowdata['alt_count'] + ")");
		window.altListTable.DataTable.ajax.url( "php/getAltList.php?id=" + mzid).load();
	}
	else{
		$('#altExpNum').text("(0)");
		$('#nav-altListTable').addClass('disabled');
	}

	$.ajax({
		url: 'php/getSpectrumReq.php?i='+id,
		type: 'GET',
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		success: function (returndata) {
			var json = JSON.parse(returndata);
			window.SpectrumModel.requestId = id;
			window.SpectrumModel.mzid = mzid;
			//console.log(window.SpectrumModel.requestId);
			window.SpectrumModel.request_annotation(json);
			//var json_data_copy = jQuery.extend({}, window.SpectrumModel.JSONdata);
			//var json_req = window.SpectrumModel.get('JSONrequest');
			//window.SpectrumModel.settingsModel = SettingsSpectrumModel;
			//window.SettingsSpectrumModel.set({JSONdata: json_data_copy, JSONrequest: json_req, realModel: SpectrumModel}); //JSONrequest necessary?

		}
	});
};


    </script>
    </head>

    <body>
        <!-- Main -->
        <div id="mainView">

            <div class="mainContent">

            	 <div id="topDiv"><!--style="height: calc(60% - 5px);">-->
            	 <div class="overlay" id="topDiv-overlay"></div>
	                <div id="spectrumPanel">

						<div class="dynDiv" id="settingsWrapper">
							<div class="dynDiv_moveParentDiv">
								<span class="dynTitle">Settings</span>
								<i class="fa fa-times-circle closeButton settingsCancel" id="closeSettings"></i>
							</div>
							<div class="dynDiv_resizeDiv_tl draggableCorner"></div>
							<div class="dynDiv_resizeDiv_tr draggableCorner"></div>
							<div class="dynDiv_resizeDiv_bl draggableCorner"></div>
							<div class="dynDiv_resizeDiv_br draggableCorner"></div>
						</div>
		            	<div id="spectrumControls">
		            		<i class="btn btn-1a btn-topNav fa fa-home fa-xi" style='top: 0px;' onclick="window.location = 'index.php';" title="Home"></i>
		            		<i class="btn btn-1a btn-topNav fa fa-github fa-xi" onclick="window.open('https://github.com/Rappsilber-Laboratory/xiSPEC/issues', '_blank');" title="GitHub issue tracker" style="cursor:pointer;"></i>
	            			<i class="btn btn-1a btn-topNav fa fa-download" aria-hidden="true" id="downloadSVG" title="download SVG" style="cursor: pointer;"></i>
							<label class="btn" title="toggle moveable labels on/off">Move Labels<input id="moveLabels" type="checkbox"></label>
		            		<button id="clearHighlights" class="btn btn-1 btn-1a">Clear Highlights</button>
		            		<label class="btn" title="toggle measure mode on/off">Measure<input id="measuringTool" type="checkbox"></label>
		            		<form id="setrange">
		            			<label class="btn" title="m/z range" style="cursor: default;">m/z:</label>
								<label class="btn" for="lockZoom" title="Lock current zoom level" id="lock" class="btn">🔓</label>
		            			<input type="text" id="xleft" size="7" title="m/z range from:">
		            			<span>-</span>
		            			<input type="text" id="xright" size="7" title="m/z range to:">
		            			<input type="submit" id="rangeSubmit" value="Set" class="btn btn-1 btn-1a" style="display: none;">
		            			<span id="range-error"></span>
		            			<button id="reset" title="Reset to initial zoom level" class="btn btn-1 btn-1a">Reset Zoom</button>
		            			<input id="lockZoom" type="checkbox" style="visibility: hidden;">
		            		</form>
		            		<!-- <button id="toggleView" title="Toggle between quality control/spectrum view" class="btn btn-1 btn-1a">error/int</button> -->
		    				<i id="toggleSettings" title="Show/Hide Settings" class="btn btn-1a btn-topNav fa fa-cog" aria-hidden="true"></i>
		    				<span id="dbControls">

		    					<?php if(!isset($_SESSION['db'])) echo '<i id="saveDB" title="Save" class="btn btn-1a btn-topNav fa fa-floppy-o" aria-hidden="true"></i>';?>
								<!-- <i id="prevSpectrum" title="Previous Spectrum" class="btn btn-1a btn-topNav fa fa-arrow-left" aria-hidden="true"></i> -->
								<i id="toggleSpecList" title="Show/Hide Spectra list" class="btn btn-1a btn-topNav fa fa-bars" aria-hidden="true"></i>
								<!-- <i id="nextSpectrum" title="Next Spectrum" class="btn btn-1a btn-topNav fa fa-arrow-right" aria-hidden="true"></i> -->
							</span>
						</div>
						<div class="heightFill">
						    <svg id="spectrumSVG"></svg>
						    <div id="measureTooltip"></div>
						</div>
						<div id="errIntDiv">
							<div class='subViewControls'>
								<span>Error-Intensity plot</span>
								<i class="fa fa-angle-double-up pointer" id="dockErrInt" aria-hidden="true" title="show error/intensity plot" style="display:none;"></i>
								<i class="fa fa-angle-double-down pointer" id="minErrInt" aria-hidden="true" title="hide error/intensity plot"></i>
							</div>
							<div class="subViewContent">
								<svg id="errIntSVG"></svg>
							</div>
						</div>
					</div>
				</div><!-- end top div -->
				<div id="bottomDiv" class="tableDiv">
				<i class="fa fa-times-circle closeButton closeTable" id="specListClose"></i>

					<ul class="nav nav-tabs">
						<li class="active">
							<a data-toggle="tab" href="#tab-specListTable">Spectra List</a>
						</li>
						<li id="nav-altListTable">
							<a data-toggle="tab" href="#tab-altListTable">Alternative Explanations <span id="altExpNum"></span></a>
						</li>
					</ul>

					<div class="tab-content">
						<div id="tab-specListTable" class="tab-pane fade in active">
							<div id="specListWrapper" class="listWrapper">
							</div>
						</div>
						<div id="tab-altListTable" class="tab-pane fade">
							<div id="altListWrapper" class="listWrapper">
								<!-- <div id="altList_main">
									<table id="altListTable" width="100%" style="text-align:center;"></table>
								</div> -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- MAIN -->

		<!-- Modal -->
		<div id="saveModal" role="dialog" class="modal" style="background: #333; width:650px; text-align: center; z-index: 2147483648;">
			<div class="header" style="background: #750000; color:#fff;">Save your dataset</div>
			<div class="content" id="saveModal_content">
				<div id="saveDBerror"></div>
				<form id='saveDB_form'>
					<label class="flex-row label">
						Name: <div class="flex-grow"><input class="form-control" required length=30 id="saveDbName" name="dbName" type="text" placeholder="Enter a name for your dataset"></div>
					</label>
					<label class="flex-row label" style="line-height: 1.5em; margin: 1.5em 0em;">
						Public: <input id="publicDBchkBox" class="pointer" name="public" type="checkbox"> <span style="text-transform: initial; letter-spacing: initial; color: #ccc;">(checking this will allow anyone who knows the name of your data set to view it.)</span>
					</label>
					<label class="flex-row label" id="dbPassLabel">
						Password: <div class="flex-grow"><input class="form-control" required length=30 id="saveDbPass" name="dbPass" type="password" placeholder="Enter password"></div>
						<div class="flex-grow"><input class="form-control" required length=30 id="saveDbPassControl" name="dbPass" type="password" placeholder="Retype password"></div>
					</label>

					<input type="submit" id="saveDataSet" class="btn btn-1 btn-1a" value="save">
				</form>
	<!-- 			<div id="shareLink" class="btn clearfix" style="font-size: 1.1em;margin:10px 5px;">
					<button id="requestShareLink" type="submit" class="btn btn-1a" >Click here to generate a link for later access or sharing</button>
				</div> -->
			</div>
		</div>
		<!-- End Modal -->
	</body>
</html>
