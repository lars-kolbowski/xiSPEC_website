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
	$table = 'spectrum_identifications';

	// Table's primary key - key for getting total results not real primary table key
	$primaryKey = 'spectrum_id';

	// Array of database columns which should be read and sent back to DataTables.
	// The `db` parameter represents the column name in the database, while the `dt`
	// parameter represents the DataTables column identifier. In this case simple
	// indexes

	$columns = array(
		array( 'db' => 'si.id',		'dt' => 'identification_id', 'field' => 'identification_id', 'as' => 'identification_id'),
		array( 'db' => 'sid_count.counted',	'dt' => 'alt_count', 'field' => 'alt_count', 'as' => 'alt_count'),
		array( 'db' => 'sp.spectrum_ref',			'dt' => 'spectrum_ref', 'field' => 'spectrum_ref', 'as' => 'spectrum_ref'),
		array( 'db' => 'pep1_table.seq_mods',			'dt' => 'pep1', 'field' => 'pep1', 'as' => 'pep1'),
		array( 'db' => 'pep2_table.seq_mods',			'dt' => 'pep2', 'field' => 'pep2', 'as' => 'pep2'),
		array( 'db' => 'pep1_table.link_site',	'dt' => 'linkpos1', 'field' => 'linkpos1', 'as' => 'linkpos1'),
		array( 'db' => 'pep2_table.link_site',	'dt' => 'linkpos2', 'field' => 'linkpos2', 'as' => 'linkpos2'),
		array( 'db' => 'si.charge_state',		'dt' => 'charge', 'field' => 'charge', 'as' => 'charge'),
		array( 'db' => 'MAX(pep1_ev.decoy, COALESCE(pep2_ev.decoy, 0))',		'dt' => 'is_decoy', 'field' => 'is_decoy', 'as' => 'is_decoy'),
		array( 'db' => 'pep1_ev.decoy',		'dt' => 'decoy1', 'field' => 'decoy1', 'as' => 'decoy1'),
		array( 'db' => 'pep2_ev.decoy',		'dt' => 'decoy2', 'field' => 'decoy2', 'as' => 'decoy2'),
		array( 'db' => 'atom',		'dt' => 'score', 'field' => 'score', 'as' => 'score'),
		array( 'db' => 'si.scores',		'dt' => 'scores', 'field' => 'scores', 'as' => 'scores'),
		array( 'db' => 'pep1_ev.protein',	'dt' => 'protein1', 'field' => 'protein1', 'as' => 'protein1'),
		array( 'db' => 'pep2_ev.protein',	'dt' => 'protein2', 'field' => 'protein2', 'as' => 'protein2'),
		array( 'db' => 'si.pass_threshold',	'dt' => 'pass_threshold', 'field' => 'pass_threshold', 'as' => 'pass_threshold'),
		array( 'db' => 'sp.peak_list_file_name',	'dt' => 'file', 'field' => 'file', 'as' => 'file'),
		array( 'db' => 'sp.scan_id',		'dt' => 'scan_id', 'field' => 'scan_id', 'as' => 'scan_id')
	);

	if (session_status() === PHP_SESSION_NONE){session_start();}


	if ($_GET['tmp'] == '1'){
		$dbname = "tmp/".$_GET['db'];
	}
	elseif (isset($_GET['db'])){
		$dbname = "saved/".$_GET['db'];
	}
	else {
		die();
	}

	//check authentication
	if(!isset($_SESSION['access'])) $_SESSION['access'] = array();
	if(!in_array($_GET['db'], $_SESSION['access'])){
		//if no valid authentication re-test authentication
		//this includes a connection string to the sql database
		require('../xiSPEC_sql_conn.php');
		require('checkAuth.php');
	}
	// re-check authentication
	if(!in_array($_GET['db'], $_SESSION['access'])){
		$json['error'] = "Authentication error occured!";
		die(json_encode($json));
	}

	$xiSPEC_ms_parser_dir = '../../xiSPEC_ms_parser';
	$sql_details = "sqlite:$xiSPEC_ms_parser_dir/dbs/$dbname.db";

	require( 'ssp.customizedClass.php' );

	$jsonCol = 'scores';

	// $groupBy = "GROUP BY `sid`";
	$joinQuery = "FROM $table AS si, json_each(si.$jsonCol)
	LEFT JOIN spectra AS sp ON (si.spectrum_id = sp.id)
	LEFT JOIN peptides AS pep1_table ON (si.pep1_id = pep1_table.id)
	LEFT JOIN
	  (SELECT peptide_ref, group_concat(DISTINCT protein_accession) AS protein, MAX(is_decoy) AS decoy
	  FROM peptide_evidences GROUP BY peptide_ref)
	AS pep1_ev ON (si.pep1_id = pep1_ev.peptide_ref)
	LEFT JOIN peptides AS pep2_table ON (si.pep2_id = pep2_table.id)
	LEFT JOIN
	  (SELECT peptide_ref, group_concat(DISTINCT protein_accession) AS protein, MAX(is_decoy) AS decoy
	  FROM peptide_evidences GROUP BY peptide_ref)
	AS pep2_ev ON (si.pep2_id = pep2_ev.peptide_ref)
	LEFT JOIN (SELECT spectrum_id, COUNT() AS counted FROM $table GROUP BY spectrum_id) AS sid_count ON si.spectrum_id = sid_count.spectrum_id
	";

	$groupBy = "GROUP BY si.spectrum_id, json_each.id HAVING MIN(si.rowid)";

	if (isset($_GET['scol']))
		$extraWhere = "json_each.key == '".$_GET['scol']."' AND si.rank = 1";
	else
		$extraWhere = "json_each.id == 2 AND si.rank = 1"; // id=2 is the first key in a json (could change in future json1 versions)

	$json = json_encode(
	    SSP::simple(
			$_GET,
			$sql_details,
			$table,
			$primaryKey,
			$columns,
			$joinQuery,
			$extraWhere,
			$groupBy
		)
	);
	echo $json;

?>
