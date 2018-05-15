<!DOCTYPE HTML>
<html>
	<head>
		<?php
		$cacheBuster = '?v='.microtime(true);
		error_reporting(E_ALL & ~E_NOTICE);
		$pageName = "Upload";
		include("head.php");
		if (isset($_GET['ex'])){
			if ($_GET['ex'] == 'cl');
				$example = "cl";
			if ($_GET['ex'] == 'lin')
				$example = "lin";
			if ($_GET['ex'] == 'pxd')
				$example = "pxd";
		}
		else {
			$example = false;
		};
		?>
		<?php include("./xiSPEC_scripts.php");?>
		<script type="text/javascript" src="./src/PepInputView.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./js/PeptideView.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./src/PrecursorInfoView.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./js/ManualDataInputView.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./js/PrideSelectionView.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./src/model.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./js/upload.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./js/accordion.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./vendor/spin.js"></script>
		<script src="./vendor/jQueryFileUploadMin/jquery.ui.widget.js"></script>
		<script src="./vendor/jQueryFileUploadMin/jquery.iframe-transport.js"></script>
		<script src="./vendor/jQueryFileUploadMin/jquery.fileupload.js"></script>

		<link rel="stylesheet" href="./css/dropdown.css" />
		<link rel="stylesheet" href="./css/manDataInput.css" />
	</head>
	<body>
		<!-- Sidebar -->
		<?php include("navigation.php");?>
			<!-- Main -->
			<div id="main">
			<!-- Intro -->
			<section id="top" class="one">
				<div class="container" id="jquery-fileupload">
					<h1 class="page-header accordionHead"><i <?php echo($example ? 'class="fa fa-plus-square"' : 'class="fa fa-minus-square"');?> aria-hidden="true"></i> Data Upload - Upload your data (identification & peak list file pair)</h1>
					<div class="accordionContent" <?php echo ($example ? 'style="display: none;"' : '');?>>
						<div style="margin-left: 1em; line-height: 1.7em;">
							Supported identification file formats: <a title="HUPO-PIS: mzidentML" href="http://www.psidev.info/mzidentml" target="blank">mzIdentML</a> and <a title="Show column headings" href="help.php#csv">csv</a>.</br>
							Supported peak list file formats: <a title="HUPO-PIS: mzML" href="http://www.psidev.info/mzml" target="blank">mzML</a> and <a title="Mascot Generic Format" href="http://www.matrixscience.com/help/data_file_help.html#GEN">mgf</a> (+ zip/gz archives of mzML/mgf).</br>
							Maximum file size: 500 Mb <br />
							Privacy: Your uploaded data will be kept private unless you choose to make it publicly available upon saving. We will not make use of your data or provide access to others.<br />
							<div style="font-size: 0.8em; line-height: 1.7em; margin-top:0.5em;">
								mzML: Filter out MS1 spectra to reduce file size and upload/parsing time. (e.g. 'MS level 2-' in <a title="Proteowizard download link" href="http://proteowizard.sourceforge.net/downloads.shtml">MSconvert</a>)</br>
								<!-- mzML: Make sure to use centroided MS2 data! (e.g. use 'Peak picking' for profile data in <a title="Proteowizard download link" href="http://proteowizard.sourceforge.net/downloads.shtml">MSconvert</a>)</br> -->
								<!-- mgf: If the file does not contain ALL scans it must either contain the scan number directly in the header (SCANS=XX) or in the title (check TPP compatibility in MSconvert)!<br> -->
								csv: <a href="example/example.csv">download example .csv</a> (<a href="example/example.mzML">corresponding peak list file</a>)
							</div>
						</div>
						<div id="fileUploadWrapper">
							<input id="fileupload" type="file" name="files[]" accept=".mzid,.csv,.mzml,.mgf,.zip,.gz" multiple data-url="vendor/jQueryFileUploadMin/fileUpload.php">
							<label for="fileupload"><span class="uploadbox"></span><span class="btn">Choose file(s)</span></label>
							<div id="uploadProgress">
								<div class="file_upload_bar" style="width: 0%;"><div class="file_upload_percent"></div></div>
							</div>
							<button id="startParsing" disabled="true" class="btn btn-2">Submit Data</button>
						</div>
						<div class="fileupload_info">
						<table>
							<tr id="mzid_fileBox">
								<td style="text-align: center;">Identification file:</td>
								<td>
									<span class="fileName">Select a mzIdentML or csv file to upload</span>
									<span class="statusBox" data-filetype="mzid"></span>
									<input class="uploadCheckbox" type="checkbox" id="mzid_checkbox" style="visibility: hidden;">
								</td>
							</tr>
							<tr id="mzml_fileBox">
								<td style="text-align: center;">Peak list file(s):</td>
								<td>
									<span class="fileName">Select a mzML, mgf or zip file to upload.</span>
									<span class="statusBox" data-filetype="mzml"></span>
									<input class="uploadCheckbox" type="checkbox" id="mzml_checkbox" style="visibility: hidden;">
								</td>
							</tr>
						</table>
						</div>
					</div>
				</div>
			</section>
			<section class="one">
				<div class="container">
					<h1 class="page-header accordionHead"><i <?php echo (($example == 'pxd') ? 'class="fa fa-minus-square"' : 'class="fa fa-plus-square"');?> aria-hidden="true"></i> PRIDE data access</h1>
					<div class="accordionContent" id="prideSelectionWrapper" <?php echo (($example == 'pxd') ? '' : 'style="display: none;"');?>>
						<form id="prideForm">
							<div style="display:flex;">
								<label class="label">PRIDE accession number: <input type="text" id="pxd_in" class="form-control"/></label>
								<button class="btn btn-2" style="margin-left: 1em; margin-bottom: 0.6em; font-size: 0.8em;" type="submit">List files</button>
							</div>
							<div id="pxd_error"></div>
							<div id="pxd_title"></div>
							<div id="pxd_submit" style="display: none;">
								<div id="pxd_submitInfo">
								Please Select 1 RESULT and 1 PEAK file then press Submit selected files.</br>
								Files belonging together usually share the same assayAccession!</div>
								<button type="submit" id="pxd_submitBtn" class="btn btn-2">Submit selected files</button>
							</div>
							<table id="pxdFileTable" class="display" width="100%" style="text-align:center;"></table>
						</form>
					</div>
				</div>
			</section>
			<section class="one">
