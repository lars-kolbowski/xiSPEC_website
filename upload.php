<!DOCTYPE HTML>
<html>
	<head>
		<?php
		$pageName = "Home";
		include("head.php");
		?>
		<?php include("xiSPEC_scripts.php");?>
		<script type="text/javascript" src="./js/PepInputView.js"></script>			
		<script type="text/javascript" src="./js/PeptideView.js"></script>	
		<script type="text/javascript" src="./js/PeptideModel.js"></script>	
		<script type="text/javascript" src="./js/upload.js"></script>
	</head>
	<body>
		<!-- Sidebar -->
		<?php include("navigation.php");?>

  	 	<!-- Main -->
   	 	<div id="main">

		<!-- Modal -->
		<div id="addCLModal" role="dialog" class="modal">
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

	
   	 	<!-- Intro -->
			<section id="top" class="one">
				<div class="container">
					<h1 class="page-header">Upload</h1>
					<form id="manUpPepForm" action="userinput_to_json.php" method="post" target="_self" onsubmit="doPreSubmission();"">
					<!-- <form id="xisv_entryform"  action="http://spectrumviewer.org/xisv/index.php" method="post" target="_blank" onsubmit="doPreSubmission();"> -->
				  		<input style="margin-bottom:30px;width:25%;min-width:300px" class="form-control" id="myPeptide" required type="text" placeholder="Peptide Sequence1[;Peptide Sequence2]" name="peps" autofocus>
						<div style="text-align:center;">
							<section>
								<textarea class="form-control" style="padding-bottom:0px;width:30%;min-width:300px;display: table-cell;float:left;" id="myPeaklist" required type="text" placeholder="Peak List" name="peaklist"></textarea>
								<div id="peptideDiv" style="width:700px;display: table-cell;text-align:left;padding:2%">Peptide Preview:</div>
							</section>
							<section style="clear:left">
							<select class="form-control" style="margin-right:2%;width:20%;display:inline;" id="myCL" name="clModMass">
								<option value="" disabled selected>Select cross-linker</option>
								<option value="add">add your own...</option>
								<option value="138.06807961">BS3 [138.06807961 Da]</option>
							</select>

					  		<input class="form-control" style="margin-right:2%;width:15%;display:inline;"  required id="myPrecursorZ" type="text" placeholder="Charge" name="preCharge">

							<select class="form-control" style="margin-right:2%;width:15%;display:inline;" id="myFragmentation" name="fragMethod">
								<option value="HCD">HCD</option>
								<option value="CID">CID</option>
								<option value="ETD">ETD</option>
								<option value="ETciD">ETciD</option>
								<option value="EThcD">EThcD</option>
							</select>

							<input class="form-control" style="margin-right:2%;width:15%;display:inline;"  required id="myTolerance" type="text" placeholder="Tolerance" name="ms2Tol">

							<select class="form-control" style="margin-right:2%;width:15%;display:inline;" id="myToleranceUnit" name="tolUnit">
								<option value="ppm">ppm</option> 
								<option value="Da">Da</option>
							</select>
							</section>	
						</div>
						<div class="page-header center" style="background-color: #555;margin-top:30px;">
							<input class="btn btn-1 btn-1a network-control" type="submit" value="View Spectrum" id="mybutton3">
						    <input class="btn btn-1 btn-1a network-control" type="button" value="Example" onclick="doExample(); return false;" id="mybutton2">
							<input class="btn btn-1 btn-1a network-control" type="button" value="Reset" onclick="doClearForm();" id="mybutton1">
						</div>
					</form>								
				</div>
			</section>
		</div> <!-- MAIN -->
	</body>
</html>