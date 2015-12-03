<?php
class Tutor {
	private $connection;
	private $username;
	
	public function __construct($username, $connection) {
		$this->connection = $connection;
		$this->username = $username;
	}
	
	/**
	 * Sign up to tutor at a given session.
	 */
	public function signUpToTutor($sessionID) {
		$query = "insert into {$GLOBALS["database"]}.willTutor values ({$sessionID}, '{$this->username}')";
		$this->connection->query($query);
	}
	
	/**
	 * Indicate that this tutor will no longer tutor at a given upcoming session.
	 */
	public function cancelTutor($sessionID) {
		$query = "delete from {$GLOBALS["database"]}.willTutor where sessionID = {$sessionID} and tutorID = '{$this->username}'";
		echo($query);
		$this->connection->query($query);
	}
	
	/**
	 * Retrieve and display info about all sessions at which this tutor is scheduled to tutor.
	 */
	public function displaySessions() {
		$query = "select sessionID from {$GLOBALS["database"]}.willTutor where tutorID = '{$this->username}'";
		$sessions = $this->connection->query($query);
		if ($sessions->num_rows == 0) {
			echo("Not tutoring at any sessions yet");
		}
		else {
			while ($row = $sessions->fetch_assoc()) {
				$session = new Session($row['sessionID']);
				$session->display();
				$session->getSessionAttenders();
			}
		}
	}
}
?>
