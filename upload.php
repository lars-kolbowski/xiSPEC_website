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
		<script type="text/javascript" src="./src/PrecursorInfoView.js"></script>	
		<script type="text/javascript" src="./src/model.js"></script>		
		<script type="text/javascript" src="./js/upload.js"></script>
        <script type="text/javascript" src="./vendor/spin.js"></script>
		
		<script src="vendor/jQueryFileUploadMin/jquery.ui.widget.js"></script>
		<script src="vendor/jQueryFileUploadMin/jquery.iframe-transport.js"></script>
		<script src="vendor/jQueryFileUploadMin/jquery.fileupload.js"></script>
		<script>
		$(function () {
		    $('#fileupload').fileupload({
		        dataType: 'json',
		        fileTypes: "mzid|mzml",
		        maxChunkSize: 10000000,	//10MB
				progressall: function (e, data) {
				    var progress = parseInt(data.loaded / data.total * 100, 10);
				    $('#uploadProgress .file_upload_bar').css(
				        'width',
				        progress + '%'
				    );
				},
				add: function (e, data) {

					if(new RegExp("(.mzid)$", 'i').test(data.files[0].name)){
						$('#mzid_checkbox').prop( "checked", false ).change();
						$('#mzid_fileBox .fileName').html(data.files[0].name);
						data.context = $('#mzid_fileBox .statusBox').html("Uploading...");
						data.submit();
					}

					if(new RegExp("(.mzml)$", 'i').test(data.files[0].name)){
						$('#mzml_checkbox').prop( "checked", false ).change();
						$('#mzml_fileBox .fileName').html(data.files[0].name);
						data.context = $('#mzml_fileBox .statusBox').html("Uploading...");
						data.submit();						
					}


				},
				done: function (e, data) {
					if(data.context[0].dataset['filetype'] == 'mzml')
						$('#mzml_checkbox').prop( "checked", true ).change();
					if(data.context[0].dataset['filetype'] == 'mzid')
						$('#mzid_checkbox').prop( "checked", true ).change();
				    data.context.text('Upload finished.');
				}
		    });

			$(".uploadCheckbox").change(function(){
			    if ($('.uploadCheckbox:checked').length == $('.uploadCheckbox').length) {
			       $('#startParsing').prop('disabled', false);
			    }
			    else{
			    	$('#startParsing').prop('disabled', true);
			    }
			});

			$("#startParsing").click(function(){
				var spinner = new Spinner({scale: 5}).spin (d3.select("#jquery-fileupload").node());
				var formData = new FormData();
				formData.append("mzml_fn", $('#mzml_fileBox .fileName').html());
				formData.append("mzid_fn", $('#mzid_fileBox .fileName').html());
				$.ajax({
			        url: "php/parseData.php",
					type: 'POST',
					data: formData,
					async: false,
					cache: false,
					contentType: false,
					processData: false,
					success: function (data) {
						spinner.stop();
						console.log(data);
					}
				  });	 
				  return false;					
			});


		});
		</script>

        <link rel="stylesheet" href="./css/dropdown.css" />

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
					<h1 class="page-header">Data Input</h1>
					<form id="manUpPepForm" action="viewSpectrum.php" method="post" target="_blank">
					<!-- <form id="xisv_entryform"  action="http://spectrumviewer.org/xisv/index.php" method="post" target="_blank" onsubmit="doPreSubmission();"> -->
						<section style="margin-bottom:2%;">
						<div style="margin-bottom:30px;width:30%;min-width:300px;display:inline;min-width:300px;margin-right:2%;float:left;">
							<input style="width:100%;margin-bottom:10px" class="form-control" id="myPeptide" required type="text" placeholder="Peptide Sequence1[;Peptide Sequence2]" name="peps" autofocus>
							<textarea class="form-control" style="padding-bottom:0px;" id="myPeaklist" required type="text" placeholder="Peak List [m/z intensity]" name="peaklist"></textarea>
						</div>
						<div style="width:68%;display:inline;">
							<div style="padding-bottom:15px;"> Peptide Preview:</div>
							<div style="height:210px;font-size:100%;overflow-y: hidden;position: relative;" id="peptideDiv" class="form-control" ></div>
						</div>
						</section>
						<section style="clear:left;text-align:center;margin-bottom:2%;">
							<select class="form-control" style="margin-right:2%;width:25%;display:inline;" required id="myCL" name="clModMass">
								<option value="" disabled selected>Select cross-linker...</option>
								<option value="add">add your own...</option>
								<option value="138.06807961">BS3 [138.06807961 Da]</option>
							</select>

					  		<input class="form-control" style="margin-right:2%;width:10%;display:inline;"  required id="myPrecursorZ" type="number" min="1" placeholder="Charge" name="preCharge" autocomplete="off">


								<div class="dropdown">
									<input type="text" class="form-control btn-drop" id="ionSelection" value="Select ions..." readonly>
									<div class="dropdown-content mutliSelect">
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


