<?php

/* - Make echoed usernames a clickable link that calls a js script, bringing up a pop-up with the user's info
 */

class Tutor {
	private $connection;
	private $username;
	private $sessions;
	
	public function __construct($username, $connection) {
		$this->connection = $connection;
		$this->username = $username;
		$this->sessions = null;
	}
	
	public function createSession($site, $date, $time, $subject, $gradeLevel, $tutorUsername, $tutorName) {
		$maxIdQuery = "select max(sessionID) as sessionID from {$GLOBALS["database"]}.session";
		$this->sessions = $this->connection->query($maxIdQuery);
		$this->sessions = $this->sessions->fetch_assoc();
		$id = $this->sessions['sessionID'] + 1;
		$newSessionQuery = "insert into {$GLOBALS["database"]}.session values($id, '$site', '$date', '$time', '$subject', '$gradeLevel', '$tutorUsername', '$tutorName')";
		$this->connection->query($newSessionQuery);
	}
	
	public function cancelSession($sessionID) {
		$cancelQuery = "delete from {$GLOBALS["database"]}.session where sessionID = $sessionID";
		$this->connection->query($cancelQuery);
	}
	
	public function getAttendingUsers($sessionID) {
		$query = "select userID from {$GLOBALS["database"]}.willAttend where sessionID = $sessionID";
		$this->sessions = $this->connection->query($query);
		if ($this->sessions->num_rows == 0) {
			echo("No attenders have signed up yet.");
		}
		else {
			$row = $this->sessions->fetch_assoc();
			echo("{$row['userID']}");
			while (($row = $this->sessions->fetch_assoc()) != null) {
				echo(", {$row['userID']}");
			}
		}
	}
}
?>
