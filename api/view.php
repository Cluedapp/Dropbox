<?php
	require_once 'main.php';

	$file_id = @$_GET['file_id'];

	if (empty($file_id) || !is_numeric($file_id)) bad_request();

	check_file_permission($file_id);
	$st = $pdo->prepare('SELECT file_mime_type, file_data FROM files WHERE file_id = ?');
	$st->execute([$file_id]);
	$row = $st->fetch();
	$st->closeCursor();

	if (empty($row)) not_found();

	$mime = $row['file_mime_type'];
	header("Content-Type: $mime");;
	die(db_data($row['file_data']));
?>
