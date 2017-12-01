<?php
	$dir = 'sqlite:dbs/xiSPEC.db';
	$xiSPECdb = new PDO($dir) or die("cannot open the database");
	$xiSPECdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
