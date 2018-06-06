<?php
$cacheBuster = '?v='.microtime(true);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$justSaved = 'false';
$sid = false;
if (empty($_POST)){
	if (session_status() === PHP_SESSION_NONE){session_start();}
	$dbView = true;
	$sid = (isset($_GET['sid']) ? $_GET['sid'] : false);

	if(isset($_GET['s']) || isset($_GET['db'])){
		$tmpDB = false;
		#this includes a connection string to the sql database
		require('xiSPEC_sql_conn.php');
		require("./php/checkAuth.php");
		//log access
		require("./php/logAccess.php");

		if(isset($_SESSION[$_GET['db']])){
			unset($_SESSION[$_GET['db']]);
			$justSaved = 'true';
		}

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
		header('Location: /index.php');
	}

}
else{
	$dbView = FALSE;
	require("./php/processSpecPostData.php");
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>xiSPEC<?php if(isset($dbName) && !$tmpDB) echo " | ".$dbName; ?></title>
			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
			<meta name="description" content="mass spectrometry data analysis and visualization tool" />
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="apple-mobile-web-app-capable" content="yes">
			<meta name="apple-mobile-web-app-status-bar-style" content="black">
			<link rel="icon" type="image/ico" href="/images/logos/favicon.ico">
			<link rel="stylesheet" href="./css/style.css" />
			<link rel="stylesheet" href="./css/spectrum.css" />
			<link rel="stylesheet" href="./css/settings.css" />
			<link rel="stylesheet" href="./css/tooltip.css">
			<link rel="stylesheet" href="./css/xiSPEC_tooltip.css">
			<link rel="stylesheet" href="./css/spectrumViewWrapper.css">
			<link rel="stylesheet" href="./css/QC.css">
			<link rel="stylesheet" href="./css/dropdown.css">
			<link rel="stylesheet" type="text/css" href="./vendor/bootstrap/css/bootstrap.min.css"/>
			<link rel="stylesheet" type="text/css" href="./css/font-awesome.min.css"/>
			<?php include("./xiSPEC_scripts.php");?>

			<script type="text/javascript" src="./vendor/jscolor.min.js"></script>
			<script type="text/javascript" src="./vendor/split.min.js"></script>
			<script type="text/javascript" src="./vendor/svgexp.js"></script>
			<script type="text/javascript" src="./vendor/spin.js"></script>
			<script type="text/javascript" src="./vendor/byrei-dyndiv_1.0rc1.js"></script>
			<script type="text/javascript" src="./vendor/download.js"></script>
			<script type="text/javascript" src="./vendor/bootstrap/js/bootstrap.min.js"></script>
			<script type="text/javascript" src="./vendor/dataTables.bootstrap.min.js"></script>
			<!-- <script type="text/javascript" src="cdn.datatables.net/plug-ins/1.10.16/api/fnFindCellRowIndexes.js"></script> -->


			<!-- Spectrum view .js files -->
			<script type="text/javascript" src="./js/app.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./src/model.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./src/SpectrumView2.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./src/FragmentationKeyView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./src/PrecursorInfoView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./src/SpectrumSettingsView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./js/PeptideView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./src/PepInputView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./src/QCwrapperView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./src/ErrorPlotView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./src/FragKey/KeyFragment.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./src/graph/Graph.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./src/graph/Peak.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./src/graph/Fragment.js<?php echo $cacheBuster ?>"></script>
<?php if($dbView)
echo 	'<script type="text/javascript" src="./src/TableWrapperView.js'.$cacheBuster.'"></script>
		<script type="text/javascript" src="./src/DataTableView.js'.$cacheBuster.'"></script>
		<script type="text/javascript" src="./js/specListTable.js'.$cacheBuster.'"></script>
		<script type="text/javascript" src="./js/altListTable.js'.$cacheBuster.'"></script>';
?>
			<script>

		var model_vars = {
			baseDir: "./",
			xiAnnotatorBaseURL: "http://xi3.bio.ed.ac.uk/xiAnnotator/",
			<?php if(isset($dbName)) echo 'database: "'.$dbName.'",'; ?>
			<?php if(isset($tmpDB)) echo 'tmpDB: "'.$tmpDB.'",'; ?>
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
		window.FragmentationKey = new FragmentationKeyView({model: SpectrumModel, el:"#spectrumMainPlotDiv"});
		window.InfoView = new PrecursorInfoView ({model: SpectrumModel, el:"#spectrumPanel"});
		window.QCwrapper = new QCwrapperView({el: '#QCdiv'});
		window.ErrorIntensityPlot = new ErrorPlotView({
			model: SpectrumModel,
			el:"#subViewContent-left",
			xData: 'Intensity',
			margin: {top: 10, right: 30, bottom: 20, left: 65},
			svg: "#errIntSVG",
		});
		window.ErrorMzPlot = new ErrorPlotView({
			model: SpectrumModel,
			el:"#subViewContent-right",
			xData: 'm/z',
			margin: {top: 10, right: 30, bottom: 20, left: 65},
			svg: "#errMzSVG",
		});
		CLMSUI.vent.trigger('show:QC', true);

		window.SettingsView = new SpectrumSettingsView({
			model: SettingsSpectrumModel,
			el:"#settingsWrapper",
			showCustomCfg: false,
		});

		if(!dbView){
			SpectrumModel.set({JSONdata: json_data, JSONrequest: json_req});
			var json_data_copy = jQuery.extend({}, json_data);
			//SpectrumModel.otherModel = SettingsSpectrumModel;
			SettingsSpectrumModel.set({JSONdata: json_data_copy, JSONrequest: json_req});
			//SettingsSpectrumModel.otherModel = SpectrumModel;
			//window.SettingsView.render();
		}
		else {
			window.TableWrapper = new TableWrapperView({
				model: SpectrumModel,
				el:"#bottomDiv",
				initId: "<?php echo $sid; ?>"
			});
			// window.specListTable = new specListTableView({model: SpectrumModel, el:"#specListWrapper"});
			// window.altListTable = new altListTableView({model: SpectrumModel, el:"#altListWrapper"});
		}

});

		</script>
		</head>

		<body>
			<!-- Main -->
			<div id="mainView">
				<div class="mainContent">
						<div class="overlay" id="topDiv-overlay"></div>
						<div id="spectrumPanel">

							<div class="dynDiv" id="settingsWrapper">
								<div class="dynDiv_moveParentDiv">
									<span class="dynTitle">Settings</span>
									<i class="fa fa-times-circle settingsCancel" id="closeSettings"></i>
								</div>
								<div class="dynDiv_resizeDiv_tl draggableCorner"></div>
								<div class="dynDiv_resizeDiv_tr draggableCorner"></div>
								<div class="dynDiv_resizeDiv_bl draggableCorner"></div>
								<div class="dynDiv_resizeDiv_br draggableCorner"></div>
							</div>
						<div id="spectrumControls">
							<i class="btn btn-1a btn-topNav fa fa-home fa-xi" style='top: 0px;' onclick="window.location = './index.php';" title="Home"></i>
							<i class="btn btn-1a btn-topNav fa fa-github fa-xi" onclick="window.open('https://github.com/Rappsilber-Laboratory/xiSPEC/issues', '_blank');" title="GitHub issue tracker" style="cursor:pointer;"></i>
							<i class="btn btn-1a btn-topNav fa fa-download" aria-hidden="true" id="downloadSVG" title="download SVG" style="cursor: pointer;"></i>
							<label class="btn" title="toggle moveable labels on/off">Move Labels<input class="pointer" id="moveLabels" type="checkbox"></label>
							<button id="clearHighlights" class="btn btn-1 btn-1a">Clear Highlights</button>
							<label class="btn" title="toggle measure mode on/off">Measure<input class="pointer" id="measuringTool" type="checkbox"></label>
							<form id="setrange">
								<label class="btn" title="m/z range" style="cursor: default;">m/z:</label>
								<label class="btn" for="lockZoom" title="Lock current zoom level" id="lock" class="btn">🔓</label><input id="lockZoom" type="checkbox" style="display: none;">
								<input type="text" id="xleft" size="5" title="m/z range from:">
								<span>-</span>
								<input type="text" id="xright" size="5" title="m/z range to:">
								<input type="submit" id="rangeSubmit" value="Set" class="btn btn-1 btn-1a" style="display: none;">
								<span id="range-error"></span>
								<button id="reset" title="Reset to initial zoom level" class="btn btn-1 btn-1a">Reset Zoom</button>

							</form>
								<?php
								echo '<i id="toggleSettings" title="Show/Hide Settings" class="btn btn-1a btn-topNav fa fa-cog" aria-hidden="true"></i>';
								if($dbView){
									echo '<span id="dbControls">';
									if($tmpDB) echo '<div class="xispec_tooltip_wrapper"><span class="xispec_tooltip_tr" id="saveTooltip">Your dataset is temporary click here if you want to save it for later access!<i class="fa fa-times-circle closeButton"></i></span><i id="saveDB" title="Save" class="btn btn-1a btn-topNav fa fa-floppy-o" aria-hidden="true"></i></div>';
									else echo '<i id="shareDB" title="Share" class="btn btn-1a btn-topNav fa fa-share-alt" aria-hidden="true"></i>';
									// <i id="prevSpectrum" title="Previous Spectrum" class="btn btn-1a btn-topNav fa fa-arrow-left" aria-hidden="true"></i> -->
									echo '<i id="toggleSpecList" title="Show/Hide Spectra list" class="btn btn-1a btn-topNav fa fa-bars" aria-hidden="true"></i>';
									//  <i id="nextSpectrum" title="Next Spectrum" class="btn btn-1a btn-topNav fa fa-arrow-right" aria-hidden="true"></i> -->
									echo '</span>';
									echo '<i id="revertAnnotation" title="revert to original annotation" class="btn btn-topNav fa fa-undo disabled"  aria-hidden="true"></i>';
								}
								?>
							<a href="/help.php" target="_blank"><i title="Help" class="btn btn-1a btn-topNav fa fa-question" aria-hidden="true"></i></a>

						</div>
						<div class="plotsDiv">
							<div id="spectrumMainPlotDiv">
								<svg id="spectrumSVG"></svg>
								<div id="measureTooltip"></div>
							</div>
							<div id="QCdiv">
								<div class="subViewHeader"></div>
								<div class="subViewContent">
									<div class="subViewContent-plot" id="subViewContent-left">
										<!-- <i class="fa fa-times closeButton" aria-hidden="true"></i> -->
										<svg id="errIntSVG" class="errSVG"></svg>
									</div>
									<div class="subViewContent-plot" id="subViewContent-right">
										<!-- <i class="fa fa-times closeButton" aria-hidden="true"></i> -->
										<svg id="errMzSVG" class="errSVG"></svg>
									</div>
								</div>
							</div>
						</div>
					</div>
				<div id="bottomDiv" class="tableDiv"></div>
			</div>
		</div><!-- MAIN -->

		<!-- Save Modal -->
		<div id="saveModal" role="dialog" class="modal">
			<div class="header" style="background: #750000; color:#fff;">Save dataset</div>
			<div class="content" id="saveModal_content">
				<div id="saveDBerror"></div>
				<form id='saveDB_form'>
					Fill out the form to save your data for later access. <br />
					<?php //if($db_size > 500000000){	}; ?>
					Your data will be accessible for 3 months.<br />
					If you provide an email address we will contact you before deletion with the possiblity to extend the time.<br />
					<label class="flex-row label">
						Name: <div class="flex-grow"><input class="form-control" required length=30 id="saveDbName" autocomplete="off" name="dbName" type="text" placeholder="Enter a name for the dataset"></div>
					</label>
					<label class="flex-row label">
						Email: <div class="flex-grow"><input class="form-control" length=128 id="saveDbEmail" name="dbEmail" type="text" placeholder="Enter your email address (optional)"></div>
					</label>

					<label class="flex-row label" style="line-height: 1.5em; margin: 1.5em 0em;">
						Public: <input id="publicDBchkBox" class="pointer" name="public" type="checkbox"> <span style="text-transform: initial; letter-spacing: initial; color: #ccc;"></span>
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
			<div class="header" style="background: #750000; color:#fff;">Share dataset: <span id='dbName'><?php if (isset($_GET['db'])) echo $_GET['db']; ?></span></div>
			<div class="content" id="shareModal_content">
				<div id='justSavedMsg' style="line-height: 2em;"></div>
				<?php

					$link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://". $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
					if (isset($public)){
						echo 'This dataset is public - you can go ahead and share the link below</br><label class="flex-row label">url: <div class="flex-grow"><input type="text" class="form-control shareURL" value="'.$link.'" readonly onClick="this.select();"></div></label>';
					}
					else {
						echo 'This dataset is private - you can either share the password protected link:</br><label class="flex-row label">url (password protected): <div class="flex-grow"><input type="text" class="form-control shareURL" value="'.$link.'" readonly onClick="this.select();"></div></label></br>';
						if(!$shareLink){
							echo '<span id="shareLinkSpan">or <a id="createShareLink" class="pointer">generate a share link</a> - </span><strong>Anyone</strong> with the link will be able view this dataset!';
							echo '<label class="flex-row label" id="shareLinkLabel" style="display: none;">url: <div class="flex-grow"><input type="text" id="shareLink" class="form-control shareURL" value="" readonly onClick="this.select();"></div></label>';
						}
						else{
							echo 'or share the link below - <strong>Anyone</strong> with the link will be able view this dataset!';
							echo '<label class="flex-row label" id="shareLinkLabel">url: <div class="flex-grow"><input type="text" id="shareLink" class="form-control shareURL" value="'.$shareLink.'" readonly onClick="this.select();"></div></label>';
						}
					}
				 ?>
				 <div style="display:none;">
				 <label><input type="checkbox" id="shareInclSid"/>Include currently selected spectrum in link</label>
				 </div>
						</div>
		<!-- End Share Modal -->
	</body>
</html>
