 <!DOCTYPE html>
<html>
    <head>
		<?php $pageName = "Figure 7";include("head.php");?>
		<?php include("xiNET_scripts.php");?>
		<link rel="stylesheet" href="css/noNav.css" />
    </head>
    <body>
				
	<!-- Slidey panels -->	
	<div class="overlay-box" id="infoPanel">
	<div id="networkCaption">
		<p>No selection.</p>
	</div>
	</div>

	<div class="overlay-box" id="helpPanel">
	<table class="overlay-table"  bordercolor="#eee" >
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
			<td>Hide links between two specific proteins</td>
			<td>Right click on any link between those proteins</td>
		</tr>
		<tr>
			<td>Show all hidden links</td>
			<td>Right click on background</td>
		</tr>
		<tr>
			<td>'Flip' self-links</td>
			<td>Right-click on self-link</td>
		</tr>
	</table> 
</div>	

<div class="overlay-box" id="legendPanel">
	<div><img src="./images/fig3_1.svg"></div>
</div>	

		<script type="text/javascript">
				//<![CDATA[
				helpShown = false;
				infoShown = false;
				legendShown = false;
				function toggleHelpPanel() {
					if (helpShown){
						hideHelpPanel();
					}
					else {
						showHelpPanel();
					}
				}
				
				function toggleInfoPanel() {
					if (infoShown){
						hideInfoPanel();
					}
					else {
						showInfoPanel();
					}
				}
				function toggleLegendPanel() {
					if (legendShown){
						hideLegendPanel();
					}
					else {
						showLegendPanel();
					}
				}
				
				function showHelpPanel() {
						helpShown = true;
						d3.select("#helpPanel").transition().style("height", "500px").style("top", "100px").duration(700);
				}
				function hideHelpPanel() {
						helpShown = false;
						d3.select("#helpPanel").transition().style("height", "0px").style("top", "-95px").duration(700);
				}
				function showInfoPanel() {
						infoShown = true;
						d3.select("#infoPanel").transition().style("height", "300px").style("bottom", "115px").duration(700);

				}
				function hideInfoPanel() {
						infoShown = false;
						d3.select("#infoPanel").transition().style("height", "0px").style("bottom", "-95px").duration(700);

				}
				function showLegendPanel() {
						legendShown = true;
						d3.select("#legendPanel").transition().style("height", "500px").style("top", "100px").duration(700);

				}
				function hideLegendPanel() {
						legendShown = false;
						d3.select("#legendPanel").transition().style("height", "0px").style("top", "-95px").duration(700);

				}
				//]]>
		</script>
		<!-- Main -->
		<div id="main">
			<div class="container">   	 				
				<h1 class="page-header">Figure 7.
					<div style='float:right'>
						<button class="btn btn-1 btn-1a network-control resetzoom" onclick="xlv.reset();">
							Reset
						</button>
						<!--
						<button class="btn btn-1 btn-1a network-control" onclick="xlv.exportSVG('networkSVG');">Export SVG</button>
						-->
						<label class="btn">
								Legend
								<input id="selection" onclick="toggleLegendPanel()" type="checkbox">
						</label>
						<label class="btn">
								Details
								<input id="selection" onclick="toggleInfoPanel()" type="checkbox">
						</label>
						<label class="btn">
								Help
								<input id="help" onclick="toggleHelpPanel()" type="checkbox">
						</label>
                </div>
				</h1>
			</div>
<!--
			<div class="long-citation" id="citation"></div> 				
-->
			<div id="networkContainer"></div>
			<?php include("filterBar.php"); ?>						
		</div> <!-- MAIN -->
				
		<script type="text/javascript">
                //<![CDATA[
	
	var targetDiv = document.getElementById('networkContainer');
	var messageDiv = document.getElementById('networkCaption');
	xlv = new xiNET.Controller(targetDiv);
	xlv.setMessageElement(messageDiv);

	
