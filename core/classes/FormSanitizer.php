<?php

namespace MyApp;

class FormSanitizer
{
	public static function sanitizeFromString(string $input): string
	{
		$input = strip_tags($input);
		$input = trim($input);
		$input = strtolower($input);
		return ucfirst($input);
	}
	public static function sanitizeFromUsername(string $input): string
	{
		$input = strip_tags($input);
		$input = trim($input);
		return  strtolower($input);
	}
	public static function sanitizeFromEmail(string $input): string
	{
		$input = htmlentities($input, ENT_QUOTES);
		$input = stripslashes($input);
		return  trim($input);
	}
	public static function sanitizeFromPassword(string $input): string
	{
		return strip_tags($input);
	}
}