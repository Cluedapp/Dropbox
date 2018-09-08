<?php
	function ok() {
		header('HTTP/1.1 200 OK');
		die;
	}

	function bad_request() {
		header('HTTP/1.1 400 Bad Request');
		die;
	}

	function forbidden() {
		# header('HTTP/1.1 403 Forbidden');
		header('Location: /login');
		die;
	}

	function not_found() {
		header('HTTP/1.1 404 Not Found');
		die;
	}

	function internal_server_error() {
		header('HTTP/1.1 500 Internal Server Error');
		die;
	}
	
	function print_message() {
		global $message;
		if ($message) echo "<p>$message</p>";
	}
?>
