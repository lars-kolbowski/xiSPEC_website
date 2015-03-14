<!DOCTYPE HTML>
<html>
	<head>
		<?php $pageName = "Example 1";include("head.php");?>
	</head>
	
	<body>
		
		<!-- Sidebar -->
		<?php include("navigation.php");?>
		
		<!-- Main -->
		<div id="main">
		
			<!-- Intro -->
			<section id="top" class="one">
				<div class="container">
					<div class="page-header center">
						<button class="btn btn-1 btn-1a network-control" onclick="toggleInfoPanel()">Selection Details</button>
						<button class="btn btn-1 btn-1a network-control" onclick="xlv.autoLayout();">Auto Layout</button>
						<button class="btn btn-1 btn-1a network-control resetzoom" onclick="xlv.resetZoom();">Reset Zoom</button>
						<button class="btn btn-1 btn-1a network-control" onclick="xlv.exportSVG('networkSVG');">Export</button>
						<button class="btn btn-1 btn-1a network-control" id="help" onclick="toggleHelpPanel()">Help</button>
						<!--<button class="btn btn-1 btn-1a network-control" onclick="window.location.href='upload.php'">Upload</button>-->
					</div>
					
                		<div class="controlsexamplespage skel-panels-fixed center">
							
								<div class="controls-box-one">
									<div id="scoreSlider">
										<p class="scoreLabel" id="scoreLabel1"></p>
										<input id="slide" type="range" min="1" max="100" step="1" value="0" oninput="sliderChanged()">
										<p class="scoreLabel" id="scoreLabel2"></p>
									</div>
								</div>	
								<div class="controls-box-two">		
										<div id="scoreSlider">
											<p id="cutoffLabel">Cut-Off:</p>
										</div> <!-- outlined scoreSlider -->
								</div>
							<div class="controls-box-three">
									<label>
											Self-Links
					     			 		<input checked="checked" 
												   id="internal" 			
												   onclick="xlv.hideInternal(!document.getElementById('internal').checked)" 
												   type="checkbox"
											/>
					     	   		</label>
							</div>
					
 						</div> <!-- PAGE-HEADER-->
						
					
					<?php include("slideyPanels.php"); ?>
					
				</div><!-- container -->
			</section>

			<div id="networkContainer" class="skel-panels-fixed"></div>

	
	<div class="long-citation skel-panels-fixed">
		<div class="container">
			<p>In this example the domain annotations match those used in <a href="http://www.nature.com/emboj/journal/v29/n4/full/emboj2009401a.html" target="_blank">Chen ZA, Jawhari A, Fischer L, Buchen C, Tahir S, Kamenski T, Rasmussen M, Lariviere L, Bukowski-Wills J-C, Nilges M, Cramer P &amp; Rappsilber J (2010) Architecture of the RNA polymerase II–TFIIF complex revealed by cross-linking and mass spectrometry. The EMBO Journal 29: 717–726</a>.</p>
		</div>
	</div>
		</div> <!-- MAIN -->
	  	<?php include('./xiNET_scripts.php'); ?>
	  	
	  	<script type="text/javascript">
	  	//<![CDATA[
	  	
	  	window.addEventListener('load', function() {
	  	
	  	var targetDiv = document.getElementById('networkContainer');
	  	var messageDiv = document.getElementById('networkCaption');
	  	xlv = new xiNET.Controller(targetDiv);
	  	xlv.setMessageElement(messageDiv);
	  	
	  	<?php include('./data/polIIdata.php'); ?>
	  	document.getElementById('scoreLabel1').innerHTML = xlv.scores.min.toFixed(2);
		document.getElementById('scoreLabel2').innerHTML = xlv.scores.max.toFixed(2) + '&nbsp;&nbsp;';
		document.getElementById('cutoffLabel').innerHTML = 'Cut-off:&nbsp;' + xlv.scores.min.toFixed(2);
		xlv.init();
	  	xlv.autoLayout();
	  	new xiNET.DASUtil(xlv);
	  	//~ xlv.checkLinks();
	  		  	
	  	}, false);
	  	//]]>
	  	</script>
	  	
	  	<script type="text/javascript">	
				//<![CDATA[
				function sliderChanged(){
					var slide = document.getElementById('slide');
					var cut = calcCutOff(slide.value / 100.0);
					var cutoffLabel = document.getElementById("cutoffLabel");
					cutoffLabel.innerHTML = 'Cut-off: ' + cut;
					xlv.setCutOff(cut);
				}
				
				function calcCutOff(v) {
					var result = (v * (xlv.scores.max - xlv.scores.min)) + xlv.scores.min;
					result = result.toFixed(2);
					return result;
				}				
				
                //]]>
        </script>
	</body>
</html>
