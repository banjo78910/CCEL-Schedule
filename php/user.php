<?php
/* - shade every other session in displayAllSessions
 * - generate page divs in displayAllSessions
 * - make sure all pages redirect to home page and inform the user if their login cookie expired
 */
include("tutor.php");
include("attender.php");
include("supervisor.php");

$server = "localhost";
$dbUsername = "root";
$dbPassword = "root";
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
	public $specializedObject;
	
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
			user::displaySession($row);
		}
	}
	
	public function logout() {
		setcookie("username", null, time() - 1);
	}
	
	public static function displaySession($row) {
		$sessionID = $row["sessionID"];
		$sessionSite = $row["site"];
		$sessionTime = $row["date"] . " at " . $row["time"] . " o'clock";
		$sessionSubject = "grade " . $row["gradeLevel"] . " " . $row["subject"];
		$tutorUsername = $row["tutorUsername"];
		$tutorName = $row["tutorName"];
		echo("<a href='#' class=\"sessionInfo list-group-item\">\n<button class=\"btn btn-default btn-add-to-sessions pull-right\">\nAdd to My Sessions</button>\t<h4>{$sessionSite}: {$sessionSubject}</h4>\n\t<p>{$sessionTime}<p>\n\t<p>{$tutorName}</p>\n</a>\n");
	}
	
	private function attemptLogin() {
		if (isset($_COOKIE["username"])) {
			$userQuery = "select * from {$GLOBALS["database"]}.user where username = '{$_COOKIE["username"]}';";
		}
		elseif (isset($_POST["username"])) {
			$userQuery = "select * from {$GLOBALS["database"]}.user where username = '{$_POST["username"]}';";
		}
		else {
			//echo("login failed");
			return false;
		}
		$this->userResult = $this->connection->query($userQuery);
		$this->userResult = $this->userResult->fetch_assoc();
		if ($this->userResult == null) {
			//echo("login failed");
			return false;
		}
		/* Check password, and react appropriately: */
		elseif ((isset($_POST["password"]) && ($_POST["password"] == $this->userResult["password"])) || isset($_COOKIE["username"])) {
			setcookie("username", $this->userResult["username"], time() + 3600);
			$this->specializeLogin($this->userResult);
			//echo("login successful");
			return true;
		}
		else {
			//echo("login failed");
			return false;
		}
	}
	
	private function specializeLogin($userResult) {
		$role = $userResult["role"];
		if ($role == "attender") {
			$this->specializedObject = new attender($userResult);
		}
		elseif ($role == "tutor" || $role == "siteLeader") {
			$this->specializedObject = new tutor($userResult);
		}
		elseif ($role == "supervisor") {
			$this->specializedObject = new supervisor($userResult);
		}
		elseif ($role == "host") {
			// Will figure out later.
		}
	}
}

$_POST["username"] = "student1";
$_POST["password"] = "omg";
$user = new User();
//$user->displayAllSessions();
// $user->specializedObject->retrieveAttendingSessions();
if (isset($_GET['function'])) {
	$function = $_GET['function'];
	if ($function == 'displayAll') {
		$user->displayAllSessions();
	}
	elseif ($function == 'logout') {
		$user->logout();
	}
	elseif ($function == 'retrieveWillAttend') {
		$user->specializedObject->retrieveAttendingSessions();
	}
	elseif ($function == 'indicateWillAttend') {
		$user->specializedObject->willAttend($_GET['sessionID']);
	}
}
?>

<div>
	<?php $user->displayAllSessions(); ?>
</div>
