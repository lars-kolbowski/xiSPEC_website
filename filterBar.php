					<div class="controlsexamplespage">						
						<div id="scoreSlider">
							<p id="cutoffLabel">Cut-Off:</p>
							<p class="scoreLabel" id="scoreLabel1"></p>&nbsp;
							<input id="slide" type="range" min="1" max="100" step="1" value="0" oninput="sliderChanged()"/>
							&nbsp;<p class="scoreLabel" id="scoreLabel2"></p>
						</div>
						<div id="scoreSlider">
						</div> <!-- outlined scoreSlider -->
						<label>Self-Links
								<input checked="checked" 
									   id="internal" 			
									   onclick="xlv.hideInternal(!document.getElementById('internal').checked)" 
									   type="checkbox"
								/>
						</label>
						<label>Ambiguous
								<input checked="checked" 
									   id="internal" 			
									   onclick="xlv.hideInternal(!document.getElementById('internal').checked)" 
									   type="checkbox"
								/>
						</label>
						<div style='float:right'>
							<label>
							<select id="annotationsSelect" onChange="changeAnnotations();">
								<option selected='selected'>Custom</option> 
								<option>UniprotKB</option> 
								<option>SuperFamily</option>  
								<option>None</option>  
							</select>
							</label>	
						</div>
			
					</div>
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
