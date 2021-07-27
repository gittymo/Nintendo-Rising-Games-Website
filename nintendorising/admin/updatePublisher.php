<?php
	header("Cache-Control: no-cache, must-revalidate");
	 // Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	$dbconn = mysql_connect("localhost","root","alohamora") or
		die("Failed to connect to host: " . mysql_error());
	
	mysql_select_db("nintendorising") or
		die("Could not find NintendoRising database: " . mysql_error());
				
	$result = mysql_query("UPDATE publishers SET companyName = '" . 
		$_GET["companyName"] . "', www = '" . $_GET["homePage"] . "' WHERE " .
		"id = " . $_GET["pubID"]) or
		die ("Failed to update publisher: " . mysql_error());
	
	mysql_close($dbconn);
?>
