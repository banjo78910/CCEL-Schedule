<?php
include("dbglobals.php");

class Session {
	private $connection;
	private $sessionID;
	private $site;
	private $date;
	private $time;
	private $subject;
	private $gradeLevel;
	
	public function __construct($sessionID) {
		$this->connection = new mysqli($GLOBALS["server"], $GLOBALS["dbUsername"], $GLOBALS["dbPassword"]);
		$query = "select * from {$GLOBALS['database']}.session where sessionID = {$sessionID}";
		$result = $this->connection->query($query);
		$result = $result->fetch_assoc();
		$this->sessionID = $sessionID;
		$this->site = $result['site'];
		$this->date = $result['date'];
		$this->time = $result['time'];
		$this->subject = $result['subject'];
		$this->gradeLevel = $result['gradeLevel'];
	}
	
	public function display() {
		echo("<div class=\"list-group-item\" id=\"session{$this->sessionID}\">");
		if (isset($_COOKIE['username'])) {
			if ($this->willBeAttendedBy($_COOKIE['username']) || $this->willBeTutoredBy($_COOKIE['username'])) {
				echo("<button type='button' id='{$this->sessionID}' class='btn btn-session btn-danger pull-right delete-from-sessions'> <span class='glyphicon glyphicon-minus' pointer-events:none> </span></button>");
			}
			else {
				echo("<button type='button' id='{$this->sessionID}' class='btn btn-session btn-success pull-right add-to-sessions'> <span class='glyphicon glyphicon-plus' pointer-events:none> </span></button>");
			}
		}
		echo("<h4>{$this->site}: {$this->subject}</h4><h5>{$this->date} at {$this->time}</h5>");
		$this->displaySessionTutors();
		echo("</div>");
	}
	
	public function getSessionAttenders() {
		$query = "select userID from {$GLOBALS["database"]}.willAttend where sessionID = {$this->sessionID}";
		$attenders = $this->connection->query($query);
		return $attenders;
	}
	
	public function displaySessionAttenders() {
		$attenders = $this->getSessionAttenders();
		if ($attenders->num_rows == 0) {
			echo("No attenders have signed up yet.");
		}
		else {
			$row = $attenders->fetch_assoc();
			echo("{$row['userID']}");
			while (($row = $attenders->fetch_assoc()) != null) {
				echo(", {$row['userID']}");
			}
		}
	}
	
	public function getSessionTutors() {
		$query = "select tutorID from {$GLOBALS["database"]}.willTutor where sessionID = {$this->sessionID}";
		$tutors = $this->connection->query($query);
		return $tutors;
	}
	
	public function displaySessionTutors() {
		$tutors = $this->getSessionTutorNames();
		if ($tutors->num_rows == 0) {
			echo("No tutors have signed up to tutor at this session yet.");
		}
		else {
			$row = $tutors->fetch_assoc();
			echo("Tutors: {$row['firstName']}");
			while (($row = $tutors->fetch_assoc()) != null) {
				echo(", {$row['firstName']}");
			}
		}
	}
	
	private function getSessionTutorNames() {
		$narrowQuery = "select tutorID from {$GLOBALS["database"]}.willTutor where sessionID = {$this->sessionID}";
		$query = "select firstName from {$GLOBALS["database"]}.user where username in ({$narrowQuery})";
		$tutors = $this->connection->query($query);
		return $tutors;
	}
	
	private function willBeAttendedBy($username) {
		$thingToReturn = false;
		$attenders = $this->getSessionAttenders();
		while ($row = $attenders->fetch_assoc()) {
			if ($row['userID'] == $username) {
				$thingToReturn = true;
			}
		}
		return $thingToReturn;
	}
	
	private function willBeTutoredBy($username) {
		$thingToReturn = false;
		$tutors = $this->getSessionTutors();
		while ($row = $tutors->fetch_assoc()) {
			if ($row['tutorID'] == $username) {
				$thingToReturn = true;
			}
		}
		return $thingToReturn;
	}
}
?>
