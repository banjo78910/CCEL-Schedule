<?php
class Attender {
	private $connection;
	private $username;
	
	public function __construct($username, $connection) {
		$this->connection = $connection;
		$this->username = $username;
	}
	
	public function displayAttendingSessions() {
		$query = "select sessionID from {$GLOBALS["database"]}.willAttend where userID = '{$this->username}'";
		$sessions = $this->connection->query($query);
		while ($row = $sessions->fetch_assoc()) {
			$session = new Session($row['sessionID']);
			$session->display();
		}
	}
	
	public function willAttend($sessionID) {
		$query = "insert into {$GLOBALS["database"]}.willAttend values ({$sessionID}, '{$this->username}')";
		$this->connection->query($query);
	}
	
	public function cancelAttend($sessionID) {
		$query = "delete from {$GLOBALS["database"]}.willAttend where sessionID = $sessionID and userID = '{$this->username}'";
		$this->connection->query($query);
	}
}
?>
