					<div class="controlsexamplespage">						
						<label>Self-Links
								<input checked="checked" 
									   id="internal" 			
									   onclick="xlv.hideInternal(!document.getElementById('internal').checked)" 
									   type="checkbox"
								/>
						</label>
						<label>Ambig.
								<input checked="checked" 
									   id="internal" 			
									   onclick="xlv.hideInternal(!document.getElementById('internal').checked)" 
									   type="checkbox"
								/>
						</label>
						<div id="scoreSlider">&nbsp;
							<p class="scoreLabel" id="scoreLabel1"></p>
							<input id="slide" type="range" min="1" max="100" step="1" value="0" oninput="sliderChanged()"/>
							&nbsp;<p class="scoreLabel" id="scoreLabel2"></p>
							<p id="cutoffLabel">&nbsp;Cut-Off:</p>
							
						</div> <!-- outlined scoreSlider -->
						<div style='float:right'>
							<label>Annot's:
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
							
							//not right
							
							function sliderChanged(){
								var slide = document.getElementById('slide');
								var cut = calcCutOff(slide.value / 100.0);
								var cutoffLabel = document.getElementById("cutoffLabel");
								cutoffLabel.innerHTML = '&nbsp;(' + cut + ')';
								xlv.setCutOff(cut);
							}
							
							function calcCutOff(v) {
								var result = (v * (xlv.scores.max - xlv.scores.min)) + xlv.scores.min;
								result = result.toFixed(2);
								return result;
							}			
							
							
												
							//]]>
					</script>
