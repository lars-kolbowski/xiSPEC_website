<!DOCTYPE HTML>
<html>
<head>
    <?php
    $pageName = "Home";
    include("head.php");
    ?>
</head>
	<body>
		<!-- Sidebar -->
		<?php include("navigation.php");?>
		<!-- Main -->
			<div id="main">
			
				<!-- Intro -->
					<section id="top" class="one">
						<div class="container">
							<h1 class="page-header">Key Features of xiNET</h1>
							   <div class="box1">
							   		<h4>Residue Level Positional Information</h4>
									<a class="image-link" title="Click here to view larger." href="images/diagrams/residue-level.svg">
										<img class="image residue-level" src="images/diagrams/residue-level.svg"/>
									</a>
							   		<p>Protein interaction networks are often represented as node-link diagrams in which the nodes represent whole biomolecules. xiNET additionally allows the circles representing whole proteins to be expanded into bars providing residue resolution. </p>
							   </div> <!-- BOX 1 -->

						</div> <!-- CONTAINER -->
					</section>
					<section class="two">
						<div class="container">
							   <div class="box2">
							   	  <h4>Simplifying Networks</h4>
								 <a class="image-link " title="Click here to view larger." href="images/diagrams/simplify-networks.svg">
									<img class="image simplify-networks" src="images/diagrams/simplify-networks.svg"/>
								  </a>
								  <p>Showing the entire cross-link network at residue resolution could quickly become over-whelming. The network can be simplified by selectively aggregating the data to protein level.</p>
							   </div> <!-- BOX 2 -->
						</div> <!-- CONTAINER -->
					</section>
					<section class="one">
						<div class="container">
							   <div class="box3">
							   		<h4>Ambiguous Linkage Sites</h4>
									<a class="image-link" title="Click here to view larger." href="images/diagrams/ambiguous-linkage.svg">
										<img class="image ambiguous-linkage" src="images/diagrams/ambiguous-linkage.svg"/>
									</a>
									<p>If one or both of the identified peptides occur in more than one protein then the linkage site at one or both ends of the cross-link is ambiguous. However, such cases may still contain valuable structural information. xiNET uses a dashed line to represent ambiguous linkage sites and the highlights on mouse-over show the ambiguous alternatives.</p>
							   </div> <!-- BOX 3 -->
						</div> <!-- CONTAINER -->
					</section>
					<section class="two">
						<div class="container">
							   <div class="box4">
							   	  <h4>Download Annotations</h4>
								<a class="image-link " title="Click here to view larger." href="images/diagrams/download-annotations.svg">
									<img class="image download-annotations" src="images/diagrams/download-annotations.svg"/>
								</a><div class="external-link">
								  <p>xiNET can automatically download annotations such as domains (<a  href="./demo2.php">Demo 2</a>). Custom manual annotations can also be shown (<a href="./demo1.php">Demo 1</a>)</p></div>
							   </div> <!-- BOX 4 -->

						</div> <!-- CONTAINER -->
					</section>
					<section class="one">
						<div class="container">
							   <div class="box5">
							   		<h4>Show Sequence</h4>
									<a class="image-link" title="Click here to view larger." href="images/diagrams/show-sequence.svg">
										<img class="image show-sequence" src="images/diagrams/other/show-sequence.png"/>
									</a>
									<p>Bars can be expanded to show more detail, until eventually the sequence is visible.</p>
							   </div> <!-- BOX 5 -->

						</div> <!-- CONTAINER -->
					</section>
					<section class="two">
						<div class="container">
							   <div class="box6">
							   		<h4>Interactive Filtering</h4>
									<a class="image-link" title="Click here to view larger." href="images/diagrams/interactive-filtering.svg">
										<img class="image interactive-filtering" src="images/diagrams/interactive-filtering.svg">
									</a>
									<p>Data can be filtered using a score cut-off slider, text search or other attributes. </p>
							   </div> <!-- BOX 6 -->

						</div> <!-- CONTAINER -->
					</section>
					<section class="one">
						<div class="container">
							   <div class="box7"><div class="external-link">
							   	 <h4>Runs in any modern browser without the need for plug-ins</h4>
								 <p>In keeping with other contemporary javascript libraries (e.g. 
								 <a target="_blank" href="https://github.com/mbostock/d3/wiki#browser-support">D3.js</a>), we define "any modern browser" such that it excludes Internet Explorer 8 and older. </p></div>
								 <!--<a href="images/diagram/" title="Click here to view larger."><img src="images/diagram/compatibility.png">-->
							   </div> <!-- BOX 7 -->
						</div> <!-- CONTAINER -->
					</section>
					<section class="two">
						<div class="container">
							   <div class="box8">
							   			<h4>Export to standard vector illustration packages</h4>
										<p>The output from xiNET is standards compliant 
										- it's SVG output can be edited in drawing packages such as Inkscape or Illustrator.
										This is useful for producing publication quality figures.</p>
							   </div> <!-- BOX 8 -->
						</div> <!-- CONTAINER -->
					</section>
					<section class="one">
						<div class="container">
							   <div class="box9">
							   			<div class="controlling-features-box">
											<h4>Controlling xiNET</h4>
											<table class="hor-minimalist-a"  bordercolor="#eee" >
												<!-- <thead>
																		<td>ACTION</td>
																		<td>ATTRIBUTE</td>
																	</thead> -->
							                    <tr>
							                        <td>Toggle the proteins between a bar and a circle</td>
							                        <td>Click on protein</td>
							                    </tr>
							                    <tr>
							                        <td>Zoom</td>
							                        <td>Mouse wheel</td>
							                    </tr>
							                    <tr>
							                        <td>Pan</td>
							                        <td>Click and drag on background</td>
							                    </tr>
							                    <tr>
							                        <td>Move protein</td>
							                        <td>Click and drag on protein</td>
							                    </tr>
							                    <tr>
							                        <td>Expand bar <br>(increases bar length until sequence is visible)</td>
							                        <td>Shift_left-click on protein</td>
							                    </tr>
							                    <tr>
							                        <td>Rotate bar</td>
							                        <td>Click and drag on handles that appear at end of bar</td>
							                    </tr>
							                    <tr>
							                        <td>Hide/show protein (and all links to it)</td>
							                        <td>Right-click on protein</td>
							                    </tr>
							                    <tr>
							                        <td>Hide/show links between two specific proteins</td>
							                        <td>Right click on any link between those proteins</td>
							                    </tr>
							                    <tr>
							                        <td>'Flip' side of bar on which self-links are shown</td>
							                        <td>Right-click on self-link</td>
							                    </tr>
							                </table>
										</div>
							   </div> <!-- BOX 9 -->
						</div> <!-- CONTAINER -->
					</section>
			</div> <!-- MAIN -->
	</body>
</html>