<?php
	header("Cache-Control: no-cache, must-revalidate");
	 // Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	session_start();
	
	$reqType = $_GET["logreq"];
	if (strcmp($reqType,"logout") == 0) {
		unset($_SESSION["userName"]);
		if (isset($_SESSION["admin"])) {
			unset($_SESSION["admin"]);
		}
	}
	
	if (strcmp($reqType,"login") == 0) {
		$dbconn = mysql_connect("localhost","root","alohamora") or
			die("Failed to connect to host: " . mysql_error());
	
		mysql_select_db("nintendorising") or
			die("Could not find NintendoRising database: " . mysql_error());
	
		$userQuery = "SELECT * FROM users WHERE userName='" . $_GET["uname"] .
			"'";

		$isUser = mysql_query($userQuery) or
			die("Query failed: " . mysql_error());
	
		if (mysql_num_rows($isUser) == 1) {
			$row = mysql_fetch_assoc($isUser);
			if (crypt($_GET["pass"],$row["password"]) == $row["password"]) {
				$_SESSION["userName"] = $_GET["uname"];
				$_SESSION["admin"] = $row["admin"];
			}
		}
	
		mysql_close($dbconn);
	}
	
	if (isset($_SESSION["userName"])) {
		echo "<p>Welcome " . $_SESSION["userName"] . "</p>\n";
		if (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1) {
			echo "<p><a href=\"/nintendorising/admin/index.php\">Admin Menu</a></p>\n";
		}
		echo "<button onClick=\"logout()\">Log Out</button>\n";
	} else {
		echo "<label for=\"loginUserName\">User Name:</label>\n";
		echo "<input style=\"width: 9.5em\" id=\"loginUserName\" type=\"text\" maxlength=\"32\"/>\n";
		echo "<label for=\"loginPassword\">Password:</label>\n";
		echo "<input style=\"width: 9.5em\" id=\"loginPassword\" style=\"margin-top: 4px\" type=\"password\" maxlength=\"32\"/>\n";
		echo "<button style=\"width: 10em\" onClick=\"login()\">Log In</button><br>\n";
		echo "<a href=\"/nintendorising/userDetails.php\">Register Here!</a>\n";
	}
?>		
