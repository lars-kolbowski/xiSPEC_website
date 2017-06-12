<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("functions.php");

if (empty($_POST)){
	session_start();
	$dir = 'sqlite:../dbs/'.session_id().'.db';
	$dbh = new PDO($dir) or die("cannot open the database");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$query =  "SELECT * FROM mzids WHERE id=0 LIMIT 1";
	foreach ($dbh->query($query) as $row)
	{
	    $mzid = $row['mzid'];
	    $requestId = $row['id'];
	}

	$query =  "SELECT * FROM jsonReqs WHERE mzid='".$mzid."' ORDER BY rank ASC LIMIT 1";

	foreach ($dbh->query($query) as $row)
	{
	    $postJSON = $row['json'];
	}

}
else{
	$requestId = -1;
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

	// array_push($ions, array('type' => 'PeptideIon'));
	// if ($method == "HCD" or $method == "CID") {
	//     array_push($ions, array('type' => 'BIon'));
	//     array_push($ions, array('type' => 'YIon')); 
	// };
	// if ($method == "EThcD" or $method == "ETciD") {
	//     array_push($ions, array('type' => 'BIon'));
	//     array_push($ions, array('type' => 'CIon'));
	//     array_push($ions, array('type' => 'YIon'));
	//     array_push($ions, array('type' => 'ZIon'));     
	// };
	// if ($method == "ETD") {
	//     array_push($ions, array('type' => 'CIon'));
	//     array_push($ions, array('type' => 'ZIon')); 
	// };

	$cl = array('modMass' => $clModMass);

	$annotation = array('fragmentTolerance' => $tol, 'modifications' => $modifications, 'ions' => $ions, 'cross-linker' => $cl, 'precursorCharge' => $preCharge);

	//final array
	$postData = array('Peptides' => $peptides, 'LinkSite' => $linkSites, 'peaks' => $peaks, 'annotation' => $annotation);

	$postJSON = json_encode($postData);
	//var_dump(json_encode($postData));
	//die();
}

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
    
    echo ("xiAnnotator experienced a problem. Please try again later!");
    var_dump($postJSON);
    die();
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

		<link rel="stylesheet" href="./css/style.css" />
        <link rel="stylesheet" href="./css/style2.css" />
        <link rel="stylesheet" href="./css/tooltip.css">
        <link rel="stylesheet" href="./css/spectrumViewWrapper.css">
        <link rel="stylesheet" href="./css/validationPage.css">
        <link rel="stylesheet" href="./css/dropdown.css">
		<?php include("xiSPEC_scripts.php");?>

        <script type="text/javascript" src="./vendor/jscolor.min.js"></script>
        <script type="text/javascript" src="./vendor/c3.js"></script>
        <script type="text/javascript" src="./vendor/split.js"></script>
        <script type="text/javascript" src="./vendor/svgexp.js"></script>
        <script type="text/javascript" src="./vendor/spin.js"></script>
        <script type="text/javascript" src="./vendor/byrei-dyndiv_1.0rc1.js"></script>

        <!-- Spectrum view .js files -->
        <script type="text/javascript" src="./src/model.js"></script>
        <script type="text/javascript" src="./src/SpectrumView2.js"></script>
        <script type="text/javascript" src="./src/FragmentationKeyView.js"></script>
        <script type="text/javascript" src="./src/PrecursorInfoView.js"></script>
		<script type="text/javascript" src="./js/PeptideView.js"></script>
		<script type="text/javascript" src="./js/PepInputView.js"></script>	
        <script type="text/javascript" src="./src/ErrorIntensityPlotView.js"></script>     
        <script type="text/javascript" src="./src/FragKey/KeyFragment.js"></script>
        <script type="text/javascript" src="./src/graph/Graph.js"></script>
        <script type="text/javascript" src="./src/graph/Peak.js"></script>
        <script type="text/javascript" src="./src/graph/Fragment.js"></script>
        <script>



    SpectrumModel = new AnnotatedSpectrumModel();
    SettingsSpectrumModel = new AnnotatedSpectrumModel();

    $(function() {


        // var spinner = new Spinner({scale: 5}).spin (d3.select("#mainContent").node());
            
        // spinner.stop();

        _.extend(window, Backbone.Events);
        window.onresize = function() { window.trigger('resize') };

		window.modTable = $('#modificationTable').DataTable( {
			"paging":   false,
		    "ordering": false,
		    "info":     false,
		    "searching":false,
		    "processing": true,
		    "serverSide": true,
		    "ajax": "forms/convertMods.php?peps=",
		    "columns": [
		        { "data": "id" },
		    	{},
		        {},
		        { "data": "aminoAcid" },
		        ],

		    "columnDefs": [
		    	{
					"render": function ( data, type, row, meta ) {
						return '<input class="form-control" id="modName_'+meta.row+'" name="mods[]" readonly type="text" value='+data+'>';
					},
					"class": "invisible",
					"targets": 0,
				},
				{
					"render": function ( data, type, row, meta ) {
						return row['id'];
					},
					"targets": 1,
				},
			{
				"render": function ( data, type, row, meta ) {
					data = 0;
					for (var i = 0; i < window.SettingsSpectrumModel.userModifications.length; i++) {
						if(window.SettingsSpectrumModel.userModifications[i].id == row.id){
							data = window.SettingsSpectrumModel.userModifications[i].mass;
							var found = true;
						}
					}
					if (!found){
						for (var i = 0; i < window.SettingsSpectrumModel.knownModifications['modifications'].length; i++) {
							if(window.SettingsSpectrumModel.knownModifications['modifications'][i].id == row.id)
								data = window.SettingsSpectrumModel.knownModifications['modifications'][i].mass;
						}
					}
					return '<input class="form-control" id="modMass_'+meta.row+'" row="'+meta.row+'" name="modMasses[]" type="number" min=0 step=0.0001 required value='+data+' autocomplete=off>';
				},
				"targets": 2,
			},
			{
				"render": function ( data, type, row, meta ) {
					for (var i = 0; i < window.SettingsSpectrumModel.userModifications.length; i++) {
						if(window.SettingsSpectrumModel.userModifications[i].id == row.id){
							data = window.SettingsSpectrumModel.userModifications[i].aminoAcids.join("");
							var found = true;
						}
					}
					if (!found){				
						for (var i = 0; i < window.SpectrumModel.knownModifications['modifications'].length; i++) {
							if(window.SettingsSpectrumModel.knownModifications['modifications'][i].id == row.id){						
								data = data.split(",");
								data = _.union(data, window.SettingsSpectrumModel.knownModifications['modifications'][i].aminoAcids);
								data.sort();
								data = data.join("");
								
							}
						}
					}
					return '<input class="form-control" id="modSpec_'+meta.row+'" row="'+meta.row+'" name="modSpecificities[]" type="text" required value='+data+' autocomplete=off>'
				},
				"targets": 3,
			}
            ]
		});
        window.Spectrum = new SpectrumView({model: SpectrumModel, el:"#spectrumPanel"});
        window.FragmentationKey = new FragmentationKeyView({model: SpectrumModel, el:"#spectrumPanel"});
        window.InfoView = new PrecursorInfoView ({model: SpectrumModel, el:"#spectrumPanel"});
        window.ErrorIntensityPlot = new ErrorIntensityPlotView({model: SpectrumModel, el:"#spectrumPanel"});

		//window.SettingsPeptideView = new PeptideView({model: SettingsSpectrumModel, el:"#peptideDiv"});
        var json_data = <?php echo $response; ?>;
        var json_req = <?php echo $postJSON ?>;
        console.log(json_req);
        SpectrumModel.set({JSONdata: json_data, JSONrequest: json_req});
      	<?php print("SpectrumModel.requestId = ".$requestId).";"; ?>
         // SpectrumModel.userModifications = <?php //echo json_encode($modifications); ?>;

		var json_data_copy = jQuery.extend({}, json_data);

		SpectrumModel.settingsModel = SettingsSpectrumModel;
        SettingsSpectrumModel.set({JSONdata: json_data_copy, JSONrequest: json_req});
		window.SettingsPepInputView = new PepInputView({model: SettingsSpectrumModel, el:"#settingsPeptide"});

		//SpecListTable
		window.specListTable = $('#specListTable').DataTable( {
			// "paging":   false,
		 //    "ordering": false,
		 //    "info":     false,
		 	"dom": '<"specListToolbar">frtip',
		    "searching": true,
		    // "processing": true,
		    // "serverSide": true,
		    "ajax": "php/getSpecList.php",
		    "columns": [
		        { "data": "id" },
		        { "data": "mzid" },
				{ "data": "pep1" },
				{ "data": "pep2" },
				{ "data": "linkpos1" },	
				{ "data": "linkpos2" },	
				{ "data": "passThreshold" },			
		        ],
			"aoSearchCols": [
				null,
				null,
				null,
				null,
				null,
				null,
				{ "sSearch": "1" },
			],
			"createdRow": function( row, data, dataIndex ) {
				if ( data[6] == "0" )         
					$(row).addClass('red');
			 },
		    "columnDefs": [
		    	{
					"class": "invisible",
					"targets": [0, 6],
				},	
				{ 
					"render": function ( data, type, row, meta ) {
						if (data == 0)
							return '';
						else
							return data;
					},
					"searchable": false, 
					"targets": [4, 5]
				}		
            ]
		});

		$("div.specListToolbar").html('Filter: <label class="btn"><input id="passThreshold" type="checkbox" checked>passing threshold</label>');

		$('#passThreshold').on( 'click', function () {
			if (this.checked){
			    window.specListTable
			        .columns( 6 )
			        .search( "1" )
			        .draw();				
			}
			else{
			    window.specListTable
			        .columns( 6 )
			        .search( "" )
			        .draw();
			}

		} );

		window.specListTable.on('click', 'tbody tr', function() {
			console.log('id : ', window.specListTable.row(this).data()[0]);
			loadSpectrum(window.specListTable.row(this).data()[0]);
		});

		//settings panel - put into model? or extra view?
		function render_settings(){
			window.SettingsPepInputView.render();

			//ions
			SpectrumModel.JSONdata.annotation.ions.forEach(function(ion){
				$('#'+ion.type).attr('checked', true);
			});
			var ionSelectionArr = new Array();
			$('.ionSelectChkbox:checkbox:checked').each(function(){
			    ionSelectionArr.push($(this).val());
			});
			$('#ionSelection').val(ionSelectionArr.join(", "));

			//$("#settingsFragmentation").val("<?php //echo $method; ?>");
			$("#settingsPeaklist").val(SpectrumModel.peaksToMGF()); 
			$("#settingsPrecursorZ").val(SpectrumModel.JSONdata.annotation.precursorCharge);
			$("#settingsTolerance").val(parseInt(SpectrumModel.JSONdata.annotation.fragementTolerance));
			$("#settingsToleranceUnit").val(SpectrumModel.JSONdata.annotation.fragementTolerance.split(" ")[1]);
			$("#settingsCL").val(SpectrumModel.JSONdata.annotation['cross-linker'].modMass);
		}

		render_settings();

		$('.settingsCancel').click(function(){
			$('#settingsWrapper').hide();
			document.getElementById('highlightColor').jscolor.hide();
			var json_data_copy = jQuery.extend({}, window.SpectrumModel.JSONdata);
			SettingsSpectrumModel.set({JSONdata: json_data_copy});
			SettingsSpectrumModel.trigger("change:JSONdata");
			render_settings();
		});

		$('#toggleSettings').click(function(){
			$('#settingsWrapper').toggle();
		});

		$('#specListClose').click(function(){
			$('#specListWrapper').hide();
		})

		$('#toggleSpecList').click(function(){
			$('#specListWrapper').toggle();
		});

		$("#settingsApply").click(function(){
			$('#settingsForm').hide();
			var spinner = new Spinner({scale: 5}).spin (d3.select("#settings_main").node());
			window.SpectrumModel.request_annotation(window.SpectrumModel.JSONdata);
		});

		$('#modificationTable').on('input', 'input', function() {

			var row = this.getAttribute("row")
			var modName = $('#modName_'+row).val();
			var modMass = parseFloat($('#modMass_'+row).val());
			var modSpec = $('#modSpec_'+row).val();

			var mod = {'id': modName, 'mass': modMass, 'aminoAcids': modSpec};

			window.SpectrumModel.updateUserModifications(mod);

		 });

		$('#resetModMasses').click(function(){
			Cookies.remove('customMods');
			window.SpectrumModel.getKnownModifications();
			if(window.SpectrumModel.pepStrsMods !== undefined)
				modTable.ajax.url( "forms/convertMods.php?peps="+encodeURIComponent(window.SpectrumModel.pepStrsMods.join(";"))).load();	
		});


		$('#settings-appearance').click(function(){
			$('#settingsData').hide();
			$('#settingsAppearance').show();
		});

		$('#settings-data').click(function(){
			$('#settingsAppearance').hide();			
			$('#settingsData').show();
		});

		$('.mutliSelect input[type="checkbox"]').on('click', function() {

		    var ionSelectionArr = new Array();
			$('.ionSelectChkbox:checkbox:checked').each(function(){
			    ionSelectionArr.push($(this).val());
			});

			$('#ionSelection').val(ionSelectionArr.join(", "));

		});

		function loadSpectrum(id){
			$.ajax({
				url: 'php/getSpectrum.php?i='+id,
				type: 'GET',
				async: false,
				cache: false,
				contentType: false,
				processData: false,
				success: function (returndata) {
					var json = JSON.parse(returndata);
					window.SpectrumModel.requestId = id;
					console.log(window.SpectrumModel.requestId);
					window.SpectrumModel.request_annotation(json);
				}
			});	 			
		}

		$('#prevSpectrum').click(function(){
			loadSpectrum(window.SpectrumModel.requestId - 1)
		});

		$('#nextSpectrum').click(function(){
			loadSpectrum(window.SpectrumModel.requestId  + 1)
		});


});

