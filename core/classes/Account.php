<?php

namespace MyApp;

use PDO;

class Account
{
	public $pdo;
	public $validate_errors = [];
	public function __construct()
	{
		$db = new Database();
		$this->pdo = $db->connect();
	}
	public function register(string $first_name, string $last_name, string $username,string $email, string $password)
	{
		$this->validateFirstName($first_name);
		$this->validateLastName($last_name);
		$this->validateUsername($username);
		$this->validateEmail($email);
		$this->validatePassword($password);
		if (empty($this->validate_errors)){
			return $this->insertUserDetails($first_name, $last_name, $username, $email, $password);
		}else{
			return false;
		}
	}
	public function login(string $username, string $password): ?bool
	{
		$stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE username=:username OR email=:username");
		$stmt->bindValue(":username", $username, PDO::PARAM_STR);
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_OBJ);
		if ($stmt->rowCount() != 0){
			if (password_verify($password, $user->password)){
				return $user->id;
			}else{
				$this->validate_errors[] = Constant::LoginFailed;
				return false;
			}
		}else{
			$this->validate_errors[] = Constant::LoginFailed;
			return false;
		}
	}
	private function validateFirstName(string $first_name)
	{
		if ($this->length($first_name, 2, 25)){
			return array_push($this->validate_errors, Constant::FirstNameCharacters);
		}
	}
	private function validateLastName(string $last_name)
	{
		if ($this->length($last_name, 2, 25)){
			return array_push($this->validate_errors, Constant::LastNameCharacters);
		}
	}
	private function validateUsername(string $username)
	{
		if ($this->length($username, 2, 25)){
			return array_push($this->validate_errors, Constant::UsernameCharacters);
		}
		$stmt = $this->pdo->prepare("SELECT `username` FROM `users` WHERE username=:username");
		$stmt->bindValue(":username", $username, \PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount() != 0){
			return array_push($this->validate_errors, Constant::UsernameTake);
		}
	}
	private function validateEmail(string $email)
	{
		if ($this->length($email, 2, 25)){
			return array_push($this->validate_errors, Constant::EmailCharacters);
		}
		$stmt = $this->pdo->prepare("SELECT `email` FROM `users` WHERE email=:email");
		$stmt->bindValue(":email", $email, \PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount() != 0){
			return array_push($this->validate_errors, Constant::EmailTake);
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
			return array_push($this->validate_errors, Constant::EmailInvalid);
		}
	}
	private function validatePassword(string $password)
	{
		if ($this->length($password, 5, 32)){
			return array_push($this->validate_errors, Constant::PasswordCharacters);
		}
		if (preg_match("/[^A-Za-z0-9]/", $password)){
			return array_push($this->validate_errors, Constant::PasswordNotAlphaNumeric);
		}
	}
	private function length(string $input, int $min, int $max)
	{
		if (strlen($input) < $min){
			return true;
		}else if(strlen($input) > $max){
			return true;
		}
	}
	public function getError(string $errorMessage){
		if (in_array($errorMessage, $this->validate_errors)){
			return "<span class='errorMessage'> $errorMessage </span>";
		}
	}
	private function insertUserDetails(string $first_name, string $last_name, string $username, string $email, string $password)
	{
		$password_hash = $this->hash($password);
		switch (rand(0, 5)){
			case 0 : $profilePic = "asset/images/avatar.png"; break;
			case 1 : $profilePic = "asset/images/defaultPic.svg"; break;
			case 2 : $profilePic = "asset/images/defaultProfilePic.png"; break;
			case 3 : $profilePic = "asset/images/other.jpg"; break;
			case 4 : $profilePic = "asset/images/profilePic.jpeg"; break;
			case 5 : $profilePic = "asset/images/user_profile.png"; break;
		}
		$stmt = $this->pdo->prepare("INSERT INTO users (first_name, last_name, username, email, password, avatar) VALUES (:first_name,:last_name,:username,:email,:password,:avatar)");
		$stmt->bindParam(":first_name", $first_name, PDO::PARAM_STR);
		$stmt->bindParam(":last_name", $last_name, PDO::PARAM_STR);
		$stmt->bindParam(":username", $username, PDO::PARAM_STR);
		$stmt->bindParam(":email", $email, PDO::PARAM_STR);
		$stmt->bindParam(":password", $password_hash, PDO::PARAM_STR);
		$stmt->bindParam(":avatar", $profilePic, PDO::PARAM_STR);
		$stmt->execute();
		return $this->pdo->lastInsertId();
	}
	private function hash(string $password): string
	{
		return password_hash($password, PASSWORD_BCRYPT);
	}

}