xlv.sid = "1559";//SELECT has_protein.peptide_id, has_protein.protein_id, (peptide_position + 1) as peptide_position INTO TEMPORARY tempHasProtein109_150_74_29_1393665870 FROM has_protein, tempMatchedPeptides109_150_74_29_1393665870 WHERE tempMatchedPeptides109_150_74_29_1393665870.peptide_id = has_protein.peptide_id GROUP BY  has_protein.peptide_id, has_protein.protein_id, peptide_position;
//SELECT t1.layout AS l  FROM layouts AS t1  LEFT OUTER JOIN layouts AS t2  ON (t1.search_id = t2.search_id AND t1.time &lt; t2.time)  WHERE t1.search_id = 1559 AND t2.search_id IS NULL;
xlv.addProtein (595291,'SKA2','MEAEVDKLELMFQKAESDLDYIQYRLEYEIKTNHPDSASEKNPVTLLKELSVIKSRYQTLYARFKPVAVEQKESKSRICcmATVKKTMNMIQKLQKQTDLELSPLTKEEKTAAEQFKFHMPDL','SKA2_HUMAN Spindle and kinetochore-associated protein 2 OS=Homo sapiens GN=SKA2 PE=1 SV=1','Q8WVK7','121');
xlv.addProtein (595292,'SKA1','MASSDLEQLCcmSHVNEKIGNIKKTLSLRNCcmGQEPTLKTVLNKIGDEIIVINELLNKLELEIQYQEQTNNSLKELCcmESLEEDYKDIEHLKENVPSHLPQVTVTQSCcmVKGSDLDPEEPIKVEEPEPVKKPPKEQRSIKEMPFITCcmDEFNGVPSYMKSRLTYNQINDVIKEINKAVISKYKILHQPKKSMNSVTRNLYHRFIDEETKDTKGRYFIVEADIKEFTTLKADKKFHVLLNILRHCcmRRLSEVRGGGLTRYVIT','SKA1_HUMAN Spindle and kinetochore-associated protein 1 OS=Homo sapiens GN=SKA1 PE=1 SV=1','Q96BD8','255');
xlv.addProtein (595295,'SKA3','MDPIRSFCcmGKLRSLASTLDCcmETARLQRALDGEESDFEDYPMRILYDLHSEVQTLKDDVNILLDKARLENQEGIDFIKATKVLMEKNSMDIMKIREYFQKYGYSPRVKKNSVHEQEAINSDPELSNCcmENFQKTDVKDDLSDPPVASSCcmISEKSPRSPQLSDFGLERYIVSQVLPNPPQAVNNYKEEPVIVTPPTKQSLVKVLKTPKCcmALKMDDFECcmVTPKLEHFGISEYTMCcmLNEDYTMGLKNARNNKSEEAIDTESRLNDNVFATPSPIIQQLEKSDAEYTNSPLVPTFCcmTPGLKIPSTKNSIALVSTNYPLSKTNSSSNDLEVEDRTSLVLNSDTCcmFENLTDPSSPTISSYENLLRTPTPPEVTKIPEDILQLLSKYNSNLATPIAIKAVPPSKRFLKHGQNIRDVSNKEN','SKA3_HUMAN Spindle and kinetochore-associated protein 3 OS=Homo sapiens GN=SKA3 PE=1 SV=2','Q8IX90','412');
xlv.addProtein (595296,'TBB2B','MREIVHIQAGQCcmGNQIGAKFWEVISDEHGIDPTGSYHGDSDLQLERINVYYNEATGNKYVPRAILVDLEPGTMDSVRSGPFGQIFRPDNFVFGQSGAGNNWAKGHYTEGAELVDSVLDVVRKESESCcmDCcmLQGFQLTHSLGGGTGSGMGTLLISKIREEYPDRIMNTFSVMPSPKVSDTVVEPYNATLSVHQLVENTDETYCcmIDNEALYDICcmFRTLKLTTPTYGDLNHLVSATMSGVTTCcmLRFPGQLNADLRKLAVNMVPFPRLHFFMPGFAPLTSRGSQQYRALTVPELTQQMFDSKNMMAACcmDPRHGRYLTVAAIFRGRMSMKEVDEQMLNVQNKNSSYFVEWIPNNVKTAVCcmDIPPRGLKMSATFIGNSTAIQELFKRISEQFTAMFRRKAFLHWYTGEGMDEMEFTEAESNMNDLVSEYQQYQDATADEQGEFEEEEGEDEA','TBB2B_BOVIN Tubulin beta-2B chain OS=Bos taurus GN=TUBB2B PE=1 SV=2','Q6B856','445');
xlv.addProtein (595297,'TBB3','MREIVHIQAGQCcmGNQIGAKFWEVISDEHGIDPSGNYVGDSDLQLERISVYYNEASSHKYVPRAILVDLEPGTMDSVRSGAFGHLFRPDNFIFGQSGAGNNWAKGHYTEGAELVDSVLDVVRKECcmENCcmDCcmLQGFQLTHSLGGGTGSGMGTLLISKVREEYPDRIMNTFSVVPSPKVSDTVVEPYNATLSIHQLVENTDETYCcmIDNEALYDICcmFRTLKLATPTYGDLNHLVSATMSGVTTSLRFPGQLNADLRKLAVNMVPFPRLHFFMPGFAPLTARGSQQYRALTVPELTQQMFDAKNMMAACcmDPRHGRYLTVATVFRGRMSMKEVDEQMLAIQSKNSSYFVEWIPNNVKVAVCcmDIPPRGLKMSSTFIGNSTAIQELFKRISEQFTAMFRRKAFLHWYTGEGMDEMEFTEAESNMNDLVSEYQQYQDATAEEEGEMYEDDEEESEAQGPK','ref|NP_001070595.1| tubulin beta-3 chain [Bos taurus]','116004471','450');
xlv.addProtein (595300,'TBA1B','MRECcmISIHVGQAGVQIGNACcmWELYCcmLEHGIQPDGQMPSDKTIGGGDDSFNTFFSETGAGKHVPRAVFVDLEPTVIDEVRTGTYRQLFHPEQLITGKEDAANNYARGHYTIGKEIIDLVLDRIRKLADQCcmTGLQGFLVFHSFGGGTGSGFTSLLMERLSVDYGKKSKLEFSIYPAPQVSTAVVEPYNSILTTHTTLEHSDCcmAFMVDNEAIYDICcmRRNLDIERPTYTNLNRLISQIVSSITASLRFDGALNVDLTEFQTNLVPYPRIHFPLATYAPVISAEKAYHEQLSVAEITNACcmFEPANQMVKCcmDPRHGKYMACcmCcmLLYRGDVVPKDVNAAIATIKTKRSIQFVDWCcmPTGFKVGINYQPPTVVPGGDLAKVQRAVCcmMLSNTTAIAEAWARLDHKFDLMYAKRAFVHWYVGEGMEEGEFSEAREDMAALEKDYEEVGVDSVEGEGEEEGEEY','TBA1B_BOVIN Tubulin alpha-1B chain OS=Bos taurus PE=1 SV=2','P81947','451')

