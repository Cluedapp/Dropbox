<?php
	require_once 'main.php';

	$folder = normalize_folder(@$_GET['folder']); # fine if it is null, '', or non-empty
	$folders = split_folders($folder); # array of folder names, from first to last, e.g. /a/b/c -> ['', 'a', 'b', 'c']
	$folder_id = get_folder_id($folder);
	check_folder_permission($folder_id);
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/style.css">
	</head>
	<body>
	<table cellspacing="0">
			<tr><th>Name:</th><th>File type:</th><th>Date:</th><th>View:</th><th>Download:</th><th>Edit:</th><th>Upload:</th><th>Delete:</th><th>Hash:</th></tr>
			<?php
				# Add ..
				echo '<tr><td colspan="99"><a href="/list/', trim(implode('/', array_slice($folders, 0, count($folders) - 1)), '/'), '">..</a></td></tr>';

				# Add subfolders
				$st = $pdo->query('SELECT folder_id, folder_name FROM folders WHERE parent_folder_id ' . ($folder_id > 0 ? "= $folder_id" : 'IS NULL') . ' ORDER BY folder_name');
				foreach ($st as $row) {
					if (has_folder_permission($row['folder_id'])) {
						$name = $row['folder_name'];
						echo '<tr><td colspan="99"><a href="/list', trim($folder, '/') ? "$folder/" : '/', urlencode($name), '">', htmlspecialchars($name), '</a></td></tr>';
					}
				}

				# Add files
				$st = $pdo->query('SELECT file_id, file_name, file_mime_type, file_date FROM files WHERE folder_id ' . ($folder_id > 0 ? "= $folder_id" : 'IS NULL') . ' ORDER BY file_name');
				foreach ($st as $row) {
					$file_id = $row['file_id'];
					$name = htmlspecialchars($row['file_name']);
					$mime = htmlspecialchars($row['file_mime_type']);
					$date = htmlspecialchars($row['file_date']);
					echo '<tr><td><a href="/file/', $file_id, '">', $name, '</a></td><td>', $mime, '</td><td>', $date, '</td><td><a href="/view/', $file_id, '">View</a></td><td><a href="/file/', $file_id, '">Download</a></td><td><a href="/edit/', $file_id, '">Edit</a></td><td><a href="/upload/', $file_id, '">Upload</a></td><td><a href="/delete/', $file_id, '">Delete</a></td><td><a href="/hash/', $file_id, '">Hash</a></td></tr>';
				}
			?>
		</table>
		<p>&nbsp;</p>
		<div class="options">
			<p>Folder options:</p>
			<p><a href="/upload?folder_id=<?php echo $folder_id; ?>">Upload new file</a></p>
			<p><a href="/edit?folder_id=<?php echo $folder_id; ?>">Edit new file</a></p>
			<p><a href="/folder/new/<?php echo $folder_id; ?>">Create new subfolder</a></p>
			<p><a href="/folder/delete/<?php echo $folder_id; ?>">Delete folder</a></p>
			<p><a href="/folder/rename/<?php echo $folder_id; ?>">Rename folder</a></p>
			<p><a href="/folder/owner/<?php echo $folder_id; ?>">Change folder owner</a></p>
			<p><a href="/folder/add_permission/<?php echo $folder_id; ?>">Add permission</a></p>
			<p><a href="/folder/delete_permission/<?php echo $folder_id; ?>">Delete permission</a></p>
			<p><a href="/logout">Log out</a></p>
		</div>
	</body>
</html>