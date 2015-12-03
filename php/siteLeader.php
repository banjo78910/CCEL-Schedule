<?php
/* - Add method to mediator to print info about any user.
 */

class SiteLeader {
	private $connection;
	private $username;
	private $site;
	
	public function __construct($username, $connection) {
		$this->connection = $connection;
		$this->username = $username;
		$siteQuery = "select site from {$GLOBALS["database"]}.managesSite where username = '{$this->username}'";
		$temp = $this->connection->query($siteQuery);
		$temp = $temp->fetch_assoc();
		$this->site = $temp['site'];
	}
	
	/**
	 * Add a session to the session table, and associate this tutor with it in the willTutor table.
	 */
	public function createSession($date, $time, $subject, $gradeLevel) {
		$maxIdQuery = "select max(sessionID) as sessionID from {$GLOBALS["database"]}.session";
		$sessions = $this->connection->query($maxIdQuery);
		$sessions = $sessions->fetch_assoc();
		$id = $sessions['sessionID'] + 1;
		$newSessionQuery = "insert into {$GLOBALS["database"]}.session values({$id}, '{$this->site}', '$date', '$time', '$subject', '$gradeLevel')";
		$this->connection->query($newSessionQuery);
		return $id;
	}
	
	/**
	 * Modify a session by deleting it, creating a new session, and transferring over tutors and attenders.
	 */
	public function modifySession($sessionID, $date, $time, $subject, $gradeLevel) {
		$oldSession = new Session($sessionID);
		$attenders = $oldSession->getSessionAttenders();
		$tutors = $oldSession->getSessionTutors();
		$this->cancelSession($sessionID);
		$newID = $this->createSession($date, $time, $subject, $gradeLevel);
		/* Add attenders to new session: */
		while ($row = $attenders->fetch_assoc()) {
			$attender = new Attender($row['userID'], $this->connection);
			$attender->willAttend($newID);
		}
		/* Add tutors to new session: */
		while ($row = $tutors->fetch_assoc()) {
			$tutor = new Tutor($row['tutorID'], $this->connection);
			$tutor->signUpToTutor($newID);
		}
	}
	
	/**
	 * Delete a session from the session table, and remove all associated tutors in the willTutor table.
	 */
	public function cancelSession($sessionID) {
		$cancelQuery = "delete from {$GLOBALS["database"]}.session where sessionID = {$sessionID}";
		$this->connection->query($cancelQuery);
		$willTutorQuery = "delete from {$GLOBALS["database"]}.willTutor where sessionID = {$sessionID}";
		$this->connection->query($willTutorQuery);
		$willAttendQuery = "delete from {$GLOBALS["database"]}.willAttend where sessionID = {$sessionID}";
		$this->connection->query($willAttendQuery);
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
			}
		}
	}
	
	/**
	 * Retrieve and display info about all sessions taking place at this site leader's site.
	 */
	public function displaySiteSessions() {
		$query = "select sessionID from {$GLOBALS["database"]}.session where site = '{$this->site}'";
		$sessions = $this->connection->query($query);
		while ($row = $sessions->fetch_assoc()) {
			$session = new Session($row['sessionID']);
			$session->display();
		}
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
		$this->connection->query($query);
	}
}
?>
