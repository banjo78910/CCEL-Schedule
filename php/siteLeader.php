<?php
class SiteLeader {
	private $connection;
	private $username;
	private $site;
	private $messenger;
	
	public function __construct($username, $connection, $messenger) {
		$this->connection = $connection;
		$this->username = $username;
		$siteQuery = "select site from {$GLOBALS["database"]}.managesSite where username = '{$this->username}'";
		$temp = $this->connection->query($siteQuery);
		$temp = $temp->fetch_assoc();
		$this->site = $temp['site'];
		$this->messenger = $messenger;
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
		/* Record info about the session about to be cancelled: */
		$sessionQuery = "select * from {$GLOBALS["database"]}.session where sessionID = {$sessionID}";
		$sessionInfo = $this->connection->query($sessionQuery);
		$sessionInfo = $sessionInfo->fetch_assoc();
		/* Find out which tutors will be affected by this cancellation: */
		$affectedTutorsQuery = "select tutorID from {$GLOBALS["database"]}.willTutor where sessionID = {$sessionID}";
		$affectedTutors = $this->connection->query($affectedTutorsQuery);
		/* Find out which attenders will be affected by this cancellation: */
		$affectedAttendersQuery = "select userID from {$GLOBALS["database"]}.willAttend where sessionID = {$sessionID}";
		$affectedAttenders = $this->connection->query($affectedAttendersQuery);
		/* Perform the cancellation: */
		$cancelQuery = "delete from {$GLOBALS["database"]}.session where sessionID = {$sessionID}";
		$this->connection->query($cancelQuery);
		$willTutorQuery = "delete from {$GLOBALS["database"]}.willTutor where sessionID = {$sessionID}";
		$this->connection->query($willTutorQuery);
		$willAttendQuery = "delete from {$GLOBALS["database"]}.willAttend where sessionID = {$sessionID}";
		$this->connection->query($willAttendQuery);
		/* Inform affected tutors: */
		while ($row = $affectedTutors->fetch_assoc()) {
			$recipient = $row['tutorID'];
			$subject = "Session at {$sessionInfo['site']} on {$sessionInfo['date']} cancelled";
			$content = "Hello, \nThe {$sessionInfo['subject']} tutoring session scheduled to take place at {$sessionInfo['site']} at {$sessionInfo['time']} on {$sessionInfo['date']} has been cancelled."
			           . "\nPlease pay attention to the home page for future sessions at this site!";
			$this->messenger->sendMessage($recipient, $subject, $content);
		}
		/* Inform affected attenders: */
		while ($row = $affectedAttenders->fetch_assoc()) {
			$recipient = $row['userID'];
			$subject = "Session at {$sessionInfo['site']} on {$sessionInfo['date']} cancelled";
			$content = "Hello, \nThe {$sessionInfo['subject']} tutoring session scheduled to take place at {$sessionInfo['site']} at {$sessionInfo['time']} on {$sessionInfo['date']} has been cancelled."
			           . "\nPlease pay attention to the home page for future sessions at this site!";
			$this->messenger->sendMessage($recipient, $subject, $content);
		}
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
