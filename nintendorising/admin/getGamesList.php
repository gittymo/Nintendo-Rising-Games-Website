<?php
	header("Cache-Control: no-cache, must-revalidate");
	 // Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	$dbconn = mysql_connect("localhost","root","alohamora") or
		die("Failed to connect to host: " . mysql_error());
	
	mysql_select_db("nintendorising") or
		die("Could not find NintendoRising database: " . mysql_error());

	$gamesQuery = "SELECT games.id, games.title, games.publisherID, games.platformID, games.homeURL, " .
		"(SELECT publishers.companyName FROM publishers WHERE publishers.id = games.publisherID) AS pubName, " .
		"(SELECT platforms.description FROM platforms WHERE platforms.platformID = games.platformID) AS platform " .
		"FROM games ";
	if ($_GET["sql"] != null) {
		$gamesQuery .= $_GET["sql"];
	}
	$result = mysql_query($gamesQuery) or
		die("Could not get list list: " . mysql_error());
	
	while ($row = mysql_fetch_assoc($result)) {
		echo "<label class=\"gameName\"
			onClick='copyDetails(\"" . $row["title"] . "\",\"" . 
			$row["homeURL"] . "\"," . $row["publisherID"] . "," . $row["platformID"] . 
			", " . $row["id"] . ")'>" . $row["title"] . "</label>\n";
		echo "<label class=\"platform\">". $row["platform"] . "</label>\n";
		echo "<label class=\"publisher\">" . $row["pubName"] . "</label><br>\n";
	}
	
	mysql_close($dbconn);
?>