function updateJScolor(jscolor) {
    console.log('#' + jscolor);
    // 'jscolor' instance can be used as a string
    window.SpectrumModel.changeHighlightColor('#' + jscolor);
}
    </script>
    </head>

    <body>
        <!-- Main -->
        <div id="mainView">
			
            <div class="mainContent">
            	<!-- <div id="topDiv">style="height: calc(60% - 5px);">-->
	                <div id="spectrumPanel">
	                	<div id="spectrumControls">
							<div class="dropdown">
								<button class="btn btn-1 btn-1a btn-drop">Labels</button>
								<div class="dropdown-content">
									<ul>
									<li><label class="btn"><input id="moveLabels" type="checkbox">Movable Labels</label></li>
									<li><label class="btn"><input id="lossyChkBx" type="checkbox">Lossy Labels</label></li>
									</ul>
								</div>
							</div>
	                		<button class="downloadButton btn btn-1 btn-1a">Download SVG</button>
	                		<button id="clearHighlights" class="btn btn-1 btn-1a">Clear Highlights</button>
	                		<label class="btn">Measure<input id="measuringTool" type="checkbox"></label>
	                		<form id="setrange">
	                			<label class="btn">m/z Range:</label>
								<label class="btn" for="lockZoom" title="Lock current zoom level" id="lock" class="btn">🔓</label>
	                			<input type="text" id="xleft" size="7">
	                			<span>-</span>
	                			<input type="text" id="xright" size="7">
	                			<input type="submit" id="rangeSubmit" value="Set" class="btn btn-1 btn-1a">                			
	                			<span id="range-error"></span>
	                			<button id="reset" title="reset to initial zoom level" class="btn btn-1 btn-1a">Reset Zoom</button>
	                		<input id="lockZoom" type="checkbox" style="visibility: hidden;"></form>
	                		<button id="toggleView" title="Click to toggle view" class="btn btn-1 btn-1a">QC</button>
	        				<button id="toggleSettings" title="Show/Hide Settings" class="btn btn-1a">&#9881;</button>
	        				<button id="prevSpectrum" title="Previous Spectrum" class="btn btn-1a">&#x2039;</button>
	        				<button id="toggleSpecList" title="Spectra list" class="btn btn-1a">&#9776;</button>
	        				<button id="nextSpectrum" title="Next Spectrum" class="btn btn-1a">&#x203A;</button>               		
	                	</div>
	                    <div class="heightFill">
	                        <svg id="spectrumSVG"></svg>
	                        <div id="measureTooltip"></div>
	                    </div>

						<div class="dynDiv" id="specListWrapper" style="display: none; z-index: 2; left: 6%; top: 11%;">
							<div class="dynDiv_moveParentDiv" style="cursor: move;">
								<span class="dynTitle">Spectra List</span>
								<i class="fa fa-times-circle closeButton" id="specListClose"></i>
							</div>
							<div class="dynDiv_resizeDiv_tl" style="cursor: nw-resize;"></div>
							<div class="dynDiv_resizeDiv_tr" style="cursor: ne-resize;"></div>
							<div class="dynDiv_resizeDiv_bl" style="cursor: sw-resize;"></div>
							<div class="dynDiv_resizeDiv_br" style="cursor: se-resize;"></div>

							<div id="specList_main" style="color: #000">
							<table id="specListTable" class="display" width="100%" style="text-align:center;">
								<thead>
									<tr>
									    <th>internal_id</th>
									    <th>id</th>
									    <th>peptide 1</th>
									    <th>peptide 2</th>
									    <th>CL position 1</th>
									    <th>CL position 2</th>
									    <th>passThreshold</th>
									</tr>
								</thead>
							</table>
							</div>
						</div>

						<div class="dynDiv" id="settingsWrapper" style="display: none; z-index: 2; right: 5%; top: 10%; width: 800px; height: 600px;">
							<div class="dynDiv_moveParentDiv" style="cursor: move;">
								<span class="dynTitle">Settings</span>
								<i class="fa fa-times-circle closeButton settingsCancel" id="closeSettings"></i>
							</div>
							<div class="settings_menu">
								<button class="btn btn-1a" id="settings-data">Data</button><button class="btn btn-1a" id="settings-appearance">Appearance</button>
							</div>
							<div class="dynDiv_resizeDiv_tl" style="cursor: nw-resize;"></div>
							<div class="dynDiv_resizeDiv_tr" style="cursor: ne-resize;"></div>
							<div class="dynDiv_resizeDiv_bl" style="cursor: sw-resize;"></div>
							<div class="dynDiv_resizeDiv_br" style="cursor: se-resize;"></div>
							<div id="settings_main">
								<div id="settingsData">
									<form id="settingsForm" method="post">
										<section style="margin-bottom:2%;">
										<div style="margin-bottom:30px;width:30%;min-width:300px;display:inline;min-width:300px;margin-right:2%;float:left;">
											<input style="width:100%;margin-bottom:10px" class="form-control" id="settingsPeptide" autocomplete="off" required="" type="text" placeholder="Peptide Sequence1[;Peptide Sequence2]" name="peps" autofocus="">
											<textarea class="form-control" style="padding-bottom:0px;" id="settingsPeaklist" required="" type="text" placeholder="Peak List [m/z intensity]" name="peaklist"></textarea>
										</div>
										<div style="width:68%;display:inline;">
											<label for="settingsCL"><span class="label btn">Cross-linker mod mass: </span>
												<input class="form-control" style="margin-right:2%;width:25%" required="" id="settingsCL" placeholder="CL mod mass" name="clModMass" autocomplete="off">
											</label>

											<label for="settingsPrecursorZ"><span class="label btn">Precursor charge: </span>
								  				<input class="form-control" style="margin-right:2%;width:10%" required="" id="settingsPrecursorZ" type="number" min="1" placeholder="Charge" name="preCharge" autocomplete="off">
											</label>

											<label for="settingsIons"><span class="label btn">Ions: </span>
												<div class="dropdown">
													<input type="text" class="form-control btn-drop" id="ionSelection" readonly>
													<div class="dropdown-content mutliSelect">
														<ul>
											                <li>
											                    <label><input type="checkbox" class="ionSelectChkbox" value="peptide" id="PeptideIon" name="ions[]"/>Peptide ion</label></li>
											                <li>
											                    <label><input type="checkbox" class="ionSelectChkbox" value="a" id="AIon" name="ions[]"/>A ion</label></li>
											                <li>
											                    <label><input type="checkbox" class="ionSelectChkbox" value="b" id="BIon" name="ions[]"/>B ion</label></li>
											                <li>
											                    <label><input type="checkbox" class="ionSelectChkbox" value="c" id="CIon" name="ions[]"/>C ion</label></li>
											                <li>
											                    <label><input type="checkbox" class="ionSelectChkbox" value="x" id="XIon" name="ions[]"/>X ion</label></li>
											                <li>
											                    <label><input type="checkbox" class="ionSelectChkbox" value="y" id="YIon" name="ions[]"/>Y ion</label></li>
											                <li>
											                    <label><input type="checkbox" class="ionSelectChkbox" value="z" id="ZIon" name="ions[]"/>Z ion</label></li>
														</ul>
													</div>
												</div>
											</label>
	<!-- 
											<label for="settingsFragmentation"><span class="label btn">Fragmentation method: </span>
												<select class="form-control" style="margin-right:2%;width:15%;display:inline;" id="settingsFragmentation" name="fragMethod">
													<option value="HCD">HCD</option>
													<option value="CID">CID</option>
													<option value="ETD">ETD</option>
													<option value="ETciD">ETciD</option>
													<option value="EThcD">EThcD</option>
												</select>
											</label> -->

											<label for="settingsTolerance"><span class="label btn">MS2 tolerance: </span>
												<input class="form-control" style="margin-right:2%;width:15%;display:inline;" required="" id="settingsTolerance" type="number" min="0" step="0.1" placeholder="Tolerance" name="ms2Tol" autocomplete="off">
												<select class="form-control" style="margin-right:2%;width:15%;display:inline;" required="" id="settingsToleranceUnit" name="tolUnit">
													<option value="ppm">ppm</option> 
													<option value="Da">Da</option>
												</select>									
											</label>
										</div>
										</section>
										<section style="margin-bottom:2%;">
											<div class="form-control" style="height:auto" id="myMods">
											<div id="modificationTable_wrapper" class="dataTables_wrapper no-footer"><div id="modificationTable_processing" class="dataTables_processing" style="display: none;">Processing...</div><table id="modificationTable" class="display dataTable no-footer" width="100%" style="text-align: center; width: 100%;" role="grid">
												<thead>
													<tr role="row"><th class="sorting_disabled invisible" rowspan="1" colspan="1" style="width: 0px;">Mod-Input</th><th class="sorting_disabled" rowspan="1" colspan="1" style="width: 206px;">Modification</th><th class="sorting_disabled" rowspan="1" colspan="1" style="width: 144px;">Mass <div class="tooltip"><a href="#" id="resetModMasses"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAFaUlEQVRoQ+1YaWwUZRh+3plp60pJC4GIZUEgsXigRgvqD0BqW2oLpYWkaLiCoYjUNlgx9fphDcSDCBVbSitVCRCRJYQA5ehFEfihUjRqUFoTqhzVKpRyuWW7O6+Z0iHDdnZnZncIEJhkszPf937P8b3fNUO4xS+6xfXjjoEbncE7Gbg9MjBzixNeKsWmaVl2G76+QyjbJQqSmA+mJQCi5a+n2c5nO6DawxEztibIjAoACWqZb+NU2/lsB0S2K1qSopaAOB+AqB0y3q+ybOezFTBy1tYsMJUAcOqNdY/HE4vN08/ZOQ9sMeCYucUpQyoFIdNAnJuBzQK4snND1gE7jIRnINslRkVF5RFhqTJJLQpqInCFu9PzKTZP91lsezU8LAN3z95eAcJLoZL3tKv6j6NewPrUS6HghGUA2S6xj8ORx4SlZD0DGr3ciAiefKkys82qifAM9LA5cnY5Ra9cCrDRHAh2emwBkH5hbcZRKyZsMaAS9p27IwtEJRRgFTIURmg+Lw0ahc9GdxnG9gTYakDBHJjrir7cGb2E0HsfAKga4NRg4ghY3PHFpBU3zIBKHJNTlSAyVbBmJz77+STql7M7HSyvAjAsgMhzXRFS/MXy1H/MmLA9A9eQZrvE/jHRyo7cfRZqr0zv5oudt/M+gUjZB4boi6TK9sq0+TfeQI+C/jm7nERUemZN2tXT6ID5O+MZdDjA/iFD9A09U55xysjE9c2AAfvABbvfB+MtvTBmIeP0mtSqm9qAc96e/h4Jf+plgcFv/1uR9sFNbUARd8+CPXtBSNQRurGt/LkZthiIW1gzhYE0EF8dcsTU1il5l7eXpJ83IglWf+/C6jIAC3Vijvy1OnWUEbapORCXW90BIKYXGFNe6+qJypIY8jU4t/odRvdh0P/qbC1LdRgBmzIw+JWakwAG9zaADafKJs42IglW78ytWQ/CLJ2YUydXTdR9r9DGmjIwJL/2ezDG6JD8dqI05aFwDAzJq/0VwIO9MAiHTpSkPGmEbcrA0PzaTQCm64DJDocjtmnZ2AtGRHr1IwsP9nW73crwFHTqXcdLUp43wjVlYFh+/Vwm/lIPjIk/Ob4ypcCISK9+2KLaYjC9qtuW6cU/SpLWGuGaMhC/uGFAl9f3t/9Leg84AzShZWXSfiMybf3wRfXjAd4H6J6wfRGSOKh5eeJpI0xTBhSQEQX1O8CYrAtI1CJBTm4uTj5mRKjUxxfUjfBCqAPzcH08VB0rTsowg2XawPBFdY+KgvBjgPGqcF0i8Ou/FyeVByO+v6D+ZQZ9DKBPgDjZJ8uPt6xM/tlWA1d6bu86Jhgtm/uJaTsL8qFIt/CD0s7jkJ8gWRjDxFMAjA8mjBjrm4ufnWNGvBJjOgNK8CNvHuh3ucv3LcDx5ghIvhLHeqvMtRDdSqg5ShKf/uXDcWfN4Vs0oIA+8FpdPAvidwTEmiUJMG+uKWagg3ziU0dXjGu2gmspAyrwyMKG0QLTNgBxVsgCTFiluFUGZzYtS2y0iheSAYVkZOHBOAG+bQSMtkJKvRkbvSxmNi0b22oFR40N2YACMKGoQTrtpvkAvaucjAMK0GdpA/N7Axy8Zl9RojcU8ZYncSCSh4saosVOaQ6DpwJ4BoSIALHK55JviGmr7y7vuiNFiRdDFW5LBvTIE96ojfFIkY8RUxwRdc8RZm5l4tZIr+enwx+l3Hxfp8PtxXDahzUHwiG2q+1tbUAxr/60C4KZTuGeDCj/evemE2SGTBWpHAe0ov2fAxnSivEXqxpQ/5Wjh7ZM+xxkHwzs11+8VrR6r1emNaPc+wtVnrXi1Hv/Mm15SAb8G6kZ0+ttf9Ha4WXU8yEPpf8BwRqnQJnMMukAAAAASUVORK5CYII=" width="16" height="16" alt="revert"></a>
													<span class="tooltiptext">Reset to default</span>
																 </div></th><th class="sorting_disabled" rowspan="1" colspan="1" style="width: 175px;">Specificity</th></tr>
												</thead>
											<tbody><tr class="odd"><td valign="top" colspan="3" class="dataTables_empty">No matching records found</td></tr></tbody></table></div>
											</div>
										</section>	
										<div style="margin-top:30px; text-align: center">
											<input class="btn btn-1 btn-1a network-control" type="submit" value="Apply" id="settingsApply">
											<input class="btn btn-1 btn-1a network-control settingsCancel" type="button" value="Cancel" id="settingsCancel">
										</div>
									</form>
								</div>
								<div id="settingsAppearance" style="display:none">
									<label class="btn label">Colour scheme:
									<select id="colorSelector">
										<option value="RdBu">Red &amp; Blue</option>
										<option value="BrBG">Brown &amp; Teal</option>
										<option value="PiYG">Pink &amp; Green</option>
										<option value="PRGn">Purple &amp; Green</option>
										<option value="PuOr">Orange &amp; Purple</option>
									</select>
									</label>
									<label class="btn label">Highlight Color:
										<input class="jscolor form-control" id="highlightColor" value="#FFFF00" onchange="updateJScolor(this.jscolor);">
									</label>								
								</div>
							</div>
						</div>


					</div>
				<!--</div>top div -->
<!-- 				<div class="gutter gutter-vertical" style="height: 10px;"></div> -->
<!-- 				<div id="bottomDiv" style="height: calc(40% - 5px);">
					<div id="specList_main" style="color: #000; padding: 10px">
						<table id="specListTable" class="display" width="100%" style="text-align:center;">
							<thead>
								<tr>
								    <th>internal_id</th>
								    <th>id</th>
								    <th>peptide 1</th>
								    <th>peptide 2</th>
								    <th style="min-width: 50px">CL pos 1</th>
								    <th style="min-width: 50px">CL pos 2</th>
								    <th>passThreshold</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
            </div> -->

<!-- 			<div class="controls">
				<span id="filterPlaceholder"></span>
			</div> -->
        </div><!-- MAIN -->

    </body>
</html>
