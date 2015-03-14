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
							<h1 class="page-header">Upload Your Own Data</h1>
							<form class="fileupload"  action="http://129.215.14.125/xiNET_web/crosslink-viewer/php/fup.php" enctype="multipart/form-data" method="POST">
							<table>
								<tr>
									
										<td>	
											<div class="cross-link-csv">
												<label for="csvFile">Cross-link CSV file:</label>
												<input style="margin: 0;padding: 0;" class="file btn btn-1 btn-1a-inputbtn" name="upfile" type="file" id="csvFile"/>
											</div> <!-- CROSS-LINK-CSV -->
										</td>
										<td>	
											<div class="fasta-file">
												<label for="fastaFile">FASTA file:</label>
												<input style="margin: 0;padding: 0;" class="file btn btn-1 btn-1a-inputbtn" name="upfasta" type="file" id="fastFile"/> 
											</div> <!-- FASTA-FILE -->
										</td>
										<td>	
											<div class="annotation-csv-file">
												<label for="annotFile">Annotation CSV file:</label>
												<input style="margin: 0;padding: 0;"  class="file btn btn-1 btn-1a-inputbtn" name="upannot" type="file" id="annotFile"/>
											</div> <!-- ANNOTATION-CSV-FILE -->
										</td>	
										
								</tr>
							  </table>
									<div class="custom_file_upload">
										<div class="file_upload btn btn-1 btn-1a-inverse">
											<input class="upload" value="Upload" type="submit"/>
									  	</div> <!-- CUSTOM_FILE_UPLOAD -->
									</div>
								<br>	
								<p class="center" style="margin-bottom:-40px;top:-50px;position:relative;">You will be redirected to a unique URL for your data which you can share with others.</p>				
			            		</form>
						
								<h4>Getting Started</h4>
								<a title="Click here to view larger." class="image-link" href="images/diagrams/workflow.svg"><img class="image full" src="images/diagrams/workflow.svg"></a>	<div class="external-link">
								<p>You can view your results by uploading <a href="#crosslinkCSV">cross-link data</a> in a Comma Separated Values (CSV) file. Optionally, this can be accompanied by a <a href="http://en.wikipedia.org/wiki/FASTA_format" target="_blank">FASTA file</a> giving the protein sequences and/or a CSV file containing <a href="#annotCSV">annotations</a>. If the FASTA file is omitted then protein sequences are retrieved by looking up accession numbers via the <a href="http://www.biodas.org/wiki/Main_Page" target="_blank">Distributed Annotation System</a>. This assumes that the sequences used in the search correspond exactly with those of valid, current UniprotKB accession numbers. 	</div></p>
						
							
							<div class="columnNames">
								<h4>Column Names in CSV files</h4>
								<ul style="list-style-type:square;">
									<li>Column names are required as the first line of the CSV file.</li>
									<li>Column names are case-sensitive.</li>
									<li>The order of the columns is unspecified.</li>
								</ul>
							</div>

				            <div class="protIds" >
								<h4>Protein Identifiers</h4>
								<h6>If a FASTA file is provided:</h6>
									
								<p>	then the protein identifiers 
									(columns 'Protein1' and 'Protein2') must match identifiers in the
									FASTA file. In a FASTA file, the word following the 
									"&gt;" symbol is the identifier of the sequence, 
									and the rest of the line is the description.</p>
									<div class="external-link">
								<h6>If a FASTA file is not provided:</h6> protein identifiers are  
									assumed to be six character <a href="http://www.uniprot.org/manual/accession_numbers">UniprotKB</a> accession numbers. SwissProt style identifiers of the format: <code>sp|accession|name</code> are also accepted and in this case 'name' will be used for the protein labels.</div>
								</p>
				            </div>
						</div> <!-- CONTAINER -->
					</section>
						<section class="two">
							<div class="container">
								<h3 id="crosslinkCSV">Cross-link CSV File format</h3>
<!--
								<div class="link-arrow">
-->
					            <p>Download example files: <a href="./crosslink-viewer/data/Pol2.csv" target="_blank">Pol II</a>, <a href="./crosslink-viewer/data/Herzog.csv" target="_blank">PP2A</a></p>
<!--
					            </div>
