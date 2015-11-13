<?php
class Attender {
	private $connection;
	private $sessions;
	private $username;
	
	public function __construct($username, $connection) {
		$this->connection = $connection;
		$this->sessions = null;
		$this->username = $username;
	}
	
	public function retrieveAttendingSessions() {
		$narrowQuery = "select sessionID from {$GLOBALS["database"]}.willAttend where userID = '{$this->username}'";
		$query = "select * from {$GLOBALS["database"]}.session where sessionID in ({$narrowQuery})";
		$this->sessions = $this->connection->query($query);
		while ($row = $this->sessions->fetch_assoc()) {
			mediator::displaySession($row);
		}
	}
	
	public function willAttend($sessionID) {
		$query = "insert into {$GLOBALS["database"]}.willAttend values ({$sessionID}, '{$this->username}')";
		$this->connection->query($query);
	}
}
?>
