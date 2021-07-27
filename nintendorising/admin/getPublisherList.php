<?php
	header("Cache-Control: no-cache, must-revalidate");
	 // Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	$dbconn = mysql_connect("localhost","root","alohamora") or
		die("Failed to connect to host: " . mysql_error());
	
	mysql_select_db("nintendorising") or
		die("Could not find NintendoRising database: " . mysql_error());

	$pubQuery = "SELECT publishers.id,publishers.companyName,publishers.www," .
		"(SELECT COUNT(*) FROM games WHERE games.publisherID = publishers.id) AS gamesCount" .
		" FROM publishers ";
	if ($_GET["sql"] != null) {
		$pubQuery .= $_GET["sql"];
	}
	$result = mysql_query($pubQuery) or
		die("Could not get publisher list: " . mysql_error());
	
	while ($row = mysql_fetch_assoc($result)) {
		echo "<label class=\"companyName\"
			onClick='copyDetails(\"" . $row["companyName"] . "\",\"" . 
			$row["www"] . "\"," . $row["id"] . ")'>" . $row["companyName"] . "</label>\n";
		echo "<label class=\"homePage\">". $row["www"] . "</label>\n";
		echo "<label class=\"gamesCount\">" . $row["gamesCount"] . "</label><br>\n";
	}
	
	mysql_close($dbconn);
?>
