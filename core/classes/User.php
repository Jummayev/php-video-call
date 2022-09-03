<?php

namespace MyApp;

use PDO;

class User
{
	public $pdo, $session_id, $user_id;
	public function __construct()
	{
		$db = new Database();
		$this->pdo = $db->connect();
		$this->user_id = $this->id();
		$this->session_id = $this->sessionId();
	}
	public function id(){
		if (isset($_SESSION['user_id'])){
			return $_SESSION['user_id'];
		}
	}
	public function sessionId(): string
	{
		return session_id();
	}
	public function getUserBySessionId(string $session_id)
	{
		$user_id = (empty($session_id) ? $this->session_id : $session_id);
		$stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE session_id=:session_id");
		$stmt->bindValue(":session_id", $session_id, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}
	public function getUserByUsername(string $username)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE username=:username");
		$stmt->bindValue(":username", $username, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}
	public function getConnectedPeers()
	{
		$stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE id !=:id AND online_status ='Online' LIMIT 5");
		$stmt->bindValue(":id", $this->user_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}
	public function updateSession()
	{
		$stmt = $this->pdo->prepare("UPDATE  `users` SET `session_id`=:session_id WHERE id=:id");
		$stmt->bindValue(":session_id", $this->session_id, PDO::PARAM_STR);
		$stmt->bindValue(":id", $this->user_id, PDO::PARAM_INT);
		$stmt->execute();
	}
	public function updateConnection(int $user_id, int $connection_id, string $status="Offline")
	{
		$stmt = $this->pdo->prepare("UPDATE  `users` SET `connection_id`=:connection_id, `online_status`=:status WHERE id=:id");
		$stmt->bindValue(":connection_id", $connection_id, PDO::PARAM_INT);
		$stmt->bindValue(":status", $status, PDO::PARAM_STR);
		$stmt->bindValue(":id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
	}
	public function userData($user_id = "")
	{
		$user_id = (empty($user_id) ? $this->user_id : $user_id);
		$stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE id=:id");
		$stmt->bindValue(":id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}
}