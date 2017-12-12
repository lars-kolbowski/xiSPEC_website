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
				slideIndex = $('.sliderImg').length-1;
			else
				slideIndex += n;
			$('.sliderImg').eq(oldSlide).fadeOut(300, function(){ $('.sliderImg').eq(slideIndex).fadeIn(300); });
			$('#sliderInfo').text($('.sliderImg').eq(slideIndex).attr('alt'));
		}

		var myTimer = setInterval(function () {showDiv(slideIndex+1)}, 4000);

		$('.slider-left').click(function(){
			showDiv(-1);
			clearInterval(myTimer);
			myTimer = setInterval(function () {showDiv(slideIndex+1)}, 4000);
		})

		$('.slider-right').click(function(){
			showDiv(1);
			clearInterval(myTimer);
			myTimer = setInterval(function () {showDiv(slideIndex+1)}, 4000);
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
				<p>xiSPEC is an interactive tool for visualizing and analyzing mass spectrometry data. It features automated spectra annotation with intuitive tools for data analysis and hypothesis testing.</p>
				<p>xiSPEC allows you to upload whole MS datasets (<a href="http://www.psidev.info/mzidentml" target="blank">mzIdentML</a> & <a href="http://www.psidev.info/mzml" target="blank">mzML</a>/mgf pair) and save them for later access (share with colleagues / online access for publication). Additionally it also allows downloading high quality vector graphics (.svg format) of your spectra for use in publications.</p>
				<p>xiSPEC is an open source project on <a href="https://github.com/Rappsilber-Laboratory/xiSPEC" >GitHub</a>. You can report issues and request features  <a href="https://github.com/Rappsilber-Laboratory/xiSPEC/issues">here</a></p>
				<div style="text-align: center;">
					<div class="sliderWrapper">
					<img class="sliderImg" alt="Interactive highlighting between all views" src="images/slider/dbView.png">
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
