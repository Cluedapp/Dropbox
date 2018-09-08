<?php
	require_once 'main.php';

	$file_id = @$_REQUEST['file_id'];
	$folder_id = @$_REQUEST['folder_id'];

	# File ID must be empty or numeric, i.e. it must match ^[0-9]*$
	if (!(empty($file_id) || is_numeric($file_id))) bad_request();

	# Get file's folder ID
	if (empty($folder_id) && !empty($file_id)) {
		check_file_permission($file_id);

		$st = $pdo->prepare('SELECT folder_id FROM files WHERE file_id = ?');
		$st->execute([$file_id]);
		$row = $st->fetch();
		$st->closeCursor();
		if (!$row) not_found();
		$folder_id = $row['folder_id'];
	} else if (!empty($folder_id)) {
		check_folder_permission($folder_id);
	}

	$file = $_FILES['file'];
	if (!empty($_POST['submit'])) {
		if (!isset($file) || !file_exists($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) internal_server_error();

		$folder_id = $_POST['folder_id'];
		$description = $_POST['description'];
		$name = $file['name'];
		$mime = $file['type'];
		$data = file_get_contents($file['tmp_name']);

		if (empty($file_id)) {
			$sql = 'INSERT INTO files (file_name, file_mime_type, file_date, folder_id, file_description, file_data) VALUES (:file_name, :file_mime_type, CURRENT_TIMESTAMP, :folder_id, :file_description, :file_data)';
			$st = $pdo->prepare($sql);
			$st->bindParam(':file_name', $name);
			$st->bindParam(':file_mime_type', $mime);
			$st->bindParam(':folder_id', $folder_id);
			$st->bindParam(':file_description', $description);
			$st->bindParam(':file_data', $data, PDO::PARAM_LOB);
			$st->execute();
			if (!db_has_error($st)) $file_id = $pdo->lastInsertId('files_file_id_seq');
		} else {
			$sql = "UPDATE files SET file_name = :file_name, file_mime_type = :file_mime_type, file_date = CURRENT_TIMESTAMP, folder_id = :folder_id, file_description = :file_description, file_data = :file_data WHERE file_id = $file_id";
			$st = $pdo->prepare($sql);
			$st->bindParam(':file_name', $name);
			$st->bindParam(':file_mime_type', $mime);
			$st->bindParam(':folder_id', $folder_id);
			$st->bindParam(':file_description', $description);
			$st->bindParam(':file_data', $data, PDO::PARAM_LOB);
			$st->execute();
		}

		if (db_has_error($st)) {
			internal_server_error();
		} else {
			$message = '<a href="/file/' . $file_id . '">Download</a>';
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/style.css">
	</head>
	<body>
		<?php print_message(); ?>
		<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="submit" value="1" />
			<input type="hidden" name="file_id" value="<?php echo $file_id; ?>" />
			<p>Description:</p><p><textarea name="description" rows="10"></textarea></p>
			<p>File to upload:</p><p><input type="file" name="file" />
			<p>Folder:</p><p><select name="folder_id">
			<?php
				foreach (get_folders() as $folder) {
					echo '<option value="', $folder[0], '"', ($folder_id == $folder[0] ? ' selected="selected"' : ''), '>', htmlspecialchars($folder[1]), '</option>';
				}
			?>
			</select>
			<p><input type="submit" value="Upload" /></p>
		</form>
	</body>
</html>
