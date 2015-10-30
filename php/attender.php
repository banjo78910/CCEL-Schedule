<?php
$server = "localhost";
$dbUsername = "root";
$dbPassword = "root";
$database = "ccelSchema";

class attender {
	private $connection;
	private $sessions;
	private $username;
	
	public function __construct($userInfo) {
		$this->connection = new mysqli($GLOBALS["server"], $GLOBALS["dbUsername"], $GLOBALS["dbPassword"]);
		$this->sessions = null;
		$this->username = $userInfo["username"];
	}
	
	public function retrieveAttendingSessions() {
		$narrowQuery = "select sessionID from {$GLOBALS["database"]}.willAttend where userID = '{$this->username}'";
		$query = "select * from {$GLOBALS["database"]}.session where sessionID in ({$narrowQuery})";
		$this->sessions = $this->connection->query($query);
		while ($row = $this->sessions->fetch_assoc()) {
			user::displaySession($row);
		}
	}
	
	public function willAttend($sessionID) {
		$query = "insert into {$GLOBALS["database"]}.willAttend values ({$sessionID}, '{$this->username}')";
	}
}
?>
