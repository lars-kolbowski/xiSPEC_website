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
		<?php include("xiSPEC_scripts.php");?>
		<script type="text/javascript" src="./src/PepInputView.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./js/PeptideView.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./src/PrecursorInfoView.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./src/model.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./js/upload.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="./js/accordion.js<?php echo $cacheBuster ?>"></script>
		<script type="text/javascript" src="src/PrideSelectionView.js<?php echo $cacheBuster ?>"></script>
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
					<h1 class="page-header accordionHead"><i <?php echo($example ? 'class="fa fa-plus-square"' : 'class="fa fa-minus-square"');?> aria-hidden="true"></i> Data Upload - Upload your data (identification & peak list file pair)</h1>
					<div class="accordionContent" <?php echo ($example ? 'style="display: none;"' : '');?>>
						<div style="margin-left: 1em; line-height: 1.7em;">
							Supported identification file formats: <a title="HUPO-PIS: mzidentML" href="http://www.psidev.info/mzidentml" target="blank">mzIdentML</a> and <a title="Show column headings" class="showCsvHeader" href="#">csv</a>.</br>
							Supported peak list file formats: <a title="HUPO-PIS: mzML" href="http://www.psidev.info/mzml" target="blank">mzML</a> and <a title="Mascot Generic Format" href="http://www.matrixscience.com/help/data_file_help.html#GEN">mgf</a> (+ zip/gz archives of mzML/mgf).</br>
							<div style="font-size: 0.8em; line-height: 1.7em; margin-top:0.5em;">
								mzML: Filter out MS1 spectra to reduce upload/parsing time. (e.g. 'MS level 2-' in <a title="Proteowizard download link" href="http://proteowizard.sourceforge.net/downloads.shtml">MSconvert</a>)</br>
								<!-- mzML: Make sure to use centroided MS2 data! (e.g. use 'Peak picking' for profile data in <a title="Proteowizard download link" href="http://proteowizard.sourceforge.net/downloads.shtml">MSconvert</a>)</br> -->
								mgf: If the file does not contain ALL scans it must either contain the scan number directly in the header (SCANS=XX) or in the title (check TPP compatibility in MSconvert)!<br>
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
									<option value="138.068080">BS3/DSS [138.068080 Da]</option>
									<option value="142.093177">BS3/DSS-d4 [142.093177 Da]</option>
									<option value="82.041865">SDA [82.041865 Da]</option>
									<option value="158.003765">DSSO [158.003765 Da]</option>
									<option value="-19.972072">Photo-Methionine [-19.972072 Da]</option>
									<option value="-19.972072">Photo-Leucine [-16.0313 Da]</option>
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
								<input class="btn btn-1 btn-1a network-control" type="submit" value="View Spectrum">
								<input class="btn btn-1 btn-1a network-control" type="button" value="cross-link example" onclick="doExampleCL(); return false;">
								<input class="btn btn-1 btn-1a network-control" type="button" value="linear example" onclick="doExampleLinear(); return false;">
								<input class="btn btn-1 btn-1a network-control" type="button" value="Reset" onclick="doClearForm();">
							</div>
						</form>
					</div>
				</div>
			</section>
		</div> <!-- MAIN -->
		<!-- Modals -->
		<div id="csvHeaderModal" role="dialog" class="modal" style="display: none;">
			<div class="header">
				<h1>Peptide identification csv column headings</h1>
			</div>
			<div style="margin: 1em;">
				<table class="myTable" id="csvTable">
					<thead>
						<tr><th>column</th><th>required</th><th>default</th><th>example(s)</th></tr>
					</thead>
					<tbody>
						<tr><td>Id</td><td>Yes</td><td></td><td>SIR_1</td></tr>
						<tr><td>Rank</td><td>No*</td><td>1</td><td>1</td></tr>
						<tr><td>ScanNumber</td><td>Yes</td><td></td><td>2256</td></tr>
						<tr><td>Charge</td><td>Yes</td><td></td><td>3</td></tr>
						<tr><td>FragmentTolerance</td><td>No</td><td>10 ppm</td><td>10 ppm | 0.2 Da</td></tr>
						<tr><td>IonTypes</td><td>No</td><td>peptide;b;y</td><td>peptide;c;z</td></tr>
						<tr><td>PepSeq 1</td><td>Yes</td><td></td><td>LKECcmCcmEKPLLEK</td></tr>
						<tr><td>PepSeq 2</td><td>No**</td><td></td><td>HPYFYAPELLFFAKR</td></tr>
						<tr><td>LinkPos 1</td><td>No**</td><td></td><td>2</td></tr>
						<tr><td>LinkPos 2</td><td>No**</td><td></td><td>14</td></tr>
						<tr><td>CrossLinkerModMass</td><td>No**</td><td></td><td>138.06808</td></tr>
						<tr><td>PassThreshold</td><td>No</td><td>TRUE</td><td>TRUE | FALSE</td></tr>
						<tr><td>Score</td><td>No</td><td></td><td>10.5641</td></tr>
						<tr><td>IsDecoy</td><td>No</td><td>FALSE</td><td>TRUE | FALSE</td></tr>
						<tr><td>Protein 1</td><td>Yes</td><td></td><td>HSA</td></tr>
						<tr><td>Protein 2</td><td>No**</td><td></td><td>HSA</td></tr>
						<tr><td>RunName</td><td>No</td><td></td><td>example_file</td></tr>
					</tbody>
				</table>
				<p style="font-size: small;line-height: 1.5em;">
					*required if there are multiple alternative explanations for the same spectrum/id.</br>
					**required for cross-linked peptides.
				</p>
			</div>
		</div>
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
						<div class="mulitSelect_dropdown" style="margin-right:2%;">
							<input type="text" class="form-control btn-drop" id="ionSelectionSubmit" title="fragment ion types" value="peptide, b, y" readonly>
							<div class="mulitSelect_dropdown-content mutliSelect">
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
					doExampleCL();
				else if (example == 'lin')
					doExampleLinear();
				else if (example == 'pxd')
					window.prideSelectionView.load_pxd('PXD005654');
			});
		</script>

	</body>
</html>
