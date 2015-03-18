					<div class="controlsexamplespage">						
						<label>Self-Links
								<input checked="checked" 
									   id="selfLinks" 			
									   onclick="xlv.showSelfLinks(document.getElementById('selfLinks').checked)" 
									   type="checkbox"
								/>
						</label>
						<label>&nbsp;Ambig.
								<input checked="checked" 
									   id="ambig" 			
									   onclick="xlv.showAmbig(document.getElementById('ambig').checked)" 
									   type="checkbox"
								/>
						</label>
						<div id="scoreSlider">&nbsp;
							<p class="scoreLabel" id="scoreLabel1"></p>
							<input id="slide" type="range" min="0" max="100" step="1" value="0" oninput="sliderChanged()"/>
							&nbsp;<p class="scoreLabel" id="scoreLabel2"></p>
							<p id="cutoffLabel">(cut-off)</p>
							
						</div> <!-- outlined scoreSlider -->
						<div style='float:right'>
							<label>Annot.
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
							
							var sliderDecimalPlaces = 1;
							function getMinScore(){
								if (xlv.scores){
									var powerOfTen = Math.pow(10, sliderDecimalPlaces); 
									return (Math.floor(xlv.scores.min * powerOfTen) / powerOfTen)
											.toFixed(sliderDecimalPlaces);
								}
							}
							function getMaxScore(){
								if (xlv.scores){
									var powerOfTen = Math.pow(10, sliderDecimalPlaces); 
									return (Math.ceil(xlv.scores.max * powerOfTen) / powerOfTen)
											.toFixed(sliderDecimalPlaces);
								}
							}
							function sliderChanged(){
								var slide = document.getElementById('slide');
								var powerOfTen = Math.pow(10, sliderDecimalPlaces); 
								
								var cut = ((slide.value / 100) 
											* (getMaxScore() - getMinScore()))
											+ (getMinScore() / 1);
								cut = cut.toFixed(sliderDecimalPlaces);
								var cutoffLabel = document.getElementById("cutoffLabel");
								cutoffLabel.innerHTML = '(' + cut + ')';
								xlv.setCutOff(cut);
							}
							
							//]]>
					</script>
