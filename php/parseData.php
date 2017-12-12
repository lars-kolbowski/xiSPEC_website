<?php

	if (session_status() === PHP_SESSION_NONE){session_start();}
	// unset($_SESSION['db']);
	$_SESSION['tmpDB'] = session_id();
	$target_dir = "../uploads/".session_id()."/";
	$mzid_file = $target_dir . $_POST['mzid_fn'];
	$mzml_file = $target_dir . $_POST['mzml_fn'];
	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser/';
	$command = escapeshellcmd($xiSPEC_ms_parser_dir.'python_env/bin/python '.$xiSPEC_ms_parser_dir.'parser.py '.$mzid_file.' '.$mzml_file. ' '.session_id());
	// die($command);
	// echo "<br/>";
	$output = shell_exec($command);
	echo $output;

?>
