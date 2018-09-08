<?php
	require 'main.php';

	$folder_id = $_REQUEST['folder_id'];
	check_folder_permission($folder_id);
	$submit = $_POST['submit'];

	$st = $pdo->prepare('SELECT parent_folder_id FROM folders WHERE folder_id = ?');
	$st->execute([$folder_id]);
	$row = $st->fetch();
	$st->closeCursor();
	if ($row) $parent_folder_id = $row['parent_folder_id'];

	if (!empty($submit)) {
		$folder_name = $_POST['folder_name'];
		$st = $pdo->prepare('UPDATE folders SET folder_name = ? WHERE folder_id = ?');
		$st->execute([$folder_name, $folder_id]);
		if ($st->rowCount()) $message = 'Folder renamed';
	}
?>
<!DOCTYPE html>
<html>
	<body>
		<?php print_message(); ?>
		<form method="post">
			<input type="hidden" name="submit" value="1" />
			<input type="hidden" name="folder_id" value="<?php echo $folder_id; ?>" />
			<p>Path: <?php echo get_folder_path($parent_folder_id); ?> <input name="folder_name" /></p>
			<p><input type="submit" value="Rename folder" /></p>
		</form>
	</body>
</html>
