<?php
	require 'main.php';

	if (logged_in()) {
		header("Location: /list/$folder");
		die;
	}

	if (!logged_in() && isset($_POST['submit'])) {
		$username = @$_POST['username'];
		$password = @$_POST['password'];
		$folder = trim(@$_POST['folder'], '/');

		login($username, $password);
		if (logged_in()) {
			header("Location: /list/$folder");
			die;
		}
	}
?>
<!DOCTYPE html>
<html>
<body>
<?php if (logged_in()) { ?>
	<p>Already logged in</p>
	<p><a href="/logout">Logout</a></p>
<?php } else { ?>
	<form method="post">
		<input type="hidden" name="submit" />
		<input type="hidden" name="folder" value="<?php echo htmlspecialchars(normalize_folder($_GET['folder'])); ?>" />
		<p>Username:</p><p><input name="username" /></p>
		<p>Password:</p><p><input type="password" name="password" /></p>
		<p><input type="submit" value="Login" /></p>
	</form>
<?php } ?>
</body>
