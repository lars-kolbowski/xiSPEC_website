<!DOCTYPE HTML>
<html>
	<head>
		<?php
		$pageName = "Help";
		include("head.php");
		include("xiSPEC_scripts.php");
		?>
		<script type="text/javascript" src="/js/accordion.js"></script>
		<script type="text/javascript" src="/js/directURL.js"></script>
	</head>

	<body>
   	 	<!-- Sidebar -->
   	 	<?php include("navigation.php");?>

   	 	<!-- Main -->
   	 	<div id="main">
			<div class="container">
				<h1 class="page-header accordionHead">
					<i class="fa fa-plus-square" aria-hidden="true"></i> xiSPEC Feature support
				</h1>
				<div class="accordionContent" style="display: none;" id="features">
					<ul>
						<li><a href="#Hsave_db">Saving a dataset</a></li>
						<li><a href="#Hzoom">Zooming</a></li>
						<li><a href="#Hmeasure">Measure distances</a></li>
						<li><a href="#HmoveLabels">Move labels</a></li>
						<li><a href="#HclChange">Change cross-linker position</a></li>
						<li><a href="#HmodChange">Change modification position</a></li>
						<li><a href="#Hhighlight">Highlight fragments</h5></a></li>
					</ul>
					</br>
					<ul>
						<li>
							<h5 id="Hsave_db">Saving a dataset <a href="#" title="back to top"><i class="fa fa-arrow-up"></i></a></h5>
						</li>
						<p>
							<img src="/images/gifs/saving.gif" />
							<ul style="margin-left:30px;">
								<li>After data processing you can view your data as a temporary dataset</li>
								<li>To save your dataset simply click on the save icon</li>
								<li>Fill out the form and click save</li>
								⇨ You will be redirected to your saved dataset!
							</ul>
						</p>
						<br/>
						<li>
							<h5 id="Hzoom">Zooming <a href="#" title="back to top"><i class="fa fa-arrow-up"></i></a></h5>
						</li>
						<p>
							<img src="/images/gifs/zoom.gif" />
							<ul style="margin-left:30px;">
								<li>Click and drag the cursor below the x-axis to zoom into a specific area of the spectrum.</li>
								<li>Alternatively you can zoom in/out using the mouse wheel.</li>
								⇨ xiSPEC updates the annotated spectrum!
							</ul>
						</p>
						<br/>
						<li>
							<h5 id="Hmeasure">Measure distances<a href="#" title="back to top"><i class="fa fa-arrow-up"></i></a></h5>
						</li>
						<p>
							<img src="/images/gifs/measure.gif" />
							<ul style="margin-left:30px;">
								<li>Activate measuring mode by checking the 'Measure' option.</li>
								<li>Your cursor will change to a crosshair.</li>
								<li>Click and drag in the spectrum to measure distances between peaks.</li>
								⇨ xiSPEC displays potential amino acid residue hits in the tooltip!
							</ul>
						</p>
						<br/>

						<li>
							<h5 id="HmoveLabels">Move labels<a href="#" title="back to top"><i class="fa fa-arrow-up"></i></a></h5>
						</li>
						<p>
							<img src="/images/gifs/moveLabels.gif" />
							<ul style="margin-left:30px;">
								<li>Activate moveable labels by checking the 'Move labels' option.</li>
								<li>Click and drag labels to move them around.</li>
								⇨ xiSPEC draws dashed lines to the corresponding peaks!
							</ul>
						</p>
						<br/>

						<li>
							<h5 id="HclChange">Change cross-linker position <a href="#" title="back to top"><i class="fa fa-arrow-up"></i></a></h5>
						</li>
						<p>
							<img src="/images/gifs/changeCL.gif" />
							<ul style="margin-left:30px;">
								<li>Click on the cross-link line.</li>
								<li>Move the mouse over the desired cross-linked amino acids.</li>
								<li>Click on the amino acid to confirm the position(s).</li>
								⇨ xiSPEC updates the annotated spectrum!
							</ul>
						</p>
						<br/>
						<li>
							<h5 id="HmodChange">Change modification position <a href="#" title="back to top"><i class="fa fa-arrow-up"></i></a></h5>
						</li>
						<p>
							<img src="/images/gifs/changeMod.gif" />
							<ul style="margin-left:30px;">
								<li>Click on the modification.</li>
								<li>Move the mouse over the desired modified amino acids.</li>
								<li>Click on the amino acid to confirm the position.</li>
								⇨ xiSPEC updates the annotated spectrum!
							</ul>
						</p>
						<br/>
						<li>
							<h5 id="Hhighlight">Highlight fragments <a href="#" title="back to top"><i class="fa fa-arrow-up"></i></a></h5>
						</li>
						<p>
							<img src="/images/gifs/highlighting.gif" />
							<ul style="margin-left:30px;">
								<li>Hover over fragments in any view to highlight the corresponding fragment all views.</li>
								<li>Click on a fragment to make the highlight permanent.</li>
								<li>Ctrl+Click to add additional fragments to permanent selection.</li>
								<li>Click on the spectrum background to reset highlighting.</li>
							</ul>
						</p>
					</ul>
				</div>

				<h1 class="page-header accordionHead">
					<i class="fa fa-plus-square" aria-hidden="true"></i> Peptide identification csv column headings
				</h1>
				<div class="accordionContent" style="display: none;" id="csv">
					<table class="myTable" id="csvTable">
						<thead>
							<tr><th>column</th><th>required</th><th>default</th><th>example(s)</th></tr>
						</thead>
						<tbody>
							<tr><td>Id</td><td>Yes</td><td></td><td>SIR_1</td></tr>
							<tr><td>Rank</td><td>No*</td><td>1</td><td>1</td></tr>
							<tr><td>ScanNumber</td><td>Yes</td><td></td><td>2256</td></tr>
							<tr><td>Charge</td><td>Yes</td><td></td><td>3</td></tr>
							<tr><td>FragmentTolerance</td><td>No</td><td>10 ppm</td><td>10 ppm | 0.2 Da</td></tr>
							<tr><td>IonTypes</td><td>No</td><td>peptide;b;y</td><td>peptide;c;z</td></tr>
							<tr><td>PepSeq 1</td><td>Yes</td><td></td><td>LKECcmCcmEKPLLEK</td></tr>
							<tr><td>PepSeq 2</td><td>No**</td><td></td><td>HPYFYAPELLFFAKR</td></tr>
							<tr><td>LinkPos 1</td><td>No**</td><td></td><td>2</td></tr>
							<tr><td>LinkPos 2</td><td>No**</td><td></td><td>14</td></tr>
							<tr><td>CrossLinkerModMass</td><td>No**</td><td></td><td>138.06808</td></tr>
							<tr><td>PassThreshold</td><td>No</td><td>TRUE</td><td>TRUE | FALSE</td></tr>
							<tr><td>Score</td><td>No</td><td></td><td>10.5641</td></tr>
							<tr><td>IsDecoy</td><td>No</td><td>FALSE</td><td>TRUE | FALSE</td></tr>
							<tr><td>Protein 1</td><td>Yes</td><td></td><td>HSA</td></tr>
							<tr><td>Protein 2</td><td>No**</td><td></td><td>HSA</td></tr>
							<tr><td>RunName</td><td>No</td><td></td><td>example_file</td></tr>
						</tbody>
					</table>
					<p style="font-size: small;line-height: 1.5em;">
						*required if there are multiple alternative explanations for the same spectrum/id.</br>
						**required for cross-linked peptides.
					</p>
				</div>

				<h1 class="page-header accordionHead">
					<i class="fa fa-plus-square" aria-hidden="true"></i> Input data format for manual data input & re-annotation requests
				</h1>
				<div class="accordionContent" style="display: none;" id="syntax">
 					<ul>
   	 					<li><h5>Peptide Sequence</h5></li>
						<table class="myTable" style="max-width: 700px;">
							<thead>
								<tr>
									<th>What?</th>
									<th>How?</th>
									<th>Example(s)</th>
								</tr>
							</thead>
							<tr>
								<td>amino acid residues</td>
								<td>uppercase one letter code</td>
								<td>ARNDCEQGHILKMFPSTWYV</td>
							</tr>
							<tr>
								<td style="vertical-align:middle;">modifications</td>
								<td style="line-height:1.2em;">anything not uppercase<br />(following the residue)</td>
								<td style="vertical-align:middle;">Mox | Kbs3nh2 | K(+16)</td>
							</tr>
							<tr>
								<td>peptide delimiter</td>
								<td>;</td>
								<td rowspan="2" style="vertical-align: middle">K#LM;DAHK#SEVR</td>
							</tr>
							<tr>
								<td>cross-linked residue</td>
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
				</div>
 			</div> <!-- CONTAINER -->
		</div> <!-- MAIN -->


	</body>
</html>
