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
		// this includes a connection string to the sql database
		require('xiSPEC_sql_conn.php');
		require("./php/checkAuth.php");
		// log access
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
			<link rel="stylesheet" href="./css/xiSPEC_tooltip.css">
			<link rel="stylesheet" href="./css/spectrumViewWrapper.css">
			<link rel="stylesheet" href="./css/dropdown.css">
			<link rel="stylesheet" type="text/css" href="./vendor/bootstrap/css/bootstrap.min.css"/>
			<link rel="stylesheet" type="text/css" href="./css/font-awesome.min.css"/>
			<?php include("./xiSPEC_scripts.php");?>

			<!-- modular xiSPEC files -->
		    <script type="text/javascript" src="./spectrum/vendor/byrei-dyndiv_1.0rc1.js"></script>
		    <script type="text/javascript" src="./spectrum/vendor/jscolor.min.js"></script>
		    <script type="text/javascript" src="./spectrum/vendor/split.min.js"></script>
		    <script type="text/javascript" src="./spectrum/vendor/svgexp.js"></script>
		    <script type="text/javascript" src="./spectrum/vendor/download.js"></script>
		    <script type="text/javascript" src="./spectrum/vendor/bootstrap/js/bootstrap.min.js"></script>
		    <script type="text/javascript" src="./spectrum/vendor/dataTables.bootstrap.min.js"></script>

			<link rel="stylesheet" href="./spectrum/css/spectrum.css" />
			<link rel="stylesheet" href="./spectrum/css/settings.css" />
			<link rel="stylesheet" href="./spectrum/css/QC.css">
			<link rel="stylesheet" href="./spectrum/css/dropdown.css">
			<link rel="stylesheet" type="text/css" href="./spectrum/vendor/bootstrap/css/bootstrap.min.css"/>
			<link rel="stylesheet" type="text/css" href="./spectrum/css/font-awesome.min.css"/>

			<script type="text/javascript" src="./spectrum/src/Wrapper.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/SpectrumWrapper.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/AnnotatedSpectrumModel.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/SpectrumControlsView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/SpectrumView2.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/FragmentationKeyView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/PrecursorInfoView.js<?php echo $cacheBuster ?>"></script>
            <script type="text/javascript" src="./spectrum/src/SettingsView.js<?php echo $cacheBuster ?>"></script>
            <script type="text/javascript" src="./spectrum/src/AppearanceSettingsView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/DataSettingsView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/PepInputView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/QCwrapperView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/ErrorPlotView.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/FragKey/KeyFragment.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/graph/Graph.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/graph/Peak.js<?php echo $cacheBuster ?>"></script>
			<script type="text/javascript" src="./spectrum/src/graph/Fragment.js<?php echo $cacheBuster ?>"></script>
<?php if($dbView)
echo 	'<script type="text/javascript" src="./js/TableWrapperView.js'.$cacheBuster.'"></script>
		<script type="text/javascript" src="./js/DataTableView.js'.$cacheBuster.'"></script>

		<script type="text/javascript" src="./js/specListTable.js'.$cacheBuster.'"></script>
		<script type="text/javascript" src="./js/altListTable.js'.$cacheBuster.'"></script>';
