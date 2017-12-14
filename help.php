<!DOCTYPE HTML>
<html>
	<head>
		<?php
		$pageName = "Help";
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
				<h1 class="page-header">Input data format</h1>
 					<ul>
   	 					<li><h5>Peptide Sequence</h5></li>
						<table class="myTable" style="max-width: 700px;">
							<thead>
								<tr>
									<th>What?</th>
									<th>How?</th>
									<th>Example</th>
								</tr>
							</thead>
							<tr>
								<td>amino acids</td>
								<td>upper case one letter code</td>
								<td>ARNDCEQGHILKMFPSTWYV</td>
							</tr>
							<tr>
								<td>modifications</td>
								<td>lower case characters</td>
								<td>ox</td>
							</tr>
							<tr>
								<td>peptide delimiter</td>
								<td>;</td>
								<td rowspan="2" style="vertical-align: middle">K#LM;DAHK#SEVR</td>
							</tr>
							<tr>
								<td>cross-linked amino acid</td>
								<td>#</td>
							</tr>
						</table>
   	 					</p>
   	 					<br/>
   	 					<li><h5>Peaklist</h5></li>
   	 					<p>The peak list format accepted by xiSPEC is simple and easily extracted from most MS formats. Each line contains a pair of numbers: the first is a m/z value and the second is the corresponding intensity. The two numbers should be separated by either spaces or tabs.</p>
   	 					<p>
   	 						Example:
	   	 					<br/>104.31995	1077.574
							<br/>110.07077	1154.813
							<br/>116.98479	1330.395
							<br/>120.08031	6500.032
						</p>
					</ul>

					<h1 class="page-header">Features</h1>
					<ul >
						<li>
							<h5>Change cross-linker position</h5>
						</li>
						<p>
							<img src="images/gifs/changeCL.gif" />
							<ul style="margin-left:30px;">
								<li>Click on the cross-link line</li>
								<li>Move the mouse over the desired cross-linked amino acids.</li>
								<li>Click on the amino acid to confirm the position(s).</li>
								⇨ XiSPEC updates the annotated spectrum!
							</ul>
						</p>
						<br/>
						<li>
							<h5>Change modification position</h5>
						</li>
						<p>
							<img src="images/gifs/changeMod.gif" />
							<ul style="margin-left:30px;">
								<li>Click on the modification</li>
								<li>Move the mouse over the desired modified amino acids.</li>
								<li>Click on the amino acid to confirm the position.</li>
								⇨ XiSPEC updates the annotated spectrum!
							</ul>
						</p>
						<br/>
						<li>
							<h5>Highlight fragments</h5>
						</li>
						<p>
							<img src="images/gifs/highlighting.gif" />
							<ul style="margin-left:30px;">
								<li>Hover over fragments in the spectrum to highlight the corresponding fragment in the Sequence and vice versa.</li>
								<li>Click on a fragment to make the highlight permanent.</li>
								<li>Ctrl+Click to add additional fragments to selection.</li>
								<li>Click on the background to reset highlighting.</li>
							</ul>
						</p>
					</ul>
 			</div> <!-- CONTAINER -->
		</div> <!-- MAIN -->


	</body>
</html>
