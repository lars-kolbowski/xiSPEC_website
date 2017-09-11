<!DOCTYPE HTML>
<html>
	<head>
		<?php
		$pageName = "Home";
		include("head.php");
		include("xiSPEC_scripts.php");
		?>
	<script type="text/javascript">
	var slideIndex = 0;
	function showDiv(n) {
		oldSlide = slideIndex;

		if(slideIndex+n >= $('.sliderImg').length)
			slideIndex = 0;
		else if(slideIndex+n < 0)
			slideIndex = $('.sliderImg').length-1;
		else
			slideIndex += n;
		$('.sliderImg').eq(oldSlide).fadeOut(200, function(){ $('.sliderImg').eq(slideIndex).fadeIn(200); });
	}

	$( document ).ready(function() {
		showDiv(slideIndex);
		// window.setInterval(function(){
		// 	showDiv(slideIndex+1);
		// }, 2000);
	});

	</script>
	</head>

	<body>
   	 	<!-- Sidebar -->
   	 	<?php include("navigation.php");?>

   	 	<!-- Main -->
   	 	<div id="main">
			
   	 				<div class="container">
   	 					<h1 class="page-header">Visualizing (cross-link) mass spectrometry spectra.</h1>

   	 					<p>A tool for visualizing and analyzing your mass spectrometry data. XiSPEC features automated spectra annotation with intuitive tools for data analysis and hypothesis testing.</p>
   	 					<p>xiSPEC allows you to upload whole MS datasets (<a href="http://www.psidev.info/mzidentml" target="blank">mzIdentML</a> & <a href="http://www.psidev.info/mzml" target="blank">mzML</a> pair) and save them for later access (share with colleagues / online access for publication).</p>
						<div id="spectrumWrapper" style="width:100%;margin-top: 20px;position:relative;">
 							<img class="sliderImg" alt="example spectrum" src="example/example_spectrum.svg" style="max-width: 100%;">
 							<img class="sliderImg" alt="zoom spectrum" src="example/zoom.png" style="max-width: 100%;display:none;">
 							<img class="sliderImg" alt="zoom spectrum" src="example/highlight.png" style="max-width: 100%;display:none;">
							<button class="sliderBtn slider-left" onclick="showDiv(-1)">&#10094;</button>
							<button class="sliderBtn slider-right" onclick="showDiv(1)">&#10095;</button>
 						</div>
							
			 			</div> <!-- CONTAINER -->
		</div> <!-- MAIN -->
  	 				
   	 				
	</body>
</html>
