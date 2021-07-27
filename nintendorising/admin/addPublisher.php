<?php
	header("Cache-Control: no-cache, must-revalidate");
	 // Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	$dbconn = mysql_connect("localhost","root","alohamora") or
		die("Failed to connect to host: " . mysql_error());
	
	mysql_select_db("nintendorising") or
		die("Could not find NintendoRising database: " . mysql_error());
		
	$result = mysql_query("INSERT INTO publishers (companyName, www) VALUES (\"" .
		$_GET["companyName"] . "\",\"" . $_GET["homePage"] . "\")") or
		die ("Failed to add publisher: " . mysql_error());
	
	mysql_close($dbconn);
?>
