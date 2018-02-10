<!DOCTYPE HTML>
<html>
	<head>
		<?php
		$pageName = "Examples";
		include("head.php");
		include("xiSPEC_scripts.php");
		?>
	</head>

	<body>
   	 	<!-- Sidebar -->
   	 	<?php include("navigation.php");?>

   	 	<!-- Main -->
   	 	<div id="main">

			<div class="container">

				<h1 class="page-header"> Data Upload example - (identification & peak list file pair)</h1>
				<a href="viewSpectrum.php?db=HSA-BS3_example">HSA-BS3 cross-link example dataset</a>

				<h1 class="page-header"> PRIDE example </h1>
				<a href="upload.php?ex=pxd">PXD005654</a>

				<h1 class="page-header">Manual data input examples</h1>
				<a href="upload.php?ex=lin">linear peptide example</a></br>
				<a href="upload.php?ex=cl">cross-linked peptide example</a>
 			</div> <!-- CONTAINER -->
		</div> <!-- MAIN -->


	</body>
</html>