-->
					
					<p>xiNET can display data either with or without information on the sequences of the linked peptides. The fields PepSeq1, LinkPos1, PepSeq2 and LinkPos2 are only used when peptide sequence information is being supplied.</p>

					            <table width=642 cellpadding=7 cellspacing=0 class="hor-minimalist-a" style="border:1px solid #000;background-color:#eee;">
					                <col width=121>
					                <col width=88>
					                <col width=390>
					                <tr>
					                    <td width=121 height=3>
					                        <h6>COLUMN NAME</h6>
					                    </td>
					                    <td width=88>
					                        <h6>REQUIRED?</sh6>
					                    </td>
					                    <td width=390>
					                        <h6>NOTES</h6>
					                    </td>
					                </tr>
					                <tr>
					                    <td width=121 height=4>
					                        <p>Protein1</p>
					                    </td>
					                    <td width=88>
					                        <p>Yes</p>
					                    </td>
					                    <td width=390>
					                        <p>Identifier for protein 1</p>
					                    </td>
					                </tr>
					                <tr>
					                    <td width=121 height=4>
					                        <p>PepPos1</p>
					                    </td>
					                    <td width=88>
					                        <p>No</p>
					                    </td>
					                    <td width=390>
					                        <p>One-based residue number for peptide 1 start position in protein 1.</p>
					                    </td>
					                </tr>
					                <tr>
					                    <td>
					                        <p>PepSeq1</p>
					                    </td>
					                    <td>
					                        <p>No</p>
					                    </td>
					                    <td width=390>
					                        <p>Sequence for peptide 1,  lowercase characters ignored.</p>
					                    </td>
					                </tr>
					                
					                <tr>
					                    <td>
					                        <p>LinkPos1</p>
					                    </td>
					                    <td>
					                        <p>If PepSeq1 is present </p>
					                    </td>
					                    <td width=390>
					                        <p>One-based residue number for linkage site in peptide 1, or absolute position for
					                        link in Protein 1 if peptide position is ommitted.</p>
					                    </td>
					                </tr>
					                
					                <tr>
					                    <td width=121 height=4>
					                        <p>Protein2</p>
					                    </td>
					                    <td width=88>
					                        <p>See note</p>
					                    </td>
					                    <td width=390>
					                        <p>Identifier for protein 2. 
					This value is omitted for a linker modified peptide or an internally cross-linked peptide. 
					</p>
					                    </td>
					                </tr>
					                <tr>
					                    <td>
					                        <p>PepPos2</p>
					                    </td>
					                    <td>
					                        <p>No</p>
					                    </td>
					                    <td width=390>
					                        <p>One-based residue number for peptide 2 start position in protein 2.</p>
					                    </td>
					                </tr>


		
					                 <tr>
					                    <td>
					                        <p>PepSeq2</p>
					                    </td>
					                    <td>
					                        <p>No</p>
					                    </td>
					                    <td width=390>
					                        <p>Sequence for peptide 2,  lowercase characters ignored.</p>
					                    </td>
					                </tr>
					                <tr>
					                    <td>
					                        <p>LinkPos2</p>
					                    </td>
					                    <td>
					                        <p>If PepSeq2 is present </p>
					                    </td>
					                    <td width=390>
					                        <p>One-based residue number for linkage site in peptide 2, or absolute position for
					                        link in Protein 2 if peptide position is ommitted.</p>
					                        <p>Ommitted for linked modified peptides (mono-links).</p>
					                    </td>
					                </tr>              

					                <tr>
					                    <td width=121 height=4>
					                        <p>Score</p>
					                    </td>
					                    <td width=88>
					                        <p>No
					                        </p>
					                    </td>
					                    <td width=390>
					                        <p>Confidence score – used by cut-off slider.</p>
					                    </td>
					                </tr>
					                <tr>
					                    <td width=121 height=4>
					                        <p>Id</p>
					                    </td>
					                    <td width=88>
					                        <p>No
					                        </p>
					                    </td>
					                    <td width=390>
					                        <p>Id for link</p>
					                    </td>
					                </tr>
					            </table>
							</div><!-- CONTAINER -->
						</section>
						<section class="one">
							<div class="container">
								
									<h4>Ambiguous Linkage Sites</h4>
								
									<p>Ambiguous links are represented by listing the alternative linkage sites separated 
									by commas or semi-colons in the protein and position fields. For example:</p>
									<table cellpadding=7 cellspacing=0 class="hor-minimalist-a" style="position:relative;border:1px solid #000;background-color:#eee;">
						            <tr >

										<td width=150>
											<p>Protein1</p>
										</td>
										<td width=150>
											<p>LinkPos1</p>
										</td>
										<td width=150>
											<p>Protein2</p>
										</td>
										<td width=124>
											<p>LinkPos2</p>
										</td>

						            </tr>
						            <tr>
						                <td width=150>
						                    <p>O43815; Q13033</p>
						                </td>
						                <td width=150>
						                    <p>89; 105</p>
						                </td>
						                <td width=150>
						                    <p>O43815; Q13033</p>
						                </td>
						                <td width=124>
						                    <p>96; 112</p>
						                </td>
						            </tr>
						        </table>
						        <p>would result in these four ambiguous links being displayed:</p> from O43815, residue 89	to O43815, residue 96 <br/> from O43815, residue 89	to Q13033, residue 112<br/> from Q13033, residue 105 to O43815, residue 96 <br/>from Q13033, residue 105 to Q13033, residue 112<br/></p>
						</section>
						<section class="two">
							<div class="container">
				
							<h4>Product types</h4>

							<p>The figures below show the representation of different product types.
							These are: (a) linker modified peptides; (b) internally linked peptides; and, (c) cross-linked peptides.
							The product type is indicated by the presence or absence of information for the second protein and second link position. 
							We also identify a subset of cross-linked peptides, (d), in which the peptides overlap in the protein sequence.
						    </p>
							<table class="productTypeFigs">
								<tr>
						   	<td><div class="productTypeFigLeft">
							  <h6><u>(a) Linker modified peptides</u></h6>
						      <a title="Click to view larger." href="images/diagrams/f4a.svg"><img class="image featured full" src="images/diagrams/f4a.svg"></img></a>
							  <p>&nbsp;</p>
						    </div></td>
						   <td> <div class="productTypeFigRight">
								<h6><u>(b) Internally linked peptides</u></h6>
								<a title="Click to view larger." href="images/diagrams/f4b.svg"><img class="image featured full" src="images/diagrams/f4b.svg"></img></a>
								<p>&nbsp;</p>
						    </div></td>
								</tr>
								<tr>
						  <td>  <div class="productTypeFigLeft">
								<h6><u>(c) Cross-linked peptides.</u></h6>
								<a title="Click to view larger." href="images/diagrams/f4c.svg"><img class="image featured full" src="images/diagrams/f4c.svg"></img></a>
								<p>&nbsp;</p>
							</div></td>
						  <td>  <div class="productTypeFigRight">
								<h6><u>(d) Homomultimers (cross-links in which peptide sequences overlap)</u></h6>
								<a title="Click to view larger." href="images/diagrams/f4d.svg"><img class="image featured full" src="images/diagrams/f4d.svg"></img></a>
								<p>&nbsp;</p>
							</div></td>
								</tr>
								</table>
							</div>
						</section>
						<section class="one">
							<div class="container">
								<h3 id="annotCSV">Annotations CSV File format</h3>
