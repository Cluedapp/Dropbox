<?php
	require_once 'main.php';

	$file_id = @$_REQUEST['file_id'];
	$file_name = @$_REQUEST['name'];
	$folder_id = @$_REQUEST['folder_id'];
	$data = @$_POST['data'];

	if (!(empty($file_id) || is_numeric($file_id))) bad_request();

	# Get file's existing details
	if (!empty($file_id)) {
		check_file_permission($file_id);

		$st = $pdo->query("SELECT file_name, folder_id FROM files WHERE file_id = $file_id");
		$row = $st->fetch();
		if (!$row) not_found();
		$file_name = $row['file_name'];
		if (empty($folder_id)) $folder_id = $row['folder_id'];
	} else if (!empty($folder_id)) {
		check_folder_permission($folder_id);
	}

	$file_name = empty($file_name) ? 'new_file.txt' : $file_name;

	if (!empty($_POST['submit']) && isset($_POST['data'])) {
		if (empty($file_id)) {
			$sql = "INSERT INTO files (file_name, file_mime_type, file_date, folder_id, file_data) VALUES (:file_name, 'text/plain', CURRENT_TIMESTAMP, :folder_id, :file_data)";
			$st = $pdo->prepare($sql);
			$st->bindParam(':file_name', $file_name);
			$st->bindParam(':folder_id', $folder_id);
			$st->bindParam(':file_data', $data, PDO::PARAM_LOB);
			$st->execute();
			if (!db_has_error($st)) $file_id = $pdo->lastInsertId('files_file_id_seq');
		} else {
			$sql = "UPDATE files SET file_name = :file_name, file_mime_type = 'text/plain', file_date = CURRENT_TIMESTAMP, folder_id = :folder_id" . (empty($data) ? '' : ', file_data = :file_data') . " WHERE file_id = $file_id";
			$st = $pdo->prepare($sql);
			$st->bindParam(':file_name', $file_name);
			$st->bindParam(':folder_id', $folder_id);
			if (!empty($data)) $st->bindParam(':file_data', $data, PDO::PARAM_LOB);
			$st->execute();
			if (db_has_error($st)) internal_server_error();
			ok();
		}
		
		if (!$st->rowCount()) bad_request();
	} else if (!empty($file_id)) {
		$st = $pdo->query("SELECT file_data FROM files WHERE file_id = $file_id");
		$row = $st->fetch();
		$st->closeCursor();
		if ($row) $data = db_data($row['file_data']);
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/style.css">
	</head>
	<body>
		<form method="post" <?php echo empty($file_id) ? '' : ' target="my_iframe"'; ?>>
			<input type="hidden" name="submit" value="1" />
			<input type="hidden" name="file_id" value="<?php echo $file_id; ?>" />
			<p><input type="submit" value="Upload" /></p>
			<p>Name:</p><p><input name="name" value="<?php echo htmlspecialchars($file_name); ?>" /></p>
			<p>Folder:</p><p><select name="folder_id">
			<?php
				foreach (get_folders() as $folder) {
					echo '<option value="', $folder[0], '"', ($folder_id == $folder[0] ? ' selected="selected"' : ''), '>', htmlspecialchars($folder[1]), '</option>';
				}
			?>
			</select>
			<p>Text:</p><p><textarea name="data" rows="50" cols="40"><?php echo htmlspecialchars($data); ?></textarea></p>
		</form>
		<iframe name="my_iframe" style="width: 50px; height: 50px"></iframe>
	</body>
</html>
