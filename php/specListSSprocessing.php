<?php

	/*
	 * See http://datatables.net/usage/server-side for full details on the server-
	 * side processing requirements of DataTables.
	 *
	 * @license MIT - http://datatables.net/license_mit
	 */

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */

	// DB table to use
	$table = 'identifications';

	// Table's primary key - key for getting total results not real primary table key
	$primaryKey = 'mzid';

	// Array of database columns which should be read and sent back to DataTables.
	// The `db` parameter represents the column name in the database, while the `dt`
	// parameter represents the DataTables column identifier. In this case simple
	// indexes
	$columns = array(
		array( 'db' => 'MIN(id)',		'dt' => 'id', 'field' => 'id', 'as' => 'id'),
		array( 'db' => 'COUNT(id)',	'dt' => 'alt_count', 'field' => 'alt_count', 'as' => 'alt_count'),
		array( 'db' => 'mzid',			'dt' => 'mzid', 'field' => 'mzid', 'as' => 'mzid'),
		array( 'db' => 'pep1',			'dt' => 'pep1', 'field' => 'pep1', 'as' => 'pep1'),
		array( 'db' => 'pep2',			'dt' => 'pep2', 'field' => 'pep2', 'as' => 'pep2'),
		array( 'db' => 'linkpos1',	'dt' => 'linkpos1', 'field' => 'linkpos1', 'as' => 'linkpos1'),
		array( 'db' => 'linkpos2',	'dt' => 'linkpos2', 'field' => 'linkpos2', 'as' => 'linkpos2'),
		array( 'db' => 'charge',		'dt' => 'charge', 'field' => 'charge', 'as' => 'charge'),
		array( 'db' => 'isDecoy',		'dt' => 'isDecoy', 'field' => 'isDecoy', 'as' => 'isDecoy'),
		array( 'db' => 'score',		'dt' => 'score', 'field' => 'score', 'as' => 'score'),
		array( 'db' => 'allScores',		'dt' => 'allScores', 'field' => 'allScores', 'as' => 'allScores'),
		array( 'db' => 'protein1',	'dt' => 'protein1', 'field' => 'protein1', 'as' => 'protein1'),
		array( 'db' => 'protein2',	'dt' => 'protein2', 'field' => 'protein2', 'as' => 'protein2'),
		array( 'db' => 'passThreshold',	'dt' => 'passThreshold', 'field' => 'passThreshold', 'as' => 'passThreshold'),
		array( 'db' => 'file',	'dt' => 'file', 'field' => 'file', 'as' => 'file'),
		array( 'db' => 'scanID',		'dt' => 'scanID', 'field' => 'scanID', 'as' => 'scanID')
	);

	if (session_status() === PHP_SESSION_NONE){session_start();}

	if (isset($_SESSION['tmpDB'])){
		$dbname = "tmp/".session_id();
	}
	else {
		$dbname = "saved/".$_GET['db'];
	}

	if(!in_array($_GET['db'], $_SESSION['access'])){
		$json['error'] = "Authentication error occured!";
		die(json_encode($json));
	}

	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser';
	$sql_details = "sqlite:$xiSPEC_ms_parser_dir/dbs/$dbname.db";

	require( 'ssp.customizedClass.php' );

	$groupBy = "GROUP BY `mzid`";
	$extraWhere = "rank = 1";
	$json = json_encode(
	    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, NULL, $extraWhere, $groupBy )
	);

	echo $json;

?>
