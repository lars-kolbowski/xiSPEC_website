<?php

session_start();
unset($_SESSION['db']);

$target_dir = "../../uploads/".session_id()."/";
$mzid_file = $target_dir . $_POST['mzid_fn'];
$mzml_file = $target_dir . $_POST['mzml_fn'];
$command = escapeshellcmd('../python_env/bin/python -W ../py/parser.py '.$mzid_file.' '.$mzml_file. ' '.session_id());
//echo $command;
// echo "<br/>";
$output = shell_exec($command);
echo $output;

?>
