<!DOCTYPE HTML>
<html>
	<head>
		<?php
		$pageName = "Home";
		include("head.php");
		include("xiSPEC_scripts.php");
		?>
	<script type="text/javascript">

	$( document ).ready(function() {

		var slideIndex = 0;
		function showDiv(n) {
			oldSlide = slideIndex;

			if(slideIndex+n >= $('.sliderImg').length)
				slideIndex = 0;
			else if(slideIndex+n < 0)
				slideIndex = $('.sliderImg').length - 1;
			else
				slideIndex += n;
			$('.sliderImg').eq(oldSlide).fadeOut(300, function(){ $('.sliderImg').eq(slideIndex).fadeIn(300); });
			$('#sliderInfo').text($('.sliderImg').eq(slideIndex).attr('alt'));
		}

		var myTimer = setInterval(function () {showDiv(1)}, 4000);

		$('.sliderImg').click(function(){
			clearInterval(myTimer);
		})

		$('.slider-left').click(function(){
			showDiv(-1);
			clearInterval(myTimer);
			// myTimer = setInterval(function () {showDiv(slideIndex+1)}, 4000);
		})

		$('.slider-right').click(function(){
			showDiv(1);
			clearInterval(myTimer);
			// myTimer = setInterval(function () {showDiv(slideIndex+1)}, 4000);
		})

	});

	</script>
	</head>

	<body>
		<!-- Sidebar -->
		<?php include("navigation.php");?>
		<!-- Main -->
		<div id="main">
			<div class="container">
				<h1 class="page-header">Visualizing mass spectrometry data.</h1>
				Citation: <a target="_blank" href="https://doi.org/10.1093/nar/gky353"> Lars Kolbowski, Colin Combe, Juri Rappsilber; xiSPEC: web-based visualization, analysis and sharing of proteomics data, Nucleic Acids Research, gky353</a></br>
				xiSPEC is an interactive tool for visualizing and analyzing mass spectrometry data.
				<ul>
					<li>Analyse mass spectra intuitively and test hypotheses.</li>
					<li>Upload and share whole MS datasets.</li>
					<li>Download annotated spectra in publication quality (vector graphic).</li>
			 </ul>
				<p>xiSPEC is an open source project on <a href="https://github.com/Rappsilber-Laboratory/xiSPEC" >GitHub</a>. Report issues and request features <a href="https://github.com/Rappsilber-Laboratory/xiSPEC/issues">here</a>.</p>
				<p>
									</p>
				<div style="text-align: center;">
					<div class="sliderWrapper">
					<img class="sliderImg" alt="Interactive highlighting between all views" src="images/slider/interactiveViews.png">
					<img class="sliderImg" alt="Measure distances between peaks" src="images/slider/measuringTool.png" style="display:none;">
					<img class="sliderImg" alt="Change spectrum annotation parameters" src="images/slider/settingsView.png" style="display:none;">
					<img class="sliderImg" alt="Zoom into spectra" src="images/slider/zoom.png" style="display:none;">
					<!-- <img class="sliderImg" alt="highlight spectrum" src="images/slider/5.png" style="display:none;"> -->
					<button class="sliderBtn slider-left">&#10094;</button>
					<button class="sliderBtn slider-right">&#10095;</button>
					<div id="sliderInfo">Interactive highlighting between all views</div>
					</div>
				</div>
			</div> <!-- CONTAINER -->
		</div> <!-- MAIN -->

	</body>
</html>
