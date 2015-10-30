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
		if ($this->connection->connect_errno) {
    		echo "Failed to connect to MySQL: (" . $this->connection->connect_errno . ") " . $this->connection->connect_error;
    	}
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
		echo("\nliterally calling the function to indicate will attend on {$sessionID}\n");
		$query = "insert into ccelSchema.willAttend values (7, 'student1');";
	}
}
?>
