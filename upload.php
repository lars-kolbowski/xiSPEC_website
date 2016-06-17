<!DOCTYPE HTML>
<html>
	<head>
		<?php
		$pageName = "Home";
		include("head.php");
		?>
		<?php include("xiSPEC_scripts.php");?>
		<script type="text/javascript" src="./js/PeptideView.js"></script>	
		<script type="text/javascript" src="./js/PeptideModel.js"></script>	
		<script type="text/javascript" src="./js/upload.js"></script>
	</head>
	<body>
		<!-- Sidebar -->
		<?php include("navigation.php");?>

  	 	<!-- Main -->
   	 	<div id="main">
	
   	 	<!-- Intro -->
			<section id="top" class="one">
				<div class="container">
					<h1 class="page-header">Upload</h1>
					<form id="manUpPepForm" action="userinput_to_json.php" method="post" target="_self" onsubmit="doPreSubmission();"">
					<!-- <form id="xisv_entryform"  action="http://spectrumviewer.org/xisv/index.php" method="post" target="_blank" onsubmit="doPreSubmission();"> -->
				  		<input style="margin-bottom:30px;width:25%;" class="form-control" id="myPeptide" required type="text" placeholder="Peptide Sequence1[;Peptide Sequence2]" name="peps" autofocus>
						<div style="text-align:center;">
							<section>
								<textarea class="form-control" style="padding-bottom:0px;width:50%;float:left;" onfocus="handleFB('myPeaklist','f','Peak list');" id="myPeaklist" required type="text" placeholder="Peak List" name="peaklist"></textarea>
								<div id="peptideDiv"></div>
							</section>
							<section style="clear:left">
							<input class="form-control" style="margin-top:30px;margin-right:2%;width:22%;display:inline;" required id="clModMass" type="text" placeholder="cross-linker mod mass" name="clModMass">

					  		<input class="form-control" style="margin-top:30px;margin-right:2%;width:15%;display:inline;"  required id="myPrecursorZ" type="text" placeholder="Charge" name="preCharge">

							<select class="form-control" style="margin-top:30px;margin-right:2%;width:15%;display:inline;" id="myFragmentation" name="fragMethod">
								<option value="HCD">HCD</option>
								<option value="CID">CID</option>
								<option value="ETD">ETD</option>
								<option value="ETciD">ETciD</option>
								<option value="EThcD">EThcD</option>
							</select>

							<input class="form-control" style="margin-top:30px;margin-right:2%;width:15%;display:inline;"  required id="myTolerance" type="text" placeholder="Tolerance" name="ms2Tol">

							<select class="form-control" style="margin-top:30px;margin-right:2%;width:15%;display:inline;" id="myToleranceUnit" name="tolUnit">
								<option value="ppm">ppm</option> 
								<option value="Da">Da</option>
							</select>
							</section>	
						</div>
						<div class="page-header center" style="background-color: #555;margin-top:30px;">
							<input class="btn btn-1 btn-1a network-control" type="submit" value="View Spectrum" id="mybutton3">
						    <input class="btn btn-1 btn-1a network-control" type="submit" value="Example" onclick="doExample(); return false;" id="mybutton2">
							<input class="btn btn-1 btn-1a network-control" type="button" value="Reset" onclick="doClearForm();" id="mybutton1">
						</div>
					</form>								
				</div>
			</section>
		</div> <!-- MAIN -->
	</body>
</html>