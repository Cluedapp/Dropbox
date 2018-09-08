<?php
	function normalize_folder($folder_name) {
		return '/' . trim($folder_name, '/');
	}

	function get_folder_id($folder_name) {
		global $pdo;

		$folder_name = normalize_folder($folder_name);
		$folders = split_folders($folder_name);
		$user_id = get_user_id();
		if (count($folders) > 0) {
			$folder_id = 0;
			foreach ($folders as $folder) {
				$sql = 'SELECT folder_id FROM folders WHERE folder_name = :folder_name AND has_folder_permission((SELECT folder_id FROM folders WHERE folder_name = :folder_name), :user_id) AND parent_folder_id ' . ($folder_id > 0 ? "= $folder_id" : 'IS NULL') . ' ORDER BY COALESCE(owner_id, 0) DESC LIMIT 1';
				$st = $pdo->prepare($sql);
				$st->bindParam(':folder_name', $folder);
				$st->bindParam(':user_id', $user_id);
				$st->execute();
				$row = $st->fetch();
				$st->closeCursor();
				if (!$row) not_found();
				$folder_id = $row['folder_id'];
			}
			return $folder_id;
		}
	}

	function get_folders() {
		global $pdo;
		$st = $pdo->query('SELECT folder_id, folder_name, parent_folder_id FROM folders');
		$folders = [];
		foreach ($st as $row) $folders[] = [$row['folder_id'], $row['folder_name'], $row['parent_folder_id']];
		foreach ($folders as $i => $folder) {
			while (!empty($folders[$i][2])) {
				foreach ($folders as $cur_folder) {
					if ($folders[$i][2] === $cur_folder[0]) {
						$folders[$i][1] = '/' . trim("{$cur_folder[1]}/{$folders[$i][1]}", '/');
						$folders[$i][2] = $cur_folder[2];
						break;
					}
				}
			}
		}
		usort($folders, function ($a, $b) { return strcmp($a[1], $b[1]); });
		return $folders;
	}

	function get_folder_path($folder_id) {
		global $pdo;
		$st = $pdo->query('SELECT folder_id, folder_name, parent_folder_id FROM folders');
		$folders = [];
		foreach ($st as $row) $folders[(int)$row['folder_id']] = [$row['folder_name'], (int)$row['parent_folder_id']];
		$path = '';
		$folder_id = (int)$folder_id;
		while ($folder_id && !empty($folders[$folder_id])) {
			$path = trim("{$folders[$folder_id][0]}/$path", '/');
			$folder_id = $folders[$folder_id][1];
		}
		return str_replace('//', '/', '/' . trim($path, '/') . '/');
	}

	function split_folders($folder_name) {
		$folder_name = normalize_folder($folder_name);
		$a = preg_split('/\//', $folder_name, -1, PREG_SPLIT_NO_EMPTY);
		array_splice($a, 0, 0, '/');
		return $a;
	}
?>
