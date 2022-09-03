<?php


namespace MyApp\src;

use MyApp\User;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class Chat implements MessageComponentInterface
{

	/**
	 * @var SplObjectStorage
	 */
	protected SplObjectStorage $clients;
	private $userData;
	private User $userObj;

	public function __construct()
	{
		$this->clients = new \SplObjectStorage;
		$this->userObj = new User();
	}

	public function onOpen(ConnectionInterface $conn)
	{
		$queryString = $conn->httpRequest->getUri()->getQuery();
		parse_str($queryString, $query);
		if ($data = $this->userObj->getUserBySessionId($query['token'])){

			$this->userData = $data;
			$conn->userData = $data;
			$conn->send(json_encode([
				"type" => "CONNECTION_DISCONNECTED",
				"status" => "online"
			]));

			foreach ($this->clients as $client){
				$client->send(json_encode([
					"type" => "NEW_USER_CONNECTION",
					"status" => "online",
					"full_name" => $data->first_name." ".$data->last_name,
					"profile_image" => $data->avatar,
					"user_id" => $data->id
				]));
			}

			$this->userObj->updateConnection($data->id, $conn->resourceId, "Online");

		// Store the new connection in $this->clients
		$this->clients->attach($conn);
		echo "New connection! ({$data->username})\n";
		}

	}

	public function onMessage(ConnectionInterface $from, $msg)
	{
		$data = json_decode($msg, true);
		$type = $data['type'];
		switch ($type){
			case "client-is-ready" :
				foreach ($this->clients as $client) {
					if ($from == $client) {
						$client->send(json_encode([
							"type" => "client-is-ready",
							"success" => true
						]));
					}
				}
				break;
			case "offer" :
				$receiverData = $this->userObj->userData($data['target']);
				foreach ($this->clients as $client) {
					if ($from !== $client) {
						if ($client->resourceId == $receiverData->connection_id || $from == $client) {
							$client->send(json_encode([
								"type" => $data['type'],
								"offer" => $data['data'],
								"sender" => $from->userData->id,
								"receiver" => $data['target'],
								"name" => $from->userData->username,
								"profileImage" => $from->userData->avatar,
								"success" => true
							]));
						} else {
							echo "offer error";
						}
					}
					break;
				}
				break;
			case "answer" :
				$receiverData = $this->userObj->userData($data['target']);
				foreach ($this->clients as $client) {
					if ($from !== $client) {
						if ($client->resourceId == $receiverData->connection_id || $from == $client) {
							$client->send(json_encode([
								"type" => $data['type'],
								"answer" => $data['data'],
								"sender" => $from->userData->id,
								"receiver" => $data['target'],
							]));
						} else {
							echo "offer error";
						}
					}
					break;
				}
		}


	}

	public function onClose(ConnectionInterface $conn)
	{
		$queryString = $conn->httpRequest->getUri()->getQuery();
		parse_str($queryString, $query);
		if ($data = $this->userObj->getUserBySessionId($query['token'])) {

			$this->userData = $data;
			$conn->userData = $data;
			$conn->send(json_encode([
				"type" => "CONNECTION_DISCONNECTED",
				"status" => "Offline"
			]));

			foreach ($this->clients as $client){
				$client->send(json_encode([
					"type" => "NEW_USER_DISCONNECTION",
					"status" => "offline",
					"full_name" => $data->first_name." ".$data->last_name,
					"profile_image" => $data->avatar,
					"user_id" => $data->id
				]));
			}

			$this->userObj->updateConnection($data->id, $conn->resourceId);
			$this->clients->detach($conn);
		}
		}

	public function onError(ConnectionInterface $conn, \Exception $e)
	{
	}
}