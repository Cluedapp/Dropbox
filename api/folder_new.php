<?php
	require 'main.php';

	$parent_folder_id = $_REQUEST['parent_folder_id'];
	$parent_folder_id = empty($parent_folder_id) ? null : $parent_folder_id;
	check_folder_permission($parent_folder_id);
	$submit = $_POST['submit'];

	if (!empty($submit)) {
		$folder_name = stripslashes($_POST['folder_name']);
		if (empty($folder_name)) bad_request();

		$st = $pdo->prepare('INSERT INTO folders (folder_name, parent_folder_id, owner_id) VALUES (?, ?, ?)');
		$st->execute([$folder_name, $parent_folder_id, get_user_id()]);
		if (!$st->rowCount()) bad_request();
		$message = 'Folder created';
	}
?>
<!DOCTYPE html>
<html>
	<body>
		<?php print_message(); ?>
		<form method="post">
			<input type="hidden" name="submit" value="1" />
			<input type="hidden" name="parent_folder_id" value="<?php echo $parent_folder_id; ?>" />
			<p>Path: <?php echo get_folder_path($parent_folder_id); ?> <input name="folder_name" /></p>
			<p><input type="submit" value="Create folder" /></p>
		</form>
	</body>
</html>
