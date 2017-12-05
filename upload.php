<!DOCTYPE HTML>
<html>
	<head>
		<?php
		error_reporting(E_ALL & ~E_NOTICE);
		$pageName = "Upload";
		include("head.php");
		?>
		<?php include("xiSPEC_scripts.php");?>
		<script type="text/javascript" src="./src/PepInputView.js"></script>
		<script type="text/javascript" src="./js/PeptideView.js"></script>
		<script type="text/javascript" src="./src/PrecursorInfoView.js"></script>
		<script type="text/javascript" src="./src/model.js"></script>
		<script type="text/javascript" src="./js/upload.js"></script>
		<script type="text/javascript" src="./vendor/spin.js"></script>
		<script src="vendor/jQueryFileUploadMin/jquery.ui.widget.js"></script>
		<script src="vendor/jQueryFileUploadMin/jquery.iframe-transport.js"></script>
		<script src="vendor/jQueryFileUploadMin/jquery.fileupload.js"></script>

		<link rel="stylesheet" href="./css/dropdown.css" />
	</head>
	<body>
		<!-- Sidebar -->
		<?php include("navigation.php");?>
			<!-- Main -->
			<div id="main">
			<!-- Intro -->
			<section id="top" class="one">
				<div class="container" id="jquery-fileupload">
					<h1 class="page-header accordionHead"> <span class="accordionSym">-</span> Data Upload - Upload your data as mzIdentML + mzML pair</h1>
					<div class="accordionContent" <?php echo ($_GET['v'] == "example" ? 'style="display: none;"' : '');?>>
						<div style="margin-left: 1em; font-size: 0.6em;"> Note: Filter out MS1 spectra to reduce upload/parsing time. (e.g. 'MS level 2-' for MSconvert)</div>
						<div style="display:flex; margin-top: 0.5em;">
							<input id="fileupload" type="file" name="files[]" accept=".mzid,.mzml,.mgf" multiple data-url="vendor/jQueryFileUploadMin/fileUpload.php">
							<label for="fileupload"><span class="uploadbox"></span><span class="btn">Choose file(s)</span></label>
							<div id="uploadProgress">
								<div class="file_upload_bar" style="width: 0%;"><div class="file_upload_percent"></div></div>
							</div>
							<button id="startParsing" disabled="true" class="btn btn-1a">Submit Data</button>
						</div>
						<div class="fileupload_info">
						<table>
							<tr id="mzid_fileBox">
								<td style="text-align: center;">Identification file:</td>
								<td>
									<span class="fileName">Select a mzIdentML file to upload</span>
									<span class="statusBox" data-filetype="mzid"></span>
									<input class="uploadCheckbox" type="checkbox" id="mzid_checkbox" style="visibility: hidden;">
								</td>
							</tr>
							<tr id="mzml_fileBox">
								<td style="text-align: center;">Peak list file:</td>
								<td>
									<span class="fileName">Select a mzML/mgf file to upload</span>
									<span class="statusBox" data-filetype="mzml"></span>
									<input class="uploadCheckbox" type="checkbox" id="mzml_checkbox" style="visibility: hidden;">
								</td>
							</tr>
						</table>
						</div>
					</div>
				</div>
			</section>
			<section id="bottom" class="one">
