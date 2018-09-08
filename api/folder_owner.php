<?php
	require 'main.php';

	$folder_id = $_REQUEST['folder_id'];
	check_folder_permission($folder_id);
	$submit = $_POST['submit'];
	$username = $_POST['username'];

	if (!empty($submit)) {
		if (empty($folder_id)) bad_request();
		if (empty($username)) bad_request();

		$st = $pdo->prepare('SELECT user_id FROM users WHERE username = ?');
		$st->execute([$username]);
		if (!$st->rowCount()) bad_request();
		$row = $st->fetch();
		$st->closeCursor();
		$user_id = $row['user_id'];

		$st = $pdo->prepare('UPDATE folders SET owner_id = ? WHERE folder_id = ?');
		$st->execute([$user_id, $folder_id]);
		if ($st->rowCount()) $message = 'Folder owner changed';

		$owner = $username;
	} else {
		$st = $pdo->prepare('SELECT username FROM folders, users WHERE folders.folder_id = ? AND folders.owner_id = users.user_id');
		$st->execute([$folder_id]);
		$row = $st->fetch();
		$st->closeCursor();
		$owner = $row['username'];
	}
?>
<!DOCTYPE html>
<html>
	<body>
		<?php print_message(); ?>
		<form method="post">
			<input type="hidden" name="submit" value="1" />
			<input type="hidden" name="folder_id" value="<?php echo $folder_id; ?>" />
			<p>Path: <?php echo get_folder_path($folder_id); ?></p>
			<p>Current owner:<br /><?php echo $owner; ?></p>
			<p>Username: <input name="username" /></p>
			<p><input type="submit" value="Add permission to user for folder" /></p>
		</form>
	</body>
</html>
