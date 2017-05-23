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


        <link rel="stylesheet" href="./css/style2.css" />
        <link rel="stylesheet" href="./css/tooltip.css">
        <link rel="stylesheet" href="./css/spectrumViewWrapper.css">
        <link rel="stylesheet" href="./css/validationPage.css">
        <script type="text/javascript" src="./vendor/d3.js"></script>
        <script type="text/javascript" src="./vendor/colorbrewer.js"></script>

        <script type="text/javascript" src="../CLMS-UI/vendor/c3.js"></script>
        <script type="text/javascript" src="../CLMS-UI/vendor/split.js"></script>
        <script type="text/javascript" src="../CLMS-UI/vendor/svgexp.js"></script>
        <script type="text/javascript" src="../CLMS-UI/vendor/underscore.js"></script>
        <script type="text/javascript" src="../CLMS-UI/vendor/zepto.js"></script>
        <script type="text/javascript" src="../CLMS-UI/vendor/backbone.js"></script>
        <script type="text/javascript" src="../CLMS-UI/vendor/spin.js"></script>


        <!-- Spectrum view .js files -->
        <script type="text/javascript" src="./src/model.js"></script>
        <script type="text/javascript" src="./src/SpectrumView2.js"></script>
        <script type="text/javascript" src="./src/FragmentationKeyView.js"></script>
        <script type="text/javascript" src="./src/PrecursorInfoView.js"></script>
        <script type="text/javascript" src="./src/ErrorIntensityPlotView.js"></script>     
        <script type="text/javascript" src="./src/FragKey/KeyFragment.js"></script>
        <script type="text/javascript" src="./src/graph/Graph.js"></script>
        <script type="text/javascript" src="./src/graph/Peak.js"></script>
        <script type="text/javascript" src="./src/graph/Fragment.js"></script>
        <script type="text/javascript" src="./vendor/js.cookie.js"></script>
        <script>


    SpectrumModel = new AnnotatedSpectrumModel();


    $(function() {

        // var spinner = new Spinner({scale: 5}).spin (d3.select("#mainContent").node());
            
        // spinner.stop();

        _.extend(window, Backbone.Events);
        window.onresize = function() { window.trigger('resize') };


        window.Spectrum = new SpectrumView({model: SpectrumModel, el:"#spectrumPanel"});
        window.FragmentationKey = new FragmentationKeyView({model: SpectrumModel, el:"#spectrumPanel"});
        window.ErrorIntensityPlot = new ErrorIntensityPlotView({model: SpectrumModel, el:"#spectrumPanel"});
        var json_data = <?php echo $response; ?>;
        var json_req = <?php echo $postJSON ?>;
        SpectrumModel.set({JSONdata: json_data, JSONrequest: json_req});

});
    </script>
    </head>

    <body>
        <!-- Main -->
        <div id="main">
			
            <div class="mainContent">
                <div id="spectrumPanel">
                	<div id="spectrumControls"><button class="downloadButton btn btn-1 btn-1a">Download Image As SVG</button><button id="clearHighlights" class="btn btn-1 btn-1a">Clear Highlights</button><label id="colorSelectorLabel" class="btn">Colour scheme:</label><select id="colorSelector"><option value="RdBu">Red &amp; Blue</option><option value="BrBG">Brown &amp; Teal</option><option value="PiYG">Pink &amp; Green</option><option value="PRGn">Purple &amp; Green</option><option value="PuOr">Orange &amp; Purple</option></select><label class="btn">Lossy Labels<input id="lossyChkBx" type="checkbox"></label><label class="movePeakLabels btn">Move Labels<input id="moveLabels" type="checkbox"></label><label class="btn">Measure<input id="measuringTool" type="checkbox"></label><form id="setrange"><label class="btn">m/z Range:</label><input type="text" id="xleft" size="7"><span>to</span><input type="text" id="xright" size="7"><label for="lockZoom" title="Lock current zoom level" id="lock" class="btn">🔓</label><input type="submit" id="rangeSubmit" value="Set" class="btn btn-1 btn-1a"><span id="range-error"></span><button id="reset" title="reset to initial zoom level" class="btn btn-1 btn-1a">All</button><input id="lockZoom" type="checkbox" style="visibility: hidden;"></form><button id="toggleView" title="Click to toggle view" class="btn btn-1 btn-1a">QC</button></div>
                    <div class="heightFill">
                        <svg id="spectrumSVG"></svg>
                    </div>
                </div>
            </div>

<!-- 			<div class="controls">
				<span id="filterPlaceholder"></span>
			</div> -->
        </div><!-- MAIN -->


        <script>
        //<![CDATA[
        
        //~ var windowLoaded = function () {
			// var CLMSUI = CLMSUI || {};
			// CLMSUI.loggedIn = true;			
   //          var spinner = new Spinner({scale: 5}).spin (d3.select("#topDiv").node());
				

   //          spinner.stop



/*            var success = function (text) {
                spinner.stop(); // stop spinner on request returning 
				var json = JSON.parse (text);	
				CLMSUI.init.modelsEssential(json);

				var searches = CLMSUI.compositeModelInst.get("clmsModel").get("searches");
				document.title = Array.from(searches.keys()).join();
					
				CLMSUI.split = Split (["#topDiv", "#bottomDiv"], { direction: "vertical",
						sizes: [60,40], minSize: [200,10],
							onDragEnd: function () {CLMSUI.vent.trigger ("resizeSpectrumSubViews", true);
				} });	
										
				CLMSUI.init.viewsEssential({"specWrapperDiv":"#topDiv"});

                //CLMSUI.vent.trigger ("spectrumShow", true);
                
				var allCrossLinks = Array.from(
					CLMSUI.compositeModelInst.get("clmsModel").get("crossLinks").values());
				CLMSUI.compositeModelInst.set("selection", allCrossLinks);					

				var resize = function(event) {
					CLMSUI.vent.trigger ("resizeSpectrumSubViews", true);
					var alts = d3.select("#alternatives");
					var w = alts.node().parentNode.parentNode.getBoundingClientRect().width - 20;
					alts.attr("style", "width:"+w+"px;"); //dont know why d3 style() aint working
				};

				window.onresize = resize;

				resize();
			};

			var url = "./loadData.php" + window.location.search;
			
         
            d3.text (url, function (error, text) {
                if (!error) {
                    success (text);
                }
            });*/
        
           
        //]]>
        </script>

    </body>
</html>
