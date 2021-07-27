<?php
	header("Cache-Control: no-cache, must-revalidate");
	 // Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	$dbconn = mysql_connect("localhost","root","alohamora") or
		die("Failed to connect to host: " . mysql_error());
	
	mysql_select_db("nintendorising") or
		die("Could not find NintendoRising database: " . mysql_error());
	
	$queryString = "SELECT users.userName, UNIX_TIMESTAMP(users.joinDate) AS jDate, users.admin, users.warnings, (SELECT COUNT(*) FROM forumPosts WHERE forumPosts.userName=users.userName) AS posts FROM users " . $_GET["sql"];
	$queryString = str_replace("\\","",$queryString);
	$result = mysql_query($queryString) or 
		die("Failed to execute query: " . mysql_error());
	
	while($row = mysql_fetch_assoc($result)) {
		echo "<label class=\"userName\">" . $row["userName"] . "</label>\n";
		echo "<label class=\"joinDate\">" . date("d-m-Y",$row["jDate"]) . "</label>\n";
		echo "<select class=\"admin\">\n";
		$isAdmin = ($row["admin"] == 1);
		echo "<option value=\"1\"";
		if ($isAdmin) echo " selected>Yes</option>\n";
		echo "<option value=\"0\"";
		if (!$isAdmin) echo " selected>No</option>\n";
		echo "</select>\n";
		echo "<label class=\"status\">";
		if ($row["warnings"] == null || $row["warnings"] < 1) {
			echo "Clean";
		} else if ($row["warnings"] < 3) {
			echo $row["warnings"] . " warnings";
		} else if ($row["warnings"] == 3) {
			echo "Blocked";
		} else if ($row["warnings"] > 3) {
			echo "BANNED!";
		}
		echo "</label>\n";
		echo "<label class=\"posts\">" . $row["posts"] . "</label><br>\n";
	}
	
	mysql_close($dbconn);
?>	