<!-- 							<select class="form-control" style="margin-right:2%;width:15%;display:inline;" id="myFragmentation" name="fragMethod">
								<option value="HCD">HCD</option>
								<option value="CID">CID</option>
								<option value="ETD">ETD</option>
								<option value="ETciD">ETciD</option>
								<option value="EThcD">EThcD</option>
							</select> -->

							<input class="form-control" style="margin-right:2%;width:15%;display:inline;"  required id="myTolerance" type="number" min="0" step="0.1" placeholder="Tolerance" name="ms2Tol" autocomplete="off">

							<select class="form-control" style="margin-right:2%;width:15%;display:inline;" required id="myToleranceUnit" name="tolUnit">
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
									    <th>Mass <div class="tooltip"><a href="#" id="resetModMasses"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAFaUlEQVRoQ+1YaWwUZRh+3plp60pJC4GIZUEgsXigRgvqD0BqW2oLpYWkaLiCoYjUNlgx9fphDcSDCBVbSitVCRCRJYQA5ehFEfihUjRqUFoTqhzVKpRyuWW7O6+Z0iHDdnZnZncIEJhkszPf937P8b3fNUO4xS+6xfXjjoEbncE7Gbg9MjBzixNeKsWmaVl2G76+QyjbJQqSmA+mJQCi5a+n2c5nO6DawxEztibIjAoACWqZb+NU2/lsB0S2K1qSopaAOB+AqB0y3q+ybOezFTBy1tYsMJUAcOqNdY/HE4vN08/ZOQ9sMeCYucUpQyoFIdNAnJuBzQK4snND1gE7jIRnINslRkVF5RFhqTJJLQpqInCFu9PzKTZP91lsezU8LAN3z95eAcJLoZL3tKv6j6NewPrUS6HghGUA2S6xj8ORx4SlZD0DGr3ciAiefKkys82qifAM9LA5cnY5Ra9cCrDRHAh2emwBkH5hbcZRKyZsMaAS9p27IwtEJRRgFTIURmg+Lw0ahc9GdxnG9gTYakDBHJjrir7cGb2E0HsfAKga4NRg4ghY3PHFpBU3zIBKHJNTlSAyVbBmJz77+STql7M7HSyvAjAsgMhzXRFS/MXy1H/MmLA9A9eQZrvE/jHRyo7cfRZqr0zv5oudt/M+gUjZB4boi6TK9sq0+TfeQI+C/jm7nERUemZN2tXT6ID5O+MZdDjA/iFD9A09U55xysjE9c2AAfvABbvfB+MtvTBmIeP0mtSqm9qAc96e/h4Jf+plgcFv/1uR9sFNbUARd8+CPXtBSNQRurGt/LkZthiIW1gzhYE0EF8dcsTU1il5l7eXpJ83IglWf+/C6jIAC3Vijvy1OnWUEbapORCXW90BIKYXGFNe6+qJypIY8jU4t/odRvdh0P/qbC1LdRgBmzIw+JWakwAG9zaADafKJs42IglW78ytWQ/CLJ2YUydXTdR9r9DGmjIwJL/2ezDG6JD8dqI05aFwDAzJq/0VwIO9MAiHTpSkPGmEbcrA0PzaTQCm64DJDocjtmnZ2AtGRHr1IwsP9nW73crwFHTqXcdLUp43wjVlYFh+/Vwm/lIPjIk/Ob4ypcCISK9+2KLaYjC9qtuW6cU/SpLWGuGaMhC/uGFAl9f3t/9Leg84AzShZWXSfiMybf3wRfXjAd4H6J6wfRGSOKh5eeJpI0xTBhSQEQX1O8CYrAtI1CJBTm4uTj5mRKjUxxfUjfBCqAPzcH08VB0rTsowg2XawPBFdY+KgvBjgPGqcF0i8Ou/FyeVByO+v6D+ZQZ9DKBPgDjZJ8uPt6xM/tlWA1d6bu86Jhgtm/uJaTsL8qFIt/CD0s7jkJ8gWRjDxFMAjA8mjBjrm4ufnWNGvBJjOgNK8CNvHuh3ucv3LcDx5ghIvhLHeqvMtRDdSqg5ShKf/uXDcWfN4Vs0oIA+8FpdPAvidwTEmiUJMG+uKWagg3ziU0dXjGu2gmspAyrwyMKG0QLTNgBxVsgCTFiluFUGZzYtS2y0iheSAYVkZOHBOAG+bQSMtkJKvRkbvSxmNi0b22oFR40N2YACMKGoQTrtpvkAvaucjAMK0GdpA/N7Axy8Zl9RojcU8ZYncSCSh4saosVOaQ6DpwJ4BoSIALHK55JviGmr7y7vuiNFiRdDFW5LBvTIE96ojfFIkY8RUxwRdc8RZm5l4tZIr+enwx+l3Hxfp8PtxXDahzUHwiG2q+1tbUAxr/60C4KZTuGeDCj/evemE2SGTBWpHAe0ov2fAxnSivEXqxpQ/5Wjh7ZM+xxkHwzs11+8VrR6r1emNaPc+wtVnrXi1Hv/Mm15SAb8G6kZ0+ttf9Ha4WXU8yEPpf8BwRqnQJnMMukAAAAASUVORK5CYII=" width="16" height="16" alt="revert"></a>
													<span class="tooltiptext">Reset to default</span>
												 </div></th>
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
			</section>
			<section id="bottom" class="one">

				<div class="container" id="jquery-fileupload">
					<h1 class="page-header">Data Upload</h1>
						<input id="fileupload" type="file" name="files[]" accept=".mzid,.mzml" multiple data-url="vendor/jQueryFileUploadMin/fileUpload.php">
						<div id="uploadProgress">
							<div class="file_upload_bar" style="width: 1%;"></div>
						</div>
						<div class="fileName_box" id="mzid_fileBox">mzid file: <span class="fileName"></span> <span class="statusBox" data-filetype="mzid"></span><input class="uploadCheckbox" type="checkbox" id="mzid_checkbox" style="visibility: hidden;"></div>
						<div class="fileName_box" id="mzml_fileBox">mzid file: <span class="fileName"></span> <span class="statusBox" data-filetype="mzml"></span><input class="uploadCheckbox" type="checkbox" id="mzml_checkbox" style="visibility: hidden;"></div>
						<button id="startParsing" onclick="console.log('start parsing');" disabled="true">Submit Data</button>
				</div>

<!-- 				<div class="container">
					<h1 class="page-header">Data Upload</h1>
					<div id="fileUploadMain">
						<form action="php/file_upload.php" method="post" id="fileUploadForm" enctype="multipart/form-data">
							Select files to upload:<br/>
							mzid:
							<input type="file" name="mzidToUpload" id="mzidToUpload">
							<br/>
							mzml:
							<input type="file" name="mzmlToUpload" id="mzmlToUpload">
							<input type="submit" value="Upload Files" name="submit">
						</form>
					</div>
				</div> -->
			</section>
			</section>
		</div> <!-- MAIN -->
	</body>
</html>