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
		echo("<div class='role' id='{$this->getRole()}'></div>");
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
	
	public function logout() {
		setcookie("username", null, time() - 1);
		setcookie("role", null, time() - 1);
		$this->loggedIn = false;
	}
	
	private function attemptLogin() {
		if (isset($_POST["username"])) {
			$userQuery = "select * from {$GLOBALS["database"]}.user where username = '{$_POST["username"]}';";
		}
		elseif (isset($_COOKIE["username"])) {
			$userQuery = "select * from {$GLOBALS["database"]}.user where username = '{$_COOKIE["username"]}';";
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
			setcookie("role", $this->userResult["role"], time() + 3600);
			return true;
		}
		else {
			return false;
		}
	}
}
?>