<!--
								<div class="link-arrow">
-->
					          	  	<p>Download example files: <a href="./crosslink-viewer/data/tfiif_annot.csv" target="_blank">TFIIF annotations</a></p>
<!--
 								</div>
-->

					            <table width=642 cellpadding=7 cellspacing=0 class="hor-minimalist-a" style="border:1px solid #000;background-color:#eee;">
					                <col width=121>
					                <col width=88>
					                <col width=390>
					                <tr>
					                    <td width=121 height=3>
					                        <h6>COLUMN NAME</h6>
					                    </td>
					                    <td width=88>
					                        <h6>REQUIRED?</sh6>
					                    </td>
					                    <td width=390>
					                        <h6>NOTES</h6>
					                    </td>
					                </tr>
					                <tr>
					                    <td width=121 height=4>
					                        <p>ProteinId</p>
					                    </td>
					                    <td width=88>
					                        <p>Yes</p>
					                    </td>
					                    <td width=390>
					                        <p>Identifier for protein to annotate.</p>
					                    </td>
					                </tr>               
					                <tr>
					                    <td width=121 height=4>
					                        <p>Name</p>
					                    </td>
					                    <td width=88>
					                        <p>Yes</p>
					                    </td>
					                    <td width=390>
					                        <p>Name of annotation.</p>
					                    </td>
					                </tr>               
					                <tr>
					                    <td width=121 height=4>
					                        <p>StartResidue</p>
					                    </td>
					                    <td width=88>
					                        <p>Yes</p>
					                    </td>
					 <!--                   <td width=390 rowspan=2>
					
					                        <p>If StartResidue and EndResidue are ommitted 
					                        then the annotation is assumed to be non-positional (i.e. a keyword): 
					                        the circle will be colored according to it but it will not be represented on the bar.  
											</p>

					                    </td>					-->
					                </tr>
									<tr>
					                    <td width=121 height=4>
					                        <p>EndResidue</p>
					                    </td>
					                    <td width=88>
					                        <p>Yes</p>
					                    </td>

					                </tr>
									<tr>
					                    <td width=121 height=4>
					                        <p>Color</p>
					                    </td>
					                    <td width=88>
					                        <p>No</p>
					                    </td>
					                    <td width=390 rows=2>
					                        <p>If omitted then a color is chosen automatically.
					                         In this case annotations with the same name are always assigned the same color.</p>
					                    </td>
					                </tr>

					            </table>
							</div> <!--CONTAINER-->
						</section>
			</div>
	</body>
</html>
