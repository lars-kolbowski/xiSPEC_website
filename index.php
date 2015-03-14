<!DOCTYPE HTML>
<html>
	<head>
		<?php
		$pageName = "Home";
		include("head.php");
		?>
	</head>
	<body>
   	 	<!-- Sidebar -->
   	 	<?php include("navigation.php");?>

   	 	<!-- Main -->
   	 	<div id="main">
	
   	 	<!-- Intro -->			
   	 				<div class="container">
   	 					<h1 class="page-header">A tool for exploring and communicating cross‑linking / mass spectrometry data.</h1>
   	 					<div class="external-link">
									<p>xiNET is an <a target="_blank" href="https://github.com/colin-combe/crosslink-viewer">open source</a> visualisation of cross‑linking / mass spectrometry data. It provides an interactive 2D map of the cross‑link network. Below you can see cross&#045;links on the <a href="http://www.nature.com/emboj/journal/v29/n4/full/emboj2009401a.html" target="_blank"> RNA&nbsp;polymerase&nbsp;II&#8209;TFIIF&nbsp;complex</a>. Click node for residue resolution detail.</p>
							</div><!-- external-link -->
							
							<div class="page-header controlsFrontPage skel-panels-fixed center">
								
								<div class="controls-box-one">
									<div id="scoreSlider">
										<p class="scoreLabel" id="scoreLabel1"></p>
										<input id="slide" type="range" min="1" max="100" step="1" value="0" oninput="sliderChanged()">
										<p class="scoreLabel" id="scoreLabel2"></p>
									</div>
								</div>
									
								<div class="controls-box-two">		
										<div id="scoreSlider">
											<p id="cutoffLabel">Score Cut-Off:</p>
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
			 			</div> <!-- CONTAINER -->
			<div id="networkFrontPage" class="skel-panels-fixed"></div>
		</div> <!-- MAIN -->
   	 				<?php include("xiNET_scripts.php"); ?>

   	 				<script>//<![CDATA[
					window.addEventListener('load', function() {
   	 				
						var targetDiv = document.getElementById('networkFrontPage');
						xlv = new xiNET.Controller(targetDiv);
						
						<?php include('./data/polIIdata.php'); ?>
			
						document.getElementById('scoreLabel1').innerHTML = xlv.scores.min.toFixed(2);
						document.getElementById('scoreLabel2').innerHTML = xlv.scores.max.toFixed(2) + '&nbsp;&nbsp;';
						document.getElementById('cutoffLabel').innerHTML = 'Cut-off:&nbsp;' + xlv.scores.min.toFixed(2);
					
						xlv.initLayout();
						xlv.initProteins();
						xlv.checkLinks();
								
   	 				
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
