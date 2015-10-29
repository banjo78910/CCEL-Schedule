<?php
/* - check password in attemptLogin
 * - shade every other session in displayAllSessions
 * - generate page divs in displayAllSessions
 * - make sure all pages redirect to home page and inform the user if their login cookie expired
 */

$server = "localhost";
$dbUsername = "root";
$dbPassword = "4828";
$database = "ccelSchema";

class User {
	/* Holds a mysqli object: */
	private $connection;
	/* Boolean variable: */
	private $loggedIn;
	/* Holds results of a query of sessions from the database: */
	private $sessions;
	/* Holds information about the user from the database: */
	private $userResult;
	/* For when a list of results is printed: */
	private $resultsPerPage;
	/* Stores a more specialized user object after a user logs in: */
	private $specializedObject;
	
	public function __construct() {
		$this->connection = new mysqli($GLOBALS["server"], $GLOBALS["dbUsername"], $GLOBALS["dbPassword"]);
		$this->sessions = null;
		$this->userResult = null;
		$this->resultsPerPage = 15;
		$this->specializedObject = null;
		$this->loggedIn = $this->attemptLogin();
	}
	
	public function displayAllSessions() {
		$sessionQuery = "select * from {$GLOBALS["database"]}.session;";
		$this->sessions = $this->connection->query($sessionQuery);
		while (($row = $this->sessions->fetch_assoc()) != null) {
			$sessionID = $row["sessionID"];
			$sessionSite = $row["site"];
			$sessionTime = $row["date"] . " at " . $row["time"] . " o'clock";
			$sessionSubject = "grade " . $row["gradeLevel"] . " " . $row["subject"];
			$tutorUsername = $row["tutorUsername"];
			$tutorName = $row["tutorName"];
			echo("<div class=\"sessionInfo\">\n\t<h3>{$sessionSite}: {$sessionSubject}</h3>\n\t<h4>{$sessionTime}</h4>\n\t<h4>{$tutorName}</h4>\n</div>\n");
		}
	}
	
	private function attemptLogin() {
		if (isset($_COOKIE["username"])) {
			$userQuery = "select * from {$GLOBALS["database"]}.user where username = '{$_COOKIE["username"]}';";
		}
		elseif (isset($_POST["username"])) {
			$userQuery = "select * from {$GLOBALS["database"]}.user where username = '{$_POST["username"]}';";
		}
		else {
			return false;
		}
		$this->userResult = $this->connection->query($userQuery);
		$this->userResult = $this->userResult->fetch_assoc();
		if ($this->userResult == null) {
			return false;
		}
		/* Check password, and react appropriately: */
		else {
			setcookie("username", $_POST["username"], time() + 3600);
			/* Pass $userResult to other function to determine user's role and other info and set another cookie. */
			return true;
		}
	}
}

// $_POST["username"] = "andyThursdays";
$user = new User();
if (isset($_GET['function'])) {
	$function = $_GET['function'];
	if ($function == 'displayAll') {
		$user->displayAllSessions();
	}
}
?>
