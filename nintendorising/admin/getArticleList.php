<?php
	header("Cache-Control: no-cache, must-revalidate");
	 // Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	$dbconn = mysql_connect("localhost","root","alohamora") or
		die("Failed to connect to host: " . mysql_error());
	
	mysql_select_db("nintendorising") or
		die("Could not find NintendoRising database: " . mysql_error());

	$articleQuery = "SELECT articles.id, articles.subtitle, articles.editTime, ".
		"articles.type, articles.gameID, " .
		"(SELECT platformID FROM games WHERE id = articles.gameID) AS platID, ".
		"(SELECT description FROM articleTypes WHERE typeID = articles.type) AS typeName, ".
		"(SELECT title FROM games WHERE id = articles.gameID) AS gameName, " .
		"(SELECT description FROM platforms WHERE platformID = platID) AS platName FROM articles ";
	if ($_GET["sql"] != null) {
		$articleQuery .= $_GET["sql"];
	}
	$result = mysql_query($articleQuery) or
		die("Could not get list list: " . mysql_error());
	
	while ($row = mysql_fetch_assoc($result)) {
		$safeSubtitle = addslashes($row["subtitle"]);
		/*$str = str_replace(array('\\','\''),array('\\\\','\\\''),$str);
		$str = "'".$str."'";*/
		
		echo "<label class=\"gameName\"
			onClick=\"copyDetails(" . $row["id"] . ",'" .$safeSubtitle . "'," . 
			$row["type"] . "," . $row["gameID"] . ")\">" . $row["gameName"] . "</label>\n";
		echo "<label class=\"platform\">". $row["platName"] . "</label>\n";
		echo "<label class=\"artType\">" . $row["typeName"] . "</label>\n";
		echo "<label class=\"editTime\">" . $row["editTime"] . "</label><br>\n";
	}
	
	mysql_close($dbconn);
?>
