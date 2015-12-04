<?php
include("dbglobals.php");

class Messenger {
	private $connection;
	private $username;
	private $role;
	
	public function __construct($username) {
		$this->connection = new mysqli($GLOBALS["server"], $GLOBALS["dbUsername"], $GLOBALS["dbPassword"]);
		$this->username = $username;
		$roleResult = $this->connection->query("select role from {$GLOBALS['database']}.user where username = '{$this->username}'");
		$roleResult = $roleResult->fetch_assoc();
		$this->role = $roleResult['role'];
	}
	
	public function getAllowedRecipients() {
		$allowedRecipientsArray = array();
		if ($this->role == 'attender') {
			$query = "select username from {$GLOBALS['database']}.user where role = 'tutor' or role = 'siteLeader'";
			$allowedRecipients = $this->connection->query($query);
			while ($row = $allowedRecipients->fetch_assoc()) {
				array_push($allowedRecipientsArray, $row['username']);
			}
		}
		else {
			$query = "select username from {$GLOBALS['database']}.user";
			$allowedRecipients = $this->connection->query($query);
			while ($row = $allowedRecipients->fetch_assoc()) {
				array_push($allowedRecipientsArray, $row['username']);
			}
		}
		echo(json_encode($allowedRecipientsArray));
	}
	
	public function getMessages() {
		$query = "select * from {$GLOBALS['database']}.message where recipientID = '{$this->username}'";
		$messages = $this->connection->query($query);
		return $messages;
	}
	
	public function getJsonMessages() {
		$jsonMessageArray = array();
		$messages = $this->getMessages();
		while ($row = $messages->fetch_assoc()) {
			$jsonMessage = array();
			$jsonMessage = array("senderID" => $row['senderID'], "recipientID" => $row['recipientID'], "subject" => $row['subject'], "message" => $row['content'], "messageID" => $row['messageID']);
			array_push($jsonMessageArray, $jsonMessage);
		}
		echo(json_encode($jsonMessageArray));
	}
	
	public function sendJsonMessage($jsonString) {
		$messageInfo = json_decode($jsonString, true);
		$this->sendMessage($messageInfo['recipient'], $messageInfo['subject'], $messageInfo['message']);
	}
	
	public function sendMessage($recipient, $subject, $content) {
		$checkRecipientQuery = "select * from {$GLOBALS['database']}.user where username = '{$recipient}'";
		if ($this->connection->query($checkRecipientQuery)->num_rows == 0) {
			// The recipient is not a registered user.
		}
		else {
			$maxIdQuery = "select max(messageID) as messageID from {$GLOBALS["database"]}.message";
			$maxID = $this->connection->query($maxIdQuery);
			$maxID = $maxID->fetch_assoc();
			$id = $maxID['messageID'] + 1;
			$query = "insert into {$GLOBALS['database']}.message values ('{$this->username}', '{$recipient}', '{$subject}', '{$content}', {$id})";
			$this->connection->query($query);
		}
	}
	
	public function deleteMessage($messageID) {
		$query = "delete from {$GLOBALS["database"]}.message where messageID = {$messageID}";
		$this->connection->query($query);
	}
}
?>
