<?php
	require_once 'main.php';

	$file_id = @$_GET['file_id'];

	if (empty($file_id) || !is_numeric($file_id)) bad_request();

	check_file_permission($file_id);

	$st = $pdo->prepare('DELETE FROM files WHERE file_id = ?');
	$st->execute([$file_id]);
	if (db_has_error($st) || !$st->rowCount()) {
		bad_request();
	} else {
		echo 'Delete successful';
	}
?>
