<?php
	try {
		$pdo = new PDO('pgsql:dbname=dropbox', 'dropbox', 'dropbox');
		# $pdo = new PDO('pgsql:host=localhost;port=5432;dbname=dropbox;user=dropbox;password=dropbox');
		if (!$pdo) internal_server_error();
	}
	catch (PDOException $e) {
		error_log($e->getMessage());
		if ($pdo) error_log(print_r($pdo->errorInfo(), true));
		internal_server_error();
	}

	function db_has_error(&$st) {
		return $st->errorCode() !== '00000';
	}

	function db_data(&$db_bytea_selected_column_ref) {
		return hex2bin(substr(stream_get_contents($db_bytea_selected_column_ref), 1));
	}
?>
