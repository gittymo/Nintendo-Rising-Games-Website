<?php
	header("Cache-Control: no-cache, must-revalidate");
	 // Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	$dbconn = mysql_connect("localhost","root","alohamora") or
		die("Failed to connect to host: " . mysql_error());
	
	mysql_select_db("nintendorising") or
		die("Could not find NintendoRising database: " . mysql_error());
		
	$query = "INSERT INTO games (title, publisherID, platformID, homeURL) VALUES (\"" .
		$_GET["gameName"] . "\", " . $_GET["pubID"] . ", " . $_GET["platform"] .
		", \"" . $_GET["homePage"] . "\")";
	$result = mysql_query($query) or
		die ("Failed to add game: " . mysql_error());
	
	mysql_close($dbconn);
?>
