<?php

	function h(string $string = ''): string
	{
		return htmlspecialchars($string);
	}

	function is_request_post(): bool
	{
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}

	function getInputValue( string $input):void
	{
		if (isset($_POST[$input])){
			echo $_POST[$input];
		}
	}
	function url_for(string $script):string
	{
		return WWW_ROOT.$script;
	}
	function redirect_to(string $location)
	{
		header("Location:". $location);
		exit;
	}
	function log_out():bool
	{
		unset($_SESSION['user_id']);
		$_SESSION = [];
		session_destroy();
		session_regenerate_id();
		return true;
	}

