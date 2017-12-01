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
		$('.sliderImg').eq(oldSlide).fadeOut(300, function(){ $('.sliderImg').eq(slideIndex).fadeIn(300); });
	}

	$( document ).ready(function() {
		//showDiv(slideIndex);
		window.setInterval(function(){
			showDiv(slideIndex+1);
		}, 4000);
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
				<p>An interactive tool for visualizing and analyzing mass spectrometry data. XiSPEC features automated spectra annotation with intuitive tools for data analysis and hypothesis testing.</p>
				<p>xiSPEC allows you to upload whole MS datasets (<a href="http://www.psidev.info/mzidentml" target="blank">mzIdentML</a> & <a href="http://www.psidev.info/mzml" target="blank">mzML</a> pair) and save them for later access (share with colleagues / online access for publication).</p>
				<p>xiSPEC is an open source project on <a href="https://github.com/Rappsilber-Laboratory/xiSPEC" >GitHub</a>.</p>
				<div style="text-align: center;">
					<div class="sliderWrapper">
					<img class="sliderImg" alt="zoom spectrum" src="images/slider/zoom.png">
					<img class="sliderImg" alt="highlight spectrum" src="images/slider/highlight.png" style="display:none;">
					<button class="sliderBtn slider-left" onclick="showDiv(-1)">&#10094;</button>
					<button class="sliderBtn slider-right" onclick="showDiv(1)">&#10095;</button>
					</div>
				</div>
			</div> <!-- CONTAINER -->
		</div> <!-- MAIN -->

	</body>
</html>
