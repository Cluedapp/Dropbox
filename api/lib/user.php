<?php
	function check_file_permission($file_id) {
		global $pdo;

		if (!$file_id) {
			if (logged_in())
				return;
			else
				forbidden();
		}

		$user_id = get_user_id();

		# By default, grant access to a file if its folder has no parent folder
		# By default, grant access to a file if its parent folder has no permissions set
		# Check if user has permission to access file's folder
		$st = $pdo->prepare('SELECT has_folder_permission((SELECT folder_id FROM files WHERE file_id = ?), ?)');
		$st->execute([$file_id, $user_id]);
		$row = $st->fetch();
		$st->closeCursor();
		if (!$row[0] == 1) forbidden();
	}

	function check_folder_permission($folder_id) {
		if (!has_folder_permission($folder_id)) forbidden();
	}

	function has_folder_permission($folder_id) {
		global $pdo;

		if (!$folder_id)
			return logged_in();

		$user_id = get_user_id();

		# By default, grant access to a folder if it has no permissions set
		# Check if user has permission to access folder
		$st = $pdo->prepare('SELECT has_folder_permission(:folder_id, :user_id)');
		$st->bindParam(':folder_id', $folder_id);
		$st->bindParam(':user_id', $user_id);
		$st->execute();
		$row = $st->fetch();
		$st->closeCursor();
		return $row[0] == 1;
	}

	function get_user_id() {
		return logged_in() ? $_SESSION['user_id'] : 0;
	}

	function logged_in() {
		return isset($_SESSION['logged_in']);
	}
	
	function login($username, $password) {
		global $pdo;

		$st = $pdo->prepare('SELECT user_id FROM users WHERE username = ? AND password = ?');
		$st->execute([$username, $password]);
		if ($row = $st->fetch()) {
			$_SESSION['logged_in'] = 1;
			$_SESSION['user_id'] = $row['user_id'];
		}
	}
?>
