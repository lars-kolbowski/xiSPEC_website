<?php
	$dir = 'sqlite:'.$_SERVER['DOCUMENT_ROOT'].'xiSPEC/dbs/xiSPEC.db';
	$xiSPECdb = new PDO($dir) or die("cannot open the database");
	$xiSPECdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
