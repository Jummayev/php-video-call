<?php

namespace MyApp;
use PDO;

class Database
{
	function connect(): PDO
	{
			return new PDO("mysql:dbname=php-video-call-app;host=localhost", "root", "");
	}
}