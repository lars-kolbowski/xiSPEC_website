<!DOCTYPE HTML>
<html>
	<head>
		<title>xiSPEC CL | Cross-Link Spectrum Viewer</title>
	</head>
<body>
	<form action="userinput_to_json.php" method="post" target="_self">
		<label for="peptide1">Peptide 1</label> <input id="peptide1" required type="text" placeholder="Peptide 1 Sequence" name="pep1" value="TVTAMDVVYALK">
		<label for="clPos1">Cross-link Site 1</label> <input id="clPos1" required type="text" placeholder="cross-link site" name="clPos1" value="21"><br/>
		<label for="peptide2">Peptide 2</label> <input id="peptide2" required type="text" placeholder="Peptide 2 Sequence" name="pep2" value="YKAAFTECcmCcmQAADK">
		<label for="clPos2">Cross-link Site 2</label> <input id="clPos2" required type="text" placeholder="cross-link site" name="clPos2" value="1"><br/>
		<label for="peaklist">Peaklist</label> <textarea id="peaklist" required type="text" placeholder="Peak List" name="peaklist" style="white-space:pre-wrap;">
103.0543976	2709
110.8062744	1233
120.0808487	174918
121.0842285	14280
122.5711823	1276
123.2102127	1176
129.1019897	1470
133.0857391	1198
133.3751068	1212
142.0518036	1216
159.0762787	10138
163.9064789	1118
169.3084717	1180
175.1188812	8628
187.0711365	39262
219.1123657	4478
222.741394	1254
233.1646881	16423
233.8793793	1566
234.1685181	1679
261.1594543	7851
262.1503601	11127
274.1183472	1388
286.1405945	1924
289.1178589	32271
306.1451111	1474
316.1271057	1335
329.1820984	1419
332.1966858	1564
334.1391296	94422
335.1428223	15162
346.1384583	1492
357.1772766	2015
371.1844788	1243
377.2185364	1758
385.0169983	1806
391.1604309	4732
403.1605225	1572
405.2111206	1791
408.1873779	2210
417.2112122	1862
419.6461792	1512
425.2132874	16088
426.216156	1954
434.2652283	1373
447.2227173	7888
460.9031677	1757
476.2496643	1591
484.8515015	2636
485.8594055	1814
486.8449707	1732
501.9304199	4948
504.2440796	5395
506.7290649	1198
516.2424316	1479
554.2562866	21332
555.2589722	7137
556.7771606	1896
558.7721558	2519
571.0135498	8532
573.2640991	2832
591.2774048	2723
625.7081299	1612
626.7133179	2952
707.7803345	1657
717.3188477	38796
718.3218384	14246
719.3169556	1900
743.6900635	1850
830.4032593	31486
831.4036255	13366
870.8297729	1736
914.6835327	1642
977.4709473	28203
978.4760742	15785
979.4786377	2648
1064.505371	37306
1065.507813	15692
1066.500488	4800
1103.516357	10255
1104.511353	7485
1113.360962	1722
1121.524902	150589
1122.527954	85519
1123.532104	18841
1124.530029	2064
1216.598633	2363
1217.596436	1972
1234.610107	44253
1235.612549	25040
1236.614502	8568
1311.518433	1579
1381.664429	2753
		</textarea><br/>
		<label for="preCharge">Precursor charge</label> <input id="preCharge" required type="text" placeholder="Precursor charge state" name="preCharge" value="4"><br/>
		<label for="fragMethod">Fragmentation Method</label> <select class="form-control" name="fragMethod">
			<option value="HCD">HCD</option>
			<option value="CID">CID</option>
			<option value="ETD">ETD</option>
			<option value="ETciD">ETciD</option>
			<option value="EThcD">EThcD</option>
		</select><br/>
		<label for="clModMass">Cross-linker modification mass</label> <input id="clModMass" required type="text" placeholder="cross-linker mod mass" name="clModMass"><br/>
		<label for="msTol">MS2 tolerance</label> <input id="msTol" required type="text" placeholder="ms2 ppm tolerance" name="ms2Tol" value="20">
		<select class="form-control" name="tolUnit">
			<option value="ppm">ppm</option>
			<option value="Da">Da</option>			
		</select><br/>
		<input type="submit" name="submit">
	</form>
</body>