<!DOCTYPE HTML>
<html>
	<head>
		<?php 
		$pageName = "Example 3";
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
					<div class="page-header center">
						<button class="btn btn-1 btn-1a network-control" onclick="toggleInfoPanel()">Selection Details</button>
						<button class="btn btn-1 btn-1a network-control" onclick="xlv.autoLayout();">Auto Layout</button>
						<button class="btn btn-1 btn-1a network-control resetzoom" onclick="xlv.resetZoom();">Reset Zoom</button>
						<button class="btn btn-1 btn-1a network-control" onclick="xlv.exportSVG('networkSVG');">Export</button>
						<button class="btn btn-1 btn-1a network-control" id="help" onclick="toggleHelpPanel()">Help</button>
						<!--<button class="btn btn-1 btn-1a network-control" onclick="window.location.href='upload.php'">Upload</button>-->
					</div>
					
                		<div class="controlsexamplespage skel-panels-fixed center shiftcontrol">
							
								<div class="controls-box-one">
									<div id="scoreSlider">
										<p class="scoreLabel" id="scoreLabel1"></p>
										<input id="slide" type="range" min="1" max="100" step="1" value="0" oninput="sliderChanged()">
										<p class="scoreLabel" id="scoreLabel2"></p>
									</div>
								</div>	
								<div class="controls-box-two">		
										<div id="scoreSlider">
											<p id="cutoffLabel">Cut-Off:</p>
										</div> <!-- outlined scoreSlider -->
								</div>
							<div class="controls-box-three">
									<label>
											Self-Links
					     			 		<input checked="checked" 
												   id="internal" 			
												   onclick="xlv.hideInternal(!document.getElementById('internal').checked)" 
												   type="checkbox"
											/>
					     	   		</label>
							</div>
					
 						</div> <!-- PAGE-HEADER-->
					<?php include("slideyPanels.php"); ?>
					
				</div><!-- container -->
			</section>

	<div id="networkContainer" class="skel-panels-fixed"></div>
	
	<div class="long-citation skel-panels-fixed">
		<div class="container">
			<p>Data from <a href="http://www.nature.com/emboj/journal/v29/n4/full/emboj2009401a.html" target="_blank">Abad, M.A., Medina, B., Santamaria, A., Zou, J., Plasberg-Hill, C., Madhumalar, A.,
Jayachandran, U., Redli, P.M., Rappsilber, J., Nigg, E.A., Jeyaprakash, A.A. (2014)
Structural basis for microtubule recognition by the human kinetochore Ska complex.
Nature Communications 5, 2964
</a>.</p>
		</div>
	</div>
		</div> <!-- MAIN -->
	  	<?php include('./xiNET_scripts.php'); ?>
	  	
	  	<script type="text/javascript">
	  	//<![CDATA[
	  	
	  	window.addEventListener('load', function() {
   	 				
	  	
	  	var targetDiv = document.getElementById('networkContainer');
	  	var messageDiv = document.getElementById('networkCaption');
	  	xlv = new xiNET.Controller(targetDiv);
	  	xlv.setMessageElement(messageDiv);
	  	
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
 	
 	document.getElementById('scoreLabel1').innerHTML = xlv.scores.min.toFixed(2);
	document.getElementById('scoreLabel2').innerHTML = xlv.scores.max.toFixed(2) + '&nbsp;&nbsp;';
	document.getElementById('cutoffLabel').innerHTML = 'Cut-off:&nbsp;' + xlv.scores.min.toFixed(2);
				   
    xlv.init();    
	  	xlv.autoLayout();
	  	new xiNET.DASUtil(xlv);
	  	//~ xlv.checkLinks();
	  	
	  	}, false);
	  	//]]>
	  	</script>
   	 			<script type="text/javascript">
                //<![CDATA[			
				function sliderChanged(){
					var slide = document.getElementById('slide');
					var cut = calcCutOff(slide.value / 100.0);
					var cutoffLabel = document.getElementById("cutoffLabel");
					cutoffLabel.innerHTML = 'Cut-off: ' + cut;
					xlv.setCutOff(cut);
				}
				
				function calcCutOff(v) {
					var result = (v * (xlv.scores.max - xlv.scores.min)) + xlv.scores.min;
					result = result.toFixed(2);
					return result;
				}				
				
                //]]>
        		</script>
	</body>
</html>
