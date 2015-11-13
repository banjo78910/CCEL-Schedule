<?php
/* - shade every other session in displayAllSessions
 * - generate page divs in displayAllSessions
 * - make sure all pages redirect to home page and inform the user if their login cookie expired
 * - handle additional roles in specializeLogin
 */

include("user.php");
include("tutor.php");
include("attender.php");
include("supervisor.php");

$server = "localhost";
$dbUsername = "root";
$dbPassword = "4828";
$database = "ccelSchema";

class Mediator {
	/* Holds a mysqli object: */
	private $connection;
	/* Holds results of a query of sessions from the database: */
	private $sessions;
	/* For when a list of results is printed: */
	private $resultsPerPage;
	/* A User object: */
	private $user;
	/* A variable to hold an object corresponding to a user role: */
	private $specializedInteractor;
	
	public function __construct() {
		$this->connection = new mysqli($GLOBALS["server"], $GLOBALS["dbUsername"], $GLOBALS["dbPassword"]);
		$this->sessions = null;
		$this->resultsPerPage = 2;
		$this->user = new User($this->connection);
		$this->specializedInteractor = null;
		if ($this->user->isLoggedIn()) {
			$this->specializeLogin();
		}
	}
	
	public function getUser() {
		return $this->user;
	}
	
	public function logout() {
		setcookie("username", null, time() - 1);
	}
	
	public function displayAllSessions() {
		$sessionQuery = "select * from {$GLOBALS["database"]}.session;";
		$this->sessions = $this->connection->query($sessionQuery);
		/* Setup for pagination: */
		$i = 0;
		$this->genPageSelector();
		echo("<div class=\"page\" id=page1>");
		while (($row = $this->sessions->fetch_assoc()) != null) {
			/* If a page worth of results has been displayed, start a new "page" div: */
			if ($i > 0 && $i % $this->resultsPerPage == 0) {
                echo("</div><div class=\"page\" id=page" . ($i / $this->resultsPerPage + 1) . ">");
            }
			mediator::displaySession($row);
			$i++; // Keeps track of how many results have been displayed, for pagination.
		}
		echo("</div>"); // End of div containing the very last page worth of results.
	}
	
	public static function displaySession($row) {
		$sessionID = $row["sessionID"];
		$sessionSite = $row["site"];
		$sessionTime = $row["date"] . " at " . $row["time"];
		$sessionSubject = "grade " . $row["gradeLevel"] . " " . $row["subject"];
		$tutorUsername = $row["tutorUsername"];
		$tutorName = $row["tutorName"];
		echo("<div class=\"list-group-item\" id=\"session{$sessionID}\"><h4>{$sessionSite}: {$sessionSubject}</h4><h5>{$sessionTime}</h5><h5>{$tutorName}</h5></div>");
	}
	
	private function specializeLogin() {
		$role = $this->user->getRole();
		if ($role == "attender") {
			$this->specializedInteractor = new Attender($this->user->getUsername(), $this->connection);
		}
		elseif ($role == "tutor") {
			$this->specializedInteractor = new Tutor($this->user->getUsername(), $this->connection);
		}
		elseif ($role == "supervisor") {
			
		}
	}
	
	private function genPageSelector() {
        $numPages = ceil($this->sessions->num_rows / $this->resultsPerPage);
        $i = 1;
        echo("<br><div id=\"buttonholder\"><b>$numPages</b> pages total. ");
        while ($i <= $numPages) {
            echo("<div class=\"pagebutton\" id=\"pagebutton$i\">$i</div> ");
            $i++;
        }
        // echo("<div style=\"float:right;\">
        //           <form id=\"pagejumpform\">Jump to page: <input type=\"text\" size=\"3\" id=\"pagejump\" />
        //           </form>
        //       </div>");
        echo("</div><br>");
    }
	
	/* Attender interface: */
	public function retrieveAttendingSessions() {
		$this->specializedInteractor->retrieveAttendingSessions();
	}
	
	public function willAttend($sessionID) {
		$this->specializedInteractor->willAttend($sessionID);
	}
	
	/* Tutor interface: */
	public function createSession($site, $date, $time, $subject, $gradeLevel, $tutorUsername, $tutorName) {
		$this->specializedInteractor->createSession($site, $date, $time, $subject, $gradeLevel, $tutorUsername, $tutorName);
	}
	
	public function cancelSession($sessionID) {
		$this->specializedInteractor->cancelSession($sessionID);
	}
	
	public function getAttendingUsers($sessionID) {
		$this->specializedInteractor->getAttendingUsers($sessionID);
	}
}

// $_POST["username"] = "ben";
// $_POST["password"] = "lol";
// $_GET['sessionID'] = 4;
// $_GET['function'] = 'displayAll';
$med = new Mediator();

if (isset($_GET['function'])) {
	$function = $_GET['function'];
	if ($function == 'displayAll') {
		$med->displayAllSessions();
	}
	elseif ($function == 'logout') {
		$med->logout();
	}
	elseif ($function == 'retrieveWillAttend') {
		$med->retrieveAttendingSessions();
	}
	elseif ($function == 'indicateWillAttend') {
		$med->willAttend($_GET['sessionID']);
	}
	elseif ($function == 'create') {
		$site = $_GET['site'];
		$date = $_GET['date'];
		$time = $_GET['time'];
		$subject = $_GET['subject'];
		$gradeLevel = $_GET['gradeLevel'];
		$tutorUsername = $med->getUser()->getUsername();
		$tutorName = $med->getUser()->getName();
		$med->createSession($site, $date, $time, $subject, $gradeLevel, $tutorUsername, $tutorName);
	}
	elseif ($function == 'cancel') {
		$sessionID = $_GET['sessionID'];
		$med->cancelSession($sessionID);
	}
	elseif ($function == 'retrieveAttenders') {
		$sessionID = $_GET['sessionID'];
		$med->getAttendingUsers($sessionID);
	}
}
?>
