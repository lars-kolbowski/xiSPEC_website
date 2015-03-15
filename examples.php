<!DOCTYPE HTML>
<html>
	<head>
		<?php $pageName = "Examples";include("head.php");?>
		<?php include("xiNET_scripts.php");?>
	</head>	
	<body>
		<!-- Sidebar -->
		<?php include("navigation.php");?>
		<!-- Slidey panels -->	
		<?php include("slideyPanels.php"); ?>
		<!-- Main -->
		<div id="main">
			<div class="container">   	 				
				<h1 class="page-header">Example:&nbsp;
					<select class="btn btn-1 btn-1a" id="dataSets"  onchange="loadData();" >
<!--
						<option value="TFIIF">TFIIF</option>
-->
						<option selected value="PolII">PolII</option>
						<option value="PP2A">PP2A</option>
						<option value="NPC">NPC</option>
					</select>
					<div style='float:right'>
						<button class="btn btn-1 btn-1a network-control resetzoom" onclick="xlv.reset();">
							Reset
						</button>
						<!--
						<button class="btn btn-1 btn-1a network-control" onclick="xlv.exportSVG('networkSVG');">Export SVG</button>
						-->
						<label class="btn">
								Selection Details
								<input id="selection" onclick="toggleInfoPanel()" type="checkbox">
						</label>
						<label class="btn">
								Help
								<input id="help" onclick="toggleHelpPanel()" type="checkbox">
						</label>
					</div>
				</h1>
   	 		</div>				   	
			<div class="long-citation" id="citation"></div> 				
			<div id="networkContainer"></div>
			<?php include("filterBar.php"); ?>						
		</div> <!-- MAIN -->
	  	
	  	<script type="text/javascript">
	  	//<![CDATA[	  	
			var config = [				
				//~ {//TFIIF
					//~ file:"./data/TFIIF.csv",
					//~ cite:"<p>Data from <a href='http://www.nature.com/emboj/journal/v29/n4/full/emboj2009401a.html' target='_blank'>Chen ZA, Jawhari A, Fischer L, Buchen C, Tahir S, Kamenski T, Rasmussen M, Lariviere L, Bukowski-Wills J-C, Nilges M, Cramer P &amp; Rappsilber J (2010) Architecture of the RNA polymerase II–TFIIF complex revealed by cross-linking and mass spectrometry. The EMBO Journal 29: 717–726</a>.</p>"
				//~ },
				{//PolII
					file:"./data/PolII.csv",
					cite:"<p>Data from <a href='http://www.nature.com/emboj/journal/v29/n4/full/emboj2009401a.html' target='_blank'>Chen ZA, Jawhari A, Fischer L, Buchen C, Tahir S, Kamenski T, Rasmussen M, Lariviere L, Bukowski-Wills J-C, Nilges M, Cramer P &amp; Rappsilber J (2010) Architecture of the RNA polymerase II–TFIIF complex revealed by cross-linking and mass spectrometry. The EMBO Journal 29: 717–726</a>.</p>"
				},
				{//PP2A
					file:"./data/PP2A.csv",
					cite:"<p>Data from <a href='http://www.sciencemag.org/content/337/6100/1348' target='_blank'>Herzog, F., Kahraman, A., Boehringer, D., Mak, R., Bracher, A., Walzthoeni, T., Leitner, A., Beck, M., Hartl, F.-U., Ban, N., Malmstrom, L., and Aebersold, R. (2012) Structural Probing of a Protein Phosphatase 2A Network by Chemical Cross-Linking and Mass Spectrometry. Science 337, 1348–1352</a>.</p>"
				},
				{//NPC
					file:"./data/NPC.csv",
					cite:"<p>Data from <a href='http://www.sciencedirect.com/science/article/pii/S0092867413014165' target='_blank'>Bui, K. H., von Appen, A., DiGuilio, A. L., Ori, A., Sparks, L., Mackmull, M.-T., Bock, T., Hagen, W., Andrés-Pons, A., Glavy, J. S., and Beck, M. (2013) Integrated Structural Analysis of the Human Nuclear Pore Complex Scaffold. Cell 155, 1233–1243</a>.</p>"
				}				
			];
			
			function loadData(){
				var dataSetsSelect = document.getElementById('dataSets');
				var path = config[dataSetsSelect.selectedIndex].file;
				document.getElementById('citation').innerHTML = config[dataSetsSelect.selectedIndex].cite;
				d3.text(path, "text/csv", function(text) {
					xlv.clear();
					xlv.readCSV(text);
					
					if (xlv.scores === null){
						d3.select('#scoreSlider').style('display', 'none');
					}
					else {
						document.getElementById('scoreLabel1').innerHTML = getMinScore();
						document.getElementById('scoreLabel2').innerHTML = getMaxScore();
						sliderChanged();
						d3.select('#scoreSlider').style('display', 'inline-block');
					}
				});						
			}
			
			//~ window.addEventListener('load', function() {
				var targetDiv = document.getElementById('networkContainer');
                xlv = new xiNET.Controller(targetDiv);
                
			    var targetDiv = document.getElementById('networkContainer');
                var messageDiv = document.getElementById('networkCaption');
                xlv = new xiNET.Controller(targetDiv);
                xlv.setMessageElement(messageDiv);
				loadData();
                changeAnnotations();
			//~ }, false);
            	 
				 function changeAnnotations(){
					var annotationSelect = document.getElementById('annotationsSelect');
					xlv.setAnnotations(annotationSelect.options[annotationSelect.selectedIndex].value);
				 }
                  
                 
                  
                 //]]>
	  	</script>
	</body>
</html>
