<?php
	require_once 'main.php';

	$file_id = @$_GET['file_id'];

	if (empty($file_id) || !is_numeric($file_id)) bad_request();

	check_file_permission($file_id);

	$st = $pdo->prepare('SELECT file_name, file_mime_type, file_data FROM files WHERE file_id = ?');
	$st->execute([$file_id]);

	$row = $st->fetch();
	$st->closeCursor();
	if (!$row) not_found();

	$name = $row['file_name'];
	$mime = $row['file_mime_type'];
	$data = db_data($row['file_data']);

	header('Cache-Control: max-age=' . (360 * 24 * 60 * 60) . ', public');
	header('Expires: ' . ((new DateTime('+360 days'))->format(DateTime::RFC1123)));

	# header('Content-Type: application/octet-stream');
	header('Content-Type: ' . $mime);
	header('Content-Disposition: attachment; filename="' . $name . '"');
	header('Content-Description: File Transfer');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: ' . strlen($data));
	die($data);
?>