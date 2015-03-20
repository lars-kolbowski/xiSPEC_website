<!DOCTYPE html>
<html>
    <head>
		<?php $pageName = "Figure 6";include("head.php");?>
		<?php include("xiNET_scripts.php");?>
		<link rel="stylesheet" href="css/noNav.css" />
    </head>
    <body>

	<!-- Slidey panels -->	
	<div class="overlay-box" id="infoPanel">
	<div id="networkCaption">
		<p>No selection.</p>
	</div>
	</div>

	<div class="overlay-box" id="helpPanel">
	<table class="overlay-table"  bordercolor="#eee" >
		<tr>
			<td>Toggle the proteins between a bar and a circle</td>
			<td>Click on protein</td>
		</tr>
		<tr>
			<td>Zoom</td>
			<td>Mouse wheel</td>
		</tr>
		<tr>
			<td>Pan</td>
			<td>Click and drag on background</td>
		</tr>
		<tr>
			<td>Move protein</td>
			<td>Click and drag on protein</td>
		</tr>
		<tr>
			<td>Expand bar <br>(increases bar length until sequence is visible)</td>
			<td>Shift_left-click on protein</td>
		</tr>
		<tr>
			<td>Rotate bar</td>
			<td>Click and drag on handles that appear at end of bar</td>
		</tr>
		<tr>
			<td>Hide/show protein (and all links to it)</td>
			<td>Right-click on protein</td>
		</tr>
		<tr>
			<td>Hide links between two specific proteins</td>
			<td>Right click on any link between those proteins</td>
		</tr>
		<tr>
			<td>Show all hidden links</td>
			<td>Right click on background</td>
		</tr>
		<tr>
			<td>'Flip' self-links</td>
			<td>Right-click on self-link</td>
		</tr>
	</table> 
</div>	

<div class="overlay-box" id="legendPanel">
	<div><img src="./images/fig3_1.svg"></div>
</div>	


		<script type="text/javascript">
				//<![CDATA[
				helpShown = false;
				infoShown = false;
				legendShown = false;
				function toggleHelpPanel() {
					if (helpShown){
						hideHelpPanel();
					}
					else {
						showHelpPanel();
					}
				}
				
				function toggleInfoPanel() {
					if (infoShown){
						hideInfoPanel();
					}
					else {
						showInfoPanel();
					}
				}
				function toggleLegendPanel() {
					if (legendShown){
						hideLegendPanel();
					}
					else {
						showLegendPanel();
					}
				}
				
				function showHelpPanel() {
						helpShown = true;
						d3.select("#helpPanel").transition().style("height", "500px").style("top", "100px").duration(700);
				}
				function hideHelpPanel() {
						helpShown = false;
						d3.select("#helpPanel").transition().style("height", "0px").style("top", "-95px").duration(700);
				}
				function showInfoPanel() {
						infoShown = true;
						d3.select("#infoPanel").transition().style("height", "300px").style("bottom", "115px").duration(700);

				}
				function hideInfoPanel() {
						infoShown = false;
						d3.select("#infoPanel").transition().style("height", "0px").style("bottom", "-95px").duration(700);

				}
				function showLegendPanel() {
						legendShown = true;
						d3.select("#legendPanel").transition().style("height", "500px").style("top", "100px").duration(700);

				}
				function hideLegendPanel() {
						legendShown = false;
						d3.select("#legendPanel").transition().style("height", "0px").style("top", "-95px").duration(700);

				}
				//]]>
		</script>
		<!-- Main -->
		<div id="main">
			<div class="container">   	 				
				<h1 class="page-header">Figure 6.
					<div style='float:right'>
						<button class="btn btn-1 btn-1a network-control resetzoom" onclick="xlv.reset();">
							Reset
						</button>
						<!--
						<button class="btn btn-1 btn-1a network-control" onclick="xlv.exportSVG('networkSVG');">Export SVG</button>
						-->
						<label class="btn">
								Legend
								<input id="selection" onclick="toggleLegendPanel()" type="checkbox">
						</label>
						<label class="btn">
								Details
								<input id="selection" onclick="toggleInfoPanel()" type="checkbox">
						</label>
						<label class="btn">
								Help
								<input id="help" onclick="toggleHelpPanel()" type="checkbox">
						</label>
				</div>
				</h1>
                </div>
<!--
			<div class="long-citation" id="citation"></div> 				
-->
			<div id="networkContainer"></div>
			<?php include("filterBar.php"); ?>						
		</div> <!-- MAIN -->

		<script type="text/javascript">
                //<![CDATA[
                                 
                var targetDiv = document.getElementById('networkContainer');
                var messageDiv = document.getElementById('networkCaption');
                xlv = new xiNET.Controller(targetDiv);
                xlv.setMessageElement(messageDiv);
				d3.text('./data/NPC.csv', "text/csv", function(text) {
					xlv.readCSV(text);
					initSlider();
					changeAnnotations();
				});				
				
				function changeAnnotations(){
					var annotationSelect = document.getElementById('annotationsSelect');
					xlv.setAnnotations(annotationSelect.options[annotationSelect.selectedIndex].value);
				 };
				function initSlider(){
						if (xlv.scores === null){
							d3.select('#scoreSlider').style('display', 'none');
					}
					else {
						//no score
						document.getElementById('scoreSlider').setAttribute("style", "display:none;");
					}
				  };
                  
                //]]>
        </script>
	</body>
</html>
