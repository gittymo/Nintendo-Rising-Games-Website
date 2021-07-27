<?php
	header("Cache-Control: no-cache, must-revalidate");
	 // Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	$dbconn = mysql_connect("localhost","root","alohamora") or
		die("Failed to connect to Nintendo Rising server: " . mysql_error());
	
	mysql_select_db("nintendorising") or
		die("Failed to find Nintendo Rising database: " . mysql_error());
	
	$articleTypeQuery = mysql_query("SELECT description FROM articleTypes WHERE " .
		"typeID=" . $_GET["art"]) or
		die("Failed to get article type: " . mysql_error());
	
	$userDetailsQuery = mysql_query("SELECT realName FROM users WHERE userName='" .
		$_GET["uname"] . "'") or
		die("Failed to get user details: " . mysql_error());
	
	$gameDetailsQuery = mysql_query("SELECT title, " .
		"(SELECT description FROM platforms WHERE platformID=games.platformID) AS platform," .
		"(SELECT companyName FROM publishers WHERE id=games.publisherID) AS company, ".
		"homeURL, players, other FROM games WHERE id=" . $_GET["game"]) or
		die("Failed to get game info: " . mysql_error());
	
	$gameRow = mysql_fetch_assoc($gameDetailsQuery);
	$userRow = mysql_fetch_assoc($userDetailsQuery);
	$artRow = mysql_fetch_assoc($articleTypeQuery);
	// Banner image
	$imageSource = (isset($_GET["img"])) ? "/nintendorising/images/banner/" . $_GET["img"] . ".jpg" : 
		"/nintendorising/temp/" . $_GET["uname"] . "tmp.jpg?" . time();
	echo "<img style=\"position: absolute; left: 0px; top: 0px; width: 768px;" .
		"height:248px\" src=\"" . $imageSource . "\"/>\n";
	// Overlay image
	echo "<img style=\"position: absolute; left: 0px; top: 0px; width: 768px;" .
		"height: 248px\" src=\"/nintendorising/style/articleHeaderOverlay.png\"/>\n";
	// Article type, game title and platform.
	echo "<div style=\"position: absolute; left: 8px; top: 168px; width: 768px;" .
		"height: 72px; font-family: 'Arial','Helvetica',sans-serif; padding: 8px;" .
		"text-align: left; vertical-align: bottom\">\n" .
		"<span style=\"font-size: 1.9em; font-weight: bold; color: rgb(80, 160, 255)\">" .
		$artRow["description"] . ":</span>\n" .
		"<span style=\"font-size: 1.9em; font-weight: bold; color: white\">" . $gameRow["title"] . "</span>\n" .
		"<span style=\"font-size: 1.5em; font-style: italic; color: rgb(80, 160, 255);\">(" .
		$gameRow["platform"] . ")</span><br>\n";
	// Reviewer name
	$labelSpan = "<span style=\"color: rgb(80, 160, 255); margin-right: 4px\">";
	$valueSpan = "<span style=\"color: white; margin-right: 4px\">";
	echo $labelSpan . "by </span>" . $valueSpan . $userRow["realName"] . "</span>\n";
	// Publisher and other miscellaneous information.
	echo $labelSpan . "Publisher:</span><a href=\"" . $gameRow["homeURL"] . 
		"\" style=\"color: white; margin-right: 4px\">" . 
		$gameRow["company"] . "</a>" . $labelSpan . "Players:</span>" .
		$valueSpan . $gameRow["players"] . "</span>" .
		$labelSpan . "Other:</span>" . $valueSpan  . $gameRow["other"] . "</span></div>\n";
	mysql_close($dbconn);
?>
