<?php
	require 'main.php';

	$folder_id = $_GET['folder_id'];
	
	if (empty($folder_id)) bad_request();

	check_folder_permission($folder_id);

	$message = '';
	$st = $pdo->prepare('DELETE FROM files WHERE folder_id = ?');
	$st->execute([$folder_id]);
	$message .= ($st->rowCount() ? 'Files deleted' : 'No files deleted') . '<br /><br />';
	$st->closeCursor();

	$st = $pdo->prepare("UPDATE folders SET parent_folder_id = (SELECT folder_id FROM folders WHERE folder_name = '/') WHERE parent_folder_id = ?");
	$st->execute([$folder_id]);
	$message .= ($st->rowCount() ? 'Subfolders moved to root' : 'No subfolders moved') . '<br /><br />';
	$st->closeCursor();

	$st = $pdo->prepare('DELETE FROM folder_roles WHERE folder_id = ?');
	$st->execute([$folder_id]);
	$message .= ($st->rowCount() ? 'Folder roles deleted' : 'No folder roles deleted') . '<br /><br />';
	$st->closeCursor();

	$st = $pdo->prepare('DELETE FROM folders WHERE folder_id = ?');
	$st->execute([$folder_id]);
	$message .= ($st->rowCount() ? 'Folder deleted' : 'No folder deleted') . '<br /><br />';
	$st->closeCursor();
?>
<!DOCTYPE html>
<html>
	<body>
		<?php print_message(); ?>
	</body>
</html>