xlv.addMatch("595292",[156],"595292",[156],105668554,16.121405005758536,8,2,"LTYNQINDVIK","LTYNQINDVIK",'t','A','f');
xlv.addMatch("595297,595296",[104,104],"595292",[197],105669540,14.076265317920171,8,10,"GHYTEGAELVDSVLDVVR","FIDEETKDTKGR",'t','A','f');
xlv.addMatch("595292",[197],"595300",[113],105667773,12.903735273568381,7,1,"FIDEETKDTK","EIIDLVLDR",'t','B','f');
xlv.addMatch("595297,595296",[104,104],"595292",[197],105669509,12.22013558952423,8,10,"GHYTEGAELVDSVLDVVR","FIDEETKDTKGR",'t','A','f');
xlv.addMatch("595300",[113],"595292",[224],105670922,12.10013651460346,1,3,"EIIDLVLDR","ADKK",'f','B','f');
xlv.addMatch("595292",[197],"595297,595296",[104,104],105669520,11.926373984387777,9,8,"FIDEETKDTKGR","GHYTEGAELVDSVLDVVR",'t','A','f');
xlv.addMatch("595292",[197],"595300",[113],105667774,11.802225877509864,7,1,"FIDEETKDTK","EIIDLVLDR",'t','B','f');
xlv.addMatch("595292",[197],"595300",[423],105667809,11.680432266427825,10,2,"FIDEETKDTKGR","EDMAALEK",'t','B','f');
xlv.addMatch("595300",[403],"595292",[224],105668841,11.64585088127479,12,3,"AFVHWYVGEGMEEGEFSEAR","ADKK",'f','C','f');
xlv.addMatch("595292",[178],"595300",[423],105666243,11.491635925753842,6,1,"ILHQPKK","EDMoxAALEK",'f','B','f');
xlv.addMatch("595297,595296",[104,104],"595292",[218],105669192,11.430114494006848,8,6,"GHYTEGAELVDSVLDVVR","EFTTLKADKK",'t','A','f');
xlv.addMatch("595292",[178],"595300",[423],105666233,11.426481090737836,6,1,"ILHQPKK","EDMAALEK",'t','B','f');
xlv.addMatch("595297,595296",[104,104],"595292",[197],105669522,11.406848913360593,8,10,"GHYTEGAELVDSVLDVVR","FIDEETKDTKGR",'t','B','f');
xlv.addMatch("595292",[197],"595300",[423],105667921,11.406067371371567,10,2,"FIDEETKDTKGR","EDMoxAALEK",'t','B','f');
xlv.addMatch("595292",[197],"595296",[155],105667766,11.348028344116848,6,7,"FIDEETKDTK","IREEYPDR",'t','B','f');
xlv.addMatch("595292",[197],"595300",[113],105667775,11.2500573628709,6,1,"FIDEETKDTK","EIIDLVLDR",'t','B','f');
xlv.addMatch("595297,595296",[104,104],"595292",[197],105669518,11.045325470961629,8,7,"GHYTEGAELVDSVLDVVR","FIDEETKDTKGR",'t','B','f');
xlv.addMatch("595297,595296",[104,104],"595292",[197],105669233,10.95172511791143,8,7,"GHYTEGAELVDSVLDVVR","FIDEETKDTK",'f','C','f');
xlv.addMatch("595300",[423],"595292",[224],105670658,10.761697766294466,1,3,"EDMAALEK","ADKK",'f','B','f');
xlv.addMatch("595292",[218],"595297",[155],105667670,10.626541790609112,6,3,"EFTTLKADKK","VREEYPDR",'t','C','f');
xlv.addMatch("595292",[197],"595296",[155],105668406,10.62261508457825,9,4,"FIDEETKDTKGR","IREEYPDR",'t','C','f');
xlv.addMatch("595295",[315],"595292",[178],105667797,10.584236115933296,9,6,"TNSSSNDLEVEDR","ILHQPKK",'t','B','f');
xlv.addMatch("595297,595296",[104,104],"595292",[224],105668101,10.570853696008065,11,3,"GHYTEGAELVDSVLDVVR","ADKK",'f','B','f');
xlv.addMatch("595292",[156],"595292",[72],105668691,10.55044940152342,8,5,"LTYNQINDVIK","ELCcmESLEEDYK",'t','C','f');
xlv.addMatch("595297,595296",[104,104],"595292",[218],105669184,10.51380646766718,8,3,"GHYTEGAELVDSVLDVVR","EFTTLKADKK",'t','C','f');
xlv.addMatch("595292",[126],"595300",[423],105666258,10.49356682076222,4,2,"KPPKEQR","EDMAALEK",'t','C','f');
xlv.addMatch("595297,595296",[104,104],"595292",[218],105669187,10.39994849719127,8,3,"GHYTEGAELVDSVLDVVR","EFTTLKADKK",'t','C','f');
xlv.addMatch("595297,595296",[104,104],"595292",[197],105669246,10.35493895985002,8,6,"GHYTEGAELVDSVLDVVR","FIDEETKDTK",'t','C','f');
xlv.addMatch("595292",[156],"595292",[72],105668694,10.277677752522198,8,5,"LTYNQINDVIK","ELCcmESLEEDYK",'t','C','f');
xlv.addMatch("595300",[403],"595292",[224],105668873,10.256431553631387,12,3,"AFVHWYVGEGMoxEEGEFSEAR","ADKK",'f','B','f');
xlv.addMatch("595297,595296",[104,104],"595292",[224],105668095,10.217242489477272,8,3,"GHYTEGAELVDSVLDVVR","ADKK",'f','C','f');
xlv.addMatch("595296",[155],"595292",[197],105668391,10.169977220933019,3,10,"IREEYPDR","FIDEETKDTKGR",'t','B','f');
xlv.addMatch("595292",[218],"595296,595297",[157,157],105666475,10.158287574164742,6,1,"EFTTLKADK","EEYPDR",'f','C','f');
xlv.addMatch("595292",[176],"595300",[423],105666783,10.103513823583429,1,1,"YKILHQPK","EDMAALEK",'f','C','f');
xlv.addMatch("595292",[197],"595296",[155],105667763,10.03843905882152,6,7,"FIDEETKDTK","IREEYPDR",'t','B','f');
xlv.addMatch("595292",[178],"595296,595297",[157,157],105666026,10.021738487905166,6,1,"ILHQPKK","EEYPDR",'f','C','f');
xlv.addMatch("595292",[218],"595296,595297",[157,157],105666483,9.924808867517354,6,1,"EFTTLKADK","EEYPDR",'f','C','f');
xlv.addMatch("595292",[184],"595300",[423],105666303,9.828962699488748,1,1,"KSMNSVTR","EDMAALEK",'f','C','f');
xlv.addMatch("595295",[67],"595291",[73],105666740,9.740161111290845,5,3,"LENQEGIDFIK","ESKSR",'f','C','f');
xlv.addMatch("595300",[423],"595292",[224],105670795,9.70297410049084,1,3,"EDMoxAALEK","ADKK",'f','C','f');
xlv.addMatch("595292",[197],"595296",[155],105667760,9.610680594011493,6,3,"FIDEETKDTK","IREEYPDR",'t','C','f');
xlv.addMatch("595300",[403],"595292",[224],105668844,9.556887519041261,12,3,"AFVHWYVGEGMEEGEFSEAR","ADKK",'f','C','f');
xlv.addMatch("595292",[197],"595300",[423],105667891,9.44237122348408,7,2,"FIDEETKDTKGR","EDMoxAALEK",'f','C','f');
xlv.addMatch("595297,595296",[104,104],"595292",[197],105669230,9.434826316321153,5,6,"GHYTEGAELVDSVLDVVR","FIDEETKDTK",'t','C','f');
xlv.addMatch("595297,595296",[104,104],"595292",[224],105668117,9.320787555642736,8,3,"GHYTEGAELVDSVLDVVR","ADKK",'f','C','f');
xlv.addMatch("595292",[184],"595300",[423],105666462,9.278578145557251,1,1,"KSMoxNSVTR","EDMAALEK",'f','C','f');
xlv.addMatch("595297,595296",[104,104],"595292",[197],105669531,9.097822998200634,4,4,"GHYTEGAELVDSVLDVVR","FIDEETKDTKGR",'t','C','f');
xlv.addMatch("595292",[184],"595296",[155],105667104,9.090787423075493,1,3,"KSMNSVTR","IREEYPDR",'t','B','f');
xlv.addMatch("595292",[218],"595296",[155],105667705,9.062354698595037,6,4,"EFTTLKADKK","IREEYPDR",'t','C','f');
xlv.addMatch("595295",[315],"595292",[178],105667799,8.79535441462789,9,6,"TNSSSNDLEVEDR","ILHQPKK",'t','C','f');
xlv.addMatch("595297,595296",[104,104],"595292",[218],105668969,8.746450882949478,8,6,"GHYTEGAELVDSVLDVVR","EFTTLKADK",'t','C','f');
xlv.addMatch("595292",[197],"595297",[155],105667726,8.693194958634171,6,4,"FIDEETKDTK","VREEYPDR",'t','C','f');
xlv.addMatch("595300",[113],"595292",[224],105665910,8.565203447880076,1,3,"EIIDLVLDR","ADKK",'f','C','f');
xlv.addMatch("595300",[403],"595292",[224],105668864,8.521009518493653,12,3,"AFVHWYVGEGMoxEEGEFSEAR","ADKK",'f','B','f');
xlv.addMatch("595297,595296",[104,104],"595292",[197],105669239,8.484268983631484,5,7,"GHYTEGAELVDSVLDVVR","FIDEETKDTK",'t','C','f');
xlv.addMatch("595292",[126],"595300",[423],105666248,8.479719011328626,4,1,"KPPKEQR","EDMAALEK",'t','C','f');
xlv.addMatch("595292",[184],"595300",[423],105666295,8.41690059342265,1,1,"KSMNSVTR","EDMAALEK",'f','C','f');
xlv.addMatch("595292",[184],"595297",[155],105667079,8.392882524734326,1,3,"KSMNSVTR","VREEYPDR",'t','B','f');
xlv.addMatch("595292",[197],"595296",[155],105667764,8.231068161692198,6,7,"FIDEETKDTK","IREEYPDR",'f','C','f');
xlv.addMatch("595296",[155],"595292",[184],105667129,8.08090281916901,4,1,"IREEYPDR","KSMoxNSVTR",'t','C','f');
xlv.addMatch("595295",[315],"595292",[178],105667798,7.867536336971613,12,6,"TNSSSNDLEVEDR","ILHQPKK",'f','C','f');
xlv.addMatch("595292",[197],"595296",[155],105667769,7.860233292172401,6,7,"FIDEETKDTK","IREEYPDR",'f','C','f');
xlv.addMatch("595292",[197],"595296",[155],105667770,7.829077135293102,6,4,"FIDEETKDTK","IREEYPDR",'f','C','f');
xlv.addMatch("595292",[184],"595300",[423],105666489,7.740979551865548,1,1,"KSMoxNSVTR","EDMoxAALEK",'f','C','f');
xlv.addMatch("595292",[197],"595300",[113],105668414,7.6620272752396925,10,1,"FIDEETKDTKGR","EIIDLVLDR",'t','C','f');
xlv.addMatch("595292",[197],"595297",[155],105667739,7.602036972505289,6,4,"FIDEETKDTK","VREEYPDR",'t','C','f');
xlv.addMatch("595292",[184],"595300",[423],105666457,7.122282069502989,2,2,"KSMoxNSVTR","EDMAALEK",'f','C','f');
    
				xlv.initLayout();            
				xlv.initProteins();
				initSlider();
				changeAnnotations();	
                
				function changeAnnotations(){
					var annotationSelect = document.getElementById('annotationsSelect');
					xlv.setAnnotations(annotationSelect.options[annotationSelect.selectedIndex].value);
				 };
				function initSlider(){
						if (xlv.scores === null){
							d3.select('#scoreSlider').style('display', 'none');
					}
					else {
							document.getElementById('scoreLabel1').innerHTML = "Score:" + getMinScore();
							document.getElementById('scoreLabel2').innerHTML = getMaxScore();
							sliderChanged();
							d3.select('#scoreSlider').style('display', 'inline-block');
					}
				  };
				
				//]]>
		</script>
	</body>
</html>
