<?php
	require 'main.php';

	$folder_id = $_REQUEST['folder_id'];
	check_folder_permission($folder_id);
	$submit = $_POST['submit'];
	$username = $_POST['username'];

	if (!empty($submit)) {
		if (empty($folder_id)) bad_request();
		if (empty($username)) bad_request();

		$st = $pdo->prepare('SELECT role_id FROM role_users, users WHERE username = ? AND role_users.user_id = users.user_id');
		$st->execute([$username]);
		if (!$st->rowCount()) bad_request();
		$row = $st->fetch();
		$st->closeCursor();
		$role_id = $row['role_id'];

		$st = $pdo->prepare('DELETE FROM folder_roles WHERE folder_id = ? AND role_id = ?');
		$st->execute([$folder_id, $role_id]);
		if ($st->rowCount()) $message = 'Folder permission deleted';
	}

	$st = $pdo->prepare('SELECT username FROM folder_roles, role_users, users WHERE folder_roles.folder_id = ? AND folder_roles.role_id = role_users.role_id AND role_users.user_id = users.user_id');
	$st->execute([$folder_id]);
	$users = $st->fetchAll();
	$st->closeCursor();
?>
<!DOCTYPE html>
<html>
	<body>
		<?php print_message(); ?>
		<form method="post">
			<input type="hidden" name="submit" value="1" />
			<input type="hidden" name="folder_id" value="<?php echo $folder_id; ?>" />
			<p>Path: <?php echo get_folder_path($folder_id); ?></p>
			<p>Current permissions:<br /><?php foreach ($users as $row) echo $row['username'], '<br />'; ?></p>
			<p>Username: <input name="username" /></p>
			<p><input type="submit" value="Delete permission from user for folder" /></p>
		</form>
	</body>
</html>
