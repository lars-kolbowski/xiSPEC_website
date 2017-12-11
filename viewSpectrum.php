<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (empty($_POST)){
	if (session_status() === PHP_SESSION_NONE){session_start();}
	$dbView = true;
	$justSaved = 'false';
	if(isset($_GET['sid']) || isset($_GET['db'])){
		$tmpDB = false;

		$xiSPEC_ms_parser_dir = '../xiSPEC_ms_parser/';
		$dir = 'sqlite:'.$xiSPEC_ms_parser_dir.'dbs/xiSPEC.db';
		$xiSPECdb = new PDO($dir) or die("cannot open the database");
		$xiSPECdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		//share db link
		if(!empty($_GET['sid'])){
			$stmt = $xiSPECdb->prepare("SELECT name FROM databases WHERE share = :share;");
			$stmt->bindParam(':share', $_GET['sid'], PDO::PARAM_STR);
			$stmt->execute();
			$dbName = $stmt->fetchColumn();

			if(!$dbName)
				header('Location: index.php');

			if(!isset($_SESSION['access'])) $_SESSION['access'] = array();
			if(!in_array($dbName, $_SESSION['access'])){
				$_SESSION['access'][] = $dbName;
			}

			$shareLink = "http://" . $_SERVER['SERVER_NAME'] . "/xiSPEC/viewSpectrum.php?sid=" . $_GET['sid'];
		}

		//normal db link
		else if(!empty($_GET['db'])){

			if(isset($_SESSION[$_GET['db']])){
				unset($_SESSION[$_GET['db']]);
				$justSaved = 'true';
			}

			$stmt = $xiSPECdb->prepare("SELECT share, pass FROM databases WHERE name = :name;");
			$stmt->bindParam(':name', $_GET['db'], PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetch();

			if (!$result) {
				header("Location: index.php");
				exit();
			}

			//public check
			if ($result['pass'] === 'public'){
				if(!isset($_SESSION['access'])) $_SESSION['access'] = array();
				if(!in_array($_GET['db'], $_SESSION['access'])){
					$_SESSION['access'][] = $_GET['db'];
				}
				$public = true;
			}

			if($result['share'] != null)
				$shareLink = (isset($_SERVER['HTTPS']) ? "https" : "http") . $_SERVER['SERVER_NAME'] . "/xiSPEC/viewSpectrum.php?sid=" . $result['share'];
			else
				$shareLink = false;

			$dbName = $_GET['db'];
		}
		//check Authentication
		if(!in_array($dbName, $_SESSION['access'])){
			header('Location: auth.php?db='.$_GET['db']);
		}
		//log access
		require("php/logAccess.php");
	}
	elseif(isset($_SESSION['tmpDB'])){
		$dbName = $_SESSION['tmpDB'];
		$tmpDB = true;
		if(!isset($_SESSION['access'])) $_SESSION['access'] = array();
		if(!in_array($dbName, $_SESSION['access'])){
			$_SESSION['access'][] = $dbName;
		}
	}
	else{
		header('Location: index.php');
	}

}
else{
	$dbView = FALSE;
	require('php/processSpecPostData.php');
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>xiSPEC<?php if(isset($dbName) && !$tmpDB) echo " - ".$dbName; ?></title>
			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
			<meta name="description" content="mass spectrometry data analysis and visualization tool" />
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

		var model_vars = {
			baseDir: "",
			xiAnnotatorBaseURL: "http://xi3.bio.ed.ac.uk/xiAnnotator/",
			<?php if(isset($dbName)) echo 'database: "'.$dbName.'"'; ?>
		};

		SpectrumModel = new AnnotatedSpectrumModel(model_vars);
		SettingsSpectrumModel = new AnnotatedSpectrumModel(model_vars);

		SpectrumModel.otherModel = SettingsSpectrumModel;
		SettingsSpectrumModel.otherModel = SpectrumModel;

		<?php
			//ToDo: php vars to js - find nicer way to handle this
			if($dbView){
				echo 'window.dbView = true;';
				echo 'window.justSaved = '.$justSaved.';';
			}
			else{
				echo 'window.dbView = false;';
				echo 'var json_data = '.$response.';';
				echo 'var json_req = '.$postJSON.';';
			}
		?>
		$(function() {

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
							<label class="btn" title="toggle moveable labels on/off">Move Labels<input class="pointer" id="moveLabels" type="checkbox"></label>
							<button id="clearHighlights" class="btn btn-1 btn-1a">Clear Highlights</button>
							<label class="btn" title="toggle measure mode on/off">Measure<input class="pointer" id="measuringTool" type="checkbox"></label>
							<form id="setrange">
								<label class="btn" title="m/z range" style="cursor: default;">m/z:</label>
								<label class="btn" for="lockZoom" title="Lock current zoom level" id="lock" class="btn"><input id="lockZoom" type="checkbox" style="display: none;">🔓</label>
								<input type="text" id="xleft" size="5" title="m/z range from:">
								<span>-</span>
								<input type="text" id="xright" size="5" title="m/z range to:">
								<input type="submit" id="rangeSubmit" value="Set" class="btn btn-1 btn-1a" style="display: none;">
								<span id="range-error"></span>
								<button id="reset" title="Reset to initial zoom level" class="btn btn-1 btn-1a">Reset Zoom</button>

							</form>
								<!-- <button id="toggleView" title="Toggle between quality control/spectrum view" class="btn btn-1 btn-1a">error/int</button> -->
							<i id="toggleSettings" title="Show/Hide Settings" class="btn btn-1a btn-topNav fa fa-cog" aria-hidden="true"></i>
							<span id="dbControls">
								<?php
								if($dbView){
									if($tmpDB) echo '<i id="saveDB" title="Save" class="btn btn-1a btn-topNav fa fa-floppy-o" aria-hidden="true"></i>';
									else echo '<i id="shareDB" title="Share" class="btn btn-1a btn-topNav fa fa-share-alt" aria-hidden="true"></i>';
								}
								?>
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

		<!-- Save Modal -->
		<div id="saveModal" role="dialog" class="modal">
			<div class="header" style="background: #750000; color:#fff;">Save your dataset</div>
			<div class="content" id="saveModal_content">
				<div id="saveDBerror"></div>
				<form id='saveDB_form'>
					<label class="flex-row label">
						Name: <div class="flex-grow"><input class="form-control" required length=30 id="saveDbName" name="dbName" type="text" placeholder="Enter a name for your dataset"></div>
					</label>
					<label class="flex-row label" style="line-height: 1.5em; margin: 1.5em 0em;">
						Public: <input id="publicDBchkBox" class="pointer" name="public" type="checkbox"> <span style="text-transform: initial; letter-spacing: initial; color: #ccc;">(checking this will allow anyone who knows the name of your dataset to view it.)</span>
					</label>
					<label class="flex-row label" id="dbPassLabel">
						Password: <div class="flex-grow"><input class="form-control" required length=30 id="saveDbPass" name="dbPass" type="password" placeholder="Enter password"></div>
						<div class="flex-grow"><input class="form-control" required length=30 id="saveDbPassControl" name="dbPass" type="password" placeholder="Retype password"></div>
					</label>

					<input type="submit" id="saveDataSet" class="btn btn-1 btn-1a" style="font-size: 1em;" value="save">
				</form>
			</div>
		</div>
		<!-- End Save Modal -->
		<!-- Share Modal -->
		<div id="shareModal" role="dialog" class="modal">
			<div class="header" style="background: #750000; color:#fff;">Share your dataset: <span id='dbName'><?php if (isset($_GET['db'])) echo $_GET['db']; ?></span></div>
			<div class="content" id="shareModal_content">
				<div id='justSavedMsg' style="line-height: 2em;"></div>
				<?php

					$link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://". $_SERVER['SERVER_NAME'] . "/xiSPEC/viewSpectrum.php?db=" . $dbName;
					if (isset($public)){
						echo 'Your dataset is public - you can go ahead and share the link below</br><label class="flex-row label">url: <div class="flex-grow"><input type="text" class="form-control" value="'.$link.'" readonly onClick="this.select();"></div></label>';
					}
					else {
						echo 'Your dataset is private - you can either share the password protected link:</br><label class="flex-row label">url (password protected): <div class="flex-grow"><input type="text" class="form-control" value="'.$link.'" readonly onClick="this.select();"></div></label></br>';
						if(!$shareLink){
							echo '<span id="shareLinkSpan">or <a id="createShareLink" class="pointer">generate a share link</a> - </span><strong>Anyone</strong> with the link will be able view this dataset!';
							echo '<label class="flex-row label" id="shareLinkLabel" style="display: none;">url: <div class="flex-grow"><input type="text" id="shareLink" class="form-control" value="" readonly onClick="this.select();"></div></label>';
						}
						else{
							echo 'or share the link below - <strong>Anyone</strong> with the link will be able view this dataset!';
							echo '<label class="flex-row label" id="shareLinkLabel">url: <div class="flex-grow"><input type="text" id="shareLink" class="form-control" value="'.$shareLink.'" readonly onClick="this.select();"></div></label>';
						}
					}
				 ?>
						</div>
		<!-- End Share Modal -->
	</body>
</html>
