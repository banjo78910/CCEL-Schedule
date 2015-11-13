<?php
class User {
	/* Holds a mysqli object: */
	private $connection;
	/* Boolean variable: */
	private $loggedIn;
	/* Holds information about the user from the database: */
	private $userResult;
	
	public function __construct($connection) {
		$this->connection = $connection;
		$this->userResult = null;
		$this->loggedIn = $this->attemptLogin();
	}
	
	public function isLoggedIn() {
		return $this->loggedIn;
	}
	
	public function getUsername() {
		return $this->userResult["username"];
	}
	
	public function getName() {
		return $this->userResult["firstName"] . " " . $this->userResult["lastName"];
	}
	
	public function getRole() {
		return $this->userResult["role"];
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
		/* Check password: */
		elseif ((isset($_POST["password"]) && ($_POST["password"] == $this->userResult["password"])) || isset($_COOKIE["username"])) {
			setcookie("username", $this->userResult["username"], time() + 3600);
			return true;
		}
		else {
			return false;
		}
	}
}
?>
