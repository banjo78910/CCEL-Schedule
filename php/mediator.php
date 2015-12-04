<?php
/* - Make echoed usernames a clickable link that calls a js script, bringing up a pop-up with the user's info
 * - Remove from database sessions that have already occurred.
 * - interface with messaging system
 * - static method to display a username link, which calls some script to display the user's info
 */
include("dbglobals.php");
include("user.php");
include("tutor.php");
include("attender.php");
include("siteLeader.php");
include("session.php");

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
		$this->resultsPerPage = 5;
		$this->user = new User($this->connection);
		$this->specializedInteractor = null;
		if ($this->user->isLoggedIn()) {
			$this->specializeLogin();
		}
	}
	
	public function getUser() {
		return $this->user;
	}
	
	public function displayAllSessions() {
		$sessionQuery = "select sessionID from {$GLOBALS["database"]}.session;";
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
            $session = new Session($row['sessionID']);
            $session->display();
			$i++; // Keeps track of how many results have been displayed, for pagination.
		}
		echo("</div>"); // End of div containing the very last page worth of results.
	}
	
	private function specializeLogin() {
		$role = $this->user->getRole();
		if ($role == "attender") {
			$this->specializedInteractor = new Attender($this->user->getUsername(), $this->connection);
		}
		elseif ($role == "tutor") {
			$this->specializedInteractor = new Tutor($this->user->getUsername(), $this->connection);
		}
		elseif ($role == "siteLeader") {
			$this->specializedInteractor = new SiteLeader($this->user->getUsername(), $this->connection, $this->getUser()->getMessenger());
		}
		elseif ($role == "supervisor") {
			
		}
		elseif ($role == "host") {
			
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
	
	/* Messaging interface: */
	public function getJsonMessages() {
		$this->getUser()->getMessenger()->getJsonMessages();
	}
	
	public function sendJsonMessage($jsonString) {
		$this->getUser()->getMessenger()->sendJsonMessage($jsonString);
	}
	
	public function sendMessage($recipient, $subject, $content) {
		$this->getUser()->getMessenger()->sendMessage($recipient, $subject, $content);
	}
	
	public function getAllowedRecipients() {
		$this->getUser()->getMessenger()->getAllowedRecipients();
	}
	
	/* Attender interface: */
	public function displayAttendingSessions() {
		$this->specializedInteractor->displayAttendingSessions();
	}
	
	public function willAttend($sessionID) {
		$this->specializedInteractor->willAttend($sessionID);
	}
	
	public function cancelAttend($sessionID) {
		$this->specializedInteractor->cancelAttend($sessionID);
	}
	
	/* Tutor interface: */
	public function signUpToTutor($sessionID) {
		$this->specializedInteractor->signUpToTutor($sessionID);
	}
	
	public function cancelTutor($sessionID) {
		$this->specializedInteractor->cancelTutor($sessionID);
	}
	
	public function displaySessions() {
		$this->specializedInteractor->displaySessions();
	}
	
	/* Site leader interface: */
	public function createSession($date, $time, $subject, $gradeLevel) {
		$this->specializedInteractor->createSession($date, $time, $subject, $gradeLevel);
	}
	
	public function createJsonSession($jsonString) {
		$this->specializedInteractor->createJsonSession($jsonString);
	}
	
	public function modifySession($sessionID, $date, $time, $subject, $gradeLevel) {
		$this->specializedInteractor->modifySession($sessionID, $date, $time, $subject, $gradeLevel);
	}
	
	public function cancelSession($sessionID) {
		$this->specializedInteractor->cancelSession($sessionID);
	}
	
	// displaySessions() handled in tutor interface.
	
	public function displaySiteSessions() {
		$this->specializedInteractor->displaySiteSessions();
	}
	
	// signUpToTutor() handled by tutor interface.
	
	// cancelTutor() handled by tutor interface.
}

// $_POST["username"] = "andyThursdays";
// $_POST["password"] = "derp";
// $_GET['sessionID'] = 6;
// $_GET['function'] = 'cancelSession';
// $_GET['recipient'] = 'ben';
// $_GET['subject'] = 'test message';
// $_GET['content'] = 'i hope to god this works ben';
// $_GET['jsonString'] = '{"recipient":"ben","subject":"json test","message":"JSON TEST DURRRRR"}';
$med = new Mediator();
// $med->displayAllSessions();
// $_GET['site'] = "John Hay HS";
// $_GET['date'] = "12-01-2015";
// $_GET['time'] = "8:30PM";
// $_GET['subject'] = "programming";
// $_GET['gradeLevel'] = "12";


if (isset($_GET['function'])) {
	$function = $_GET['function'];
	/* Mediator functions: */
	if ($function == 'displayAllSessions') {
		$med->displayAllSessions();
	}
	elseif ($function == 'logout') {
		$med->getUser()->logout();
	}
	elseif ($function == 'getJsonMessages') {
		$med->getJsonMessages();
	}
	elseif ($function == 'sendJsonMessage') {
		$med->sendJsonMessage($_GET['jsonString']);
	}
	elseif ($function == 'sendMessage') {
		$med->sendMessage($_GET['recipient'], $_GET['subject'], $_GET['content']);
	}
	elseif ($function == 'getAllowedRecipients') {
		$med->getAllowedRecipients();
	}
	/* Attender functions: */
	elseif ($function == 'displayAttendingSessions') {
		$med->displayAttendingSessions();
	}
	elseif ($function == 'willAttend') {
		$med->willAttend($_GET['sessionID']);
	}
	elseif ($function == 'cancelAttend') {
		$med->cancelAttend($_GET['sessionID']);
	}
	/* Tutor functions: */
	elseif ($function == 'signUpToTutor') {
		$med->signUpToTutor($_GET['sessionID']);
	}
	elseif ($function == 'cancelTutor') {
		$med->cancelTutor($_GET['sessionID']);
	}
	elseif ($function == 'displaySessions') {
		$med->displaySessions();
	}
	/* Site leader functions: */
	elseif ($function == 'createSession') {
		$med->createSession($_GET['date'], $_GET['time'], $_GET['subject'], $_GET['gradeLevel']);
	}
	elseif ($function == 'createJsonSession') {
		$med->createJsonSession($_GET['jsonString']);
	}
	elseif ($function == 'modifySession') {
		$med->modifySession($_GET['sessionID'], $_GET['date'], $_GET['time'], $_GET['subject'], $_GET['gradeLevel']);
	}
	elseif ($function == 'cancelSession') {
		$med->cancelSession($_GET['sessionID']);
	}
	elseif ($function == 'displaySiteSessions') {
		$med->displaySiteSessions();
	}
}
?>