?>
			<script type="text/javascript" src="./vendor/jquery.easyModal.js"></script>

		<script>

		<?php
			//ToDo: php vars to js - find nicer way to handle this
			if($dbView){
				if ($tmpDB) echo 'window.tmpDB = true;';
				else echo 'window.tmpDB = false;';
				echo 'window.dbView = true;';
				echo 'window.justSaved = '.$justSaved.';';
			}
			else{
				echo 'window.dbView = false;';
				echo 'var post_data = '.json_encode($_POST).';';
				echo 'var json_req = '.$postJSON.';';
			}
		?>
		$(function() {

			_.extend(window, Backbone.Events);
			window.onresize = function() { window.trigger('resize') };

			var xispec_options = {
				showCustomConfig: true,
				targetDiv: "spectrumPanel",
				xiAnnotatorBaseURL: "/xiAnnotator/",
				<?php if(isset($dbName)){
					echo 'database: "'.$dbName.'",';
					echo 'knownModificationsURL: "./php/getModifications.php?db='.$dbName.'&tmp='.$tmpDB.'",';
				}
				else{

					echo 'knownModifications: '.json_encode($modifications).',';
				}
				  ?>
				<?php if(isset($tmpDB)) echo 'tmpDB: "'.$tmpDB.'",'; ?>
			};

			window.xiSPEC = new xiSPEC_wrapper(xispec_options);

			// xispec_extra_spectrumControls
			$('#xispec_extra_spectrumControls_before').html('<a href="index.php"><i class="xispec_btn xispec_btn-1a xispec_btn-topNav fa fa-home fa-xi" style="top: 0px;" title="Home"></i></a><a href="https://github.com/Rappsilber-Laboratory/xiSPEC/issues" target="_blank"><i class="xispec_btn xispec_btn-1a xispec_btn-topNav fa fa-github fa-xi" title="GitHub issue tracker" style="cursor:pointer;"></i></a>');

			if (dbView){
				var db_controls = '<span id="dbControls">';
				if(tmpDB) db_controls += '<div class="xispec_tooltip_wrapper"><span class="xispec_tooltip_tr" id="saveTooltip">Your dataset is temporary click here if you want to save it for later access!<i class="fa fa-times-circle closeButton"></i></span><i id="saveDB" title="Save" class="xispec_btn xispec_btn-1a xispec_btn-topNav fa fa-floppy-o" aria-hidden="true"></i></div>';
				else db_controls += '<i id="shareDB" title="Share" class="xispec_btn xispec_btn-1a xispec_btn-topNav fa fa-share-alt" aria-hidden="true"></i>';
				db_controls += '<i id="xispec_toggleSpecList" title="Show/Hide Spectra list" class="xispec_btn xispec_btn-1a xispec_btn-topNav fa fa-bars" aria-hidden="true"></i>';
				// db_controls += '<i id="xispec_revertAnnotation" title="revert to original annotation" class="xispec_btn xispec_btn-topNav fa fa-undo disabled"  aria-hidden="true"></i>';
				db_controls += '</span>';
				$('#xispec_extra_spectrumControls_after').html(db_controls);

				$('#bottomDiv').show();
				// start the initSpinner
				xiSPECUI.initSpinner = new Spinner({scale: 5}).spin(
					d3.select("#xispec_spectrumMainPlotDiv").node());

				xiSPEC.TableWrapper = new TableWrapperView({
					model: xiSPEC.activeSpectrum.models['Spectrum'],
					el:"#bottomDiv",
					initId: "<?php echo $sid; ?>"		//ToDo: remove? -> not used yet
				});
			}
			else{
				$("#topDiv-overlay").css("z-index", -1);
				$('#dbControls').hide();
				$('#bottomDiv').hide();
				$('#altDiv').hide();
				xiSPECUI.vent.trigger('requestAnnotation', json_req, true);
			}

		});

		</script>
	</head>

	<body>
		<div id="mainView">
			<div class="mainContent">
				<div class="overlay" id="topDiv-overlay"></div>
				<div id="spectrumPanel"></div>
				<div id="bottomDiv" class="tableDiv"></div>
			</div>
		</div>

		<!-- Save Modal -->
		<div id="saveModal" role="dialog" class="modal">
			<div class="header" style="background: #750000; color:#fff;">Save dataset</div>
			<div class="content" id="saveModal_content">
				<div id="saveDBerror"></div>
				<form id='saveDB_form'>
					Fill out the form to save your data for later access. <br />
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
	<script type="text/javascript" src="./js/app.js<?php echo $cacheBuster ?>"></script>
</html>
