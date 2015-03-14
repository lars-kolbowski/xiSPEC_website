<!DOCTYPE HTML>
<html>
	<head>
		<?php $pageName = "Home";include("head.php");?>
	</head>
	
	<body style="height:100%;">
		
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
							<label>
								Self-Links
								<input checked="checked" 
										id="internal" 			
										onclick="xlv.hideInternal(!document.getElementById('internal').checked)" 
										type="checkbox"
									/>
							</label>			
						</div>		
					<?php include("slideyPanels.php"); ?>
					
				</div><!-- container -->
			</section>

	<div id="networkContainer" class="skel-panels-fixed"></div>
	
	<div class="long-citation skel-panels-fixed">
		<div class="container">
			<p>Data from <a target="_blank" href="http://www.sciencemag.org/content/337/6100/1348">Herzog, F. et al. Structural Probing of a Protein Phosphatase 2A Network by Chemical Cross-Linking and Mass Spectrometry. Science 337, 1348–1352 (2012).</a></p>
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


			d3.text('./crosslink-viewer/data/Herzog.csv', "text/csv", function(text) {
				xlv.readCSV(text);
			});
	  	}, false);
	  	//]]>
	  	</script>
	</body>
</html>