<!-- <span class="glyphicon glyphicon-upload"></span> -->
				<div class="container">
					<h1 class="page-header accordionHead"><i <?php echo (($example == "lin" || $example == "cl") ? 'class="fa fa-minus-square"' : 'class="fa fa-plus-square"');?> aria-hidden="true"></i> Data Input - Manually input your spectrum data</h1>
					<div class="accordionContent" <?php echo (($example == "lin" || $example == "cl") ? '' : 'style="display: none;"');?> >
						<div id="myManualDataInput" >
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
						</div>
					</div>
				</div>
			</section>
		</div> <!-- MAIN -->
		<!-- Modals -->
		<div id="submitDataModal" role="dialog" class="modal" style="display: none;">
			<div id=submitDataInfo>
				<div id="submitDataTop">
					<div id="errorInfo" style="display: none;">
						<div id="errorMsg"></div>
						<textarea class="form-control" id="errorLog" readonly></textarea>
					</div>
				</div>
				<div id="ionsInfo"  style="display: none;">
					<div id="ionsMsg"></div>
					<form id="ionsForm" method="post" action="php/updateIons.php">
						<div class="multiSelect_dropdown" style="margin-right:2%;">
							<input type="text" class="form-control btn-drop" id="ionSelectionSubmit" title="fragment ion types" value="peptide, b, y" readonly>
							<div class="multiSelect_dropdown-content mutliSelect">
								<ul>
									<li>
										<label><input type="checkbox" class="ionSelectChkboxSubmit" value="peptide" checked id="PeptideIonSubmit" name="ions[]" />Peptide ion</label></li>
									<li>
										<label><input type="checkbox" class="ionSelectChkboxSubmit" value="a" id="AIonSubmit" name="ions[]" />A ion</label></li>
									<li>
										<label><input type="checkbox" class="ionSelectChkboxSubmit" value="b" checked id="BIonSubmit" name="ions[]" />B ion</label></li>
									<li>
										<label><input type="checkbox" class="ionSelectChkboxSubmit" value="c" id="CIonSubmit" name="ions[]" />C ion</label></li>
									<li>
										<label><input type="checkbox" class="ionSelectChkboxSubmit" value="x" id="XIonSubmit" name="ions[]" />X ion</label></li>
									<li>
										<label><input type="checkbox" class="ionSelectChkboxSubmit" value="y" checked id="YIonSubmit" name="ions[]" />Y ion</label></li>
									<li>
										<label><input type="checkbox" class="ionSelectChkboxSubmit" value="z" id="ZIonSubmit" name="ions[]" />Z ion</label></li>
								</ul>
							</div>
						</div>
						<button type="submit" id="ionsFormSubmit" class="btn btn-2">update ions</button>
						<div id="ionsUpdateMsg" style="font-size: 0.8em;display: inline;"></div>
					</form>
				</div>
				<div id="modificationsInfo"  style="display: none;">
					<div id="modificationsMsg"></div>
					<form id="csvModificationsForm" method="post" action="php/submitModDataForCSV.php"></form>
				</div>
				<div id="submitDataControls">
					<button id="cancelUpload" class="btn btn-2" href="#">Cancel</button>
					<a id="gitHubIssue" class="btn btn-1a" style="display:none;" href='https://github.com/Rappsilber-Laboratory/xiSPEC/issues'>
						<i class="fa fa-github" aria-hidden="true"></i>Create issue
					</a>
					<button id="continueToDB" class="btn btn-2" href="#">Continue</button>
				</div>
			</div>
			<div id="processDataInfo">
				<div class="spinnerWrapper"></div>
				<div id="processText" style="text-align: center; padding-top: 140px; margin:10px;"></div>
			</div>
		</div>
		<div class="overlay" style="z-index: -1; visibility: hidden;"></div>

		<script type="text/javascript">
		<?php echo ('var example = "'.$example.'";');?>
			$( document ).ready(function() {
				if (example == 'cl')
					manualDataInputView.clExample();
				else if (example == 'lin')
					manualDataInputView.linExample();
				else if (example == 'pxd')
					window.prideSelectionView.load_pxd('PXD005654');
			});
		</script>

	</body>
</html>
