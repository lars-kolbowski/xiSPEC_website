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
			<td>Hide/show links between two specific proteins</td>
			<td>Right click on any link between those proteins</td>
		</tr>
		<tr>
			<td>'Flip' side of bar on which self-links are shown</td>
			<td>Right-click on self-link</td>
		</tr>
	</table> 
</div>	

<script type="text/javascript">
				//<![CDATA[
				helpShown = false;
				infoShown = false;
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
				
				function showHelpPanel() {
						helpShown = true;
						d3.select("#helpPanel").transition().style("height", "500px").style("top", "202px").duration(700);
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
				//]]>
</script>
