<?php

use MyApp\Account;
use MyApp\User;

ob_start();
	session_start();

	require "classes/FormSanitizer.php";
	require "classes/Database.php";
	require "classes/Constant.php";
	require "classes/Account.php";
	require "classes/User.php";

	$account = new Account();
	$loadFromUser = new User();
	const WWW_ROOT = "http://localhost/test/php-video-call/";

	require "functions.php";