<!-- <span class="glyphicon glyphicon-upload"></span> -->
				<div class="container">
					<h1 class="page-header accordionHead"> <span class="accordionSym">+</span> Data Input - Manually input your spectrum data</h1>
					<div class="accordionContent" <?php echo ($_GET['v'] == "example" ? '' : 'style="display: none;"');?> >
						<form id="manUpPepForm" action="viewSpectrum.php" method="post" target="_blank">
						<!-- <form id="xisv_entryform"  action="http://spectrumviewer.org/xisv/index.php" method="post" target="_blank" onsubmit="doPreSubmission();"> -->
							<section style="margin-bottom:2%;">
							<div style="margin-bottom:30px;width:30%;min-width:300px;display:inline;min-width:300px;margin-right:2%;float:left;">
								<input style="width:100%;margin-bottom:10px" class="form-control" id="myPeptide" title="peptide sequence" required type="text" placeholder="Peptide Sequence1[;Peptide Sequence2]" name="peps" autofocus>
								<textarea class="form-control" style="padding-bottom:0px; line-height: 1.3em; height: 200px;" id="myPeaklist" title="peak list [m/z intensity]" required type="text" placeholder="Peak List [m/z intensity]" name="peaklist"></textarea>
							</div>
							<div style="width:68%;display:inline;">
								<div style="padding-bottom:15px;"> Peptide Preview:</div>
								<div style="height:200px; font-size:100%; overflow-y:hidden; position:relative; line-height:1.0em;" id="peptideDiv" class="form-control" ></div>
							</div>
							</section>
							<section style="clear:left;text-align:center;margin-bottom:2%;">
								<select class="form-control" style="margin-right:2%;width:22%;display:inline;cursor:pointer;" required id="myCL" title="cross-linker" name="clModMass">
									<option value="" disabled selected>Select cross-linker...</option>
									<option value="add">add your own...</option>
									<option value="0">none (linear peptide)</option>
									<option value="138.068080">BS3 [138.068080 Da]</option>
									<option value="82.041865">SDA [82.041865 Da]</option>
									<!-- <option value="158.0038">DSSO [158.0038 Da]</option> -->
								</select>

								<div style="margin-right:2%;width:10%;display:inline;">
									<span class="input-charge-plus">
											<input class="form-control"   required id="myPrecursorZ" title="charge state" placeholder="z" name="preCharge" autocomplete="off">
										</span>
									</div>

								<div class="mulitSelect_dropdown" style="margin-right:2%;">
									<input type="text" class="form-control btn-drop" id="ionSelection" title="fragment ion types" value="Select ions..." readonly>
									<div class="mulitSelect_dropdown-content mutliSelect">
										<ul>
											<li>
												<label><input type="checkbox" class="ionSelectChkbox" value="peptide" id="PeptideIon" name="ions[]" />Peptide ion</label></li>
											<li>
												<label><input type="checkbox" class="ionSelectChkbox" value="a" id="AIon" name="ions[]" />A ion</label></li>
											<li>
												<label><input type="checkbox" class="ionSelectChkbox" value="b" id="BIon" name="ions[]" />B ion</label></li>
											<li>
												<label><input type="checkbox" class="ionSelectChkbox" value="c" id="CIon" name="ions[]" />C ion</label></li>
											<li>
												<label><input type="checkbox" class="ionSelectChkbox" value="x" id="XIon" name="ions[]" />X ion</label></li>
											<li>
												<label><input type="checkbox" class="ionSelectChkbox" value="y" id="YIon" name="ions[]" />Y ion</label></li>
											<li>
												<label><input type="checkbox" class="ionSelectChkbox" value="z" id="ZIon" name="ions[]" />Z ion</label></li>
										</ul>
									</div>
								</div>

								<input class="form-control" style="margin-right:2%;width:15%;display:inline;"  required id="myTolerance" title="error tolerance" type="number" min="0" step="0.1" placeholder="Tolerance" name="ms2Tol" autocomplete="off">

								<select class="form-control" style="margin-right:2%;width:13%;display:inline;" required id="myToleranceUnit" title="error tolerance unit" name="tolUnit">
									<option value="ppm">ppm</option>
									<option value="Da">Da</option>
								</select>
							</section>
							<section style="margin-bottom:2%;">
								<div class="form-control" style="height:auto" id="myMods">
								<table id="modificationTable" class="display" width="100%" style="text-align:center;">
									<thead>
										<tr>
											<th>Mod-Input</th>
											<th>Modification</th>
											<th>Mass<span class="resetMod" title="reset modification parameters to default"></span></th>
											<th>Specificity</th>
										</tr>
									</thead>
								</table>
								</div>
							</section>
							<div class="page-header center" style="background-color: #555;margin-top:30px;">
								<input class="btn btn-1 btn-1a network-control" type="submit" value="View Spectrum" id="mybutton3">
								<input class="btn btn-1 btn-1a network-control" type="button" value="Example" onclick="doExample(); return false;" id="mybutton2">
								<input class="btn btn-1 btn-1a network-control" type="button" value="Reset" onclick="doClearForm();" id="mybutton1">
							</div>
						</form>
					</div>
				</div>
			</section>
		</div> <!-- MAIN -->
		<!-- Modal -->
		<div id="addCLModal" role="dialog" class="modal" style="display: none;">
			<div class="header">
				<h1>Add custom cross-linker</h1>
			</div>
			<form id="addCustomCLform" action="#">
				<div style="text-align:center;">
					<input class="form-control" style="margin-top:30px;width:40%;display:inline;"  required id="newCLname" type="text" placeholder="name" name="newCLname">
					<input class="form-control" style="margin-top:30px;margin-left:2%;width:40%;display:inline;"  required id="newCLmodmass" type="text" placeholder="modMass" name="newCLmodmass">
				</div>
				<div class="btn clearfix">
					<input type="submit" class="btn network-control" value="add">
					<input type="button" class="close cancel btn network-control" value="cancel">
				</div>
			</form>
		</div>
		<div id="submitDataModal" role="dialog" class="modal">
			<div class="spinnerWrapper" style="position: relative; margin-top: 130px;"></div>
			<div style="text-align: center; padding-top: 140px; margin:10px;">
				<p>Your data is being processed. Please wait...</p>
				<p>Depending on the size of your data this process may take up to several minutes.</p>
			</div>
		</div>
		<div class="overlay" style="z-index: -1; visibility: hidden;"></div>

		<script type="text/javascript">
		<?php echo "var example = ".($_GET['v'] == "example" ? "true;\r\n" : "false;\r\n");?>
			$( document ).ready(function() {
				if (example)
					doExample();
			});
		</script>

	</body>
</html>
