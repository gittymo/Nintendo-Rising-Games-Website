<html lang="en" dir="ltr">
<head>
<title>Nintendo Rising: Administration Panel (Manager Games)</title>
<link type="text/css" rel="stylesheet" href="../style/admin.css"/>
<script type="text/javascript" src="../scripts/utils.js"></script>
<script type="text/javascript">
var ajaxListReq = null;
var sortOrder = new Array(true, true, true);
var lastSQLQuery = null;

function addGame() {
	var gameName = escape(getElement("gameName").value);
	var homePage = escape(getElement("homePage").value);
	var ajaxAddReq = getAjaxRequest();
	if (ajaxAddReq != null) {
		var urlString = "./addGame.php?gameName=" + gameName +
			"&homePage=" + homePage + "&pubID=" + getElement("pubID").value +
			"&platform=" + getElement("platform").value;
		ajaxAddReq.open("GET",urlString,true);
		ajaxAddReq.send(null);
		getElement("gameName").value = "";
		getElement("homePage").value = "";
	}
}

function fetchGamesList(SQLQuery) {
	if (SQLQuery == null) {
		SQLQuery = "ORDER BY title ASC";
	}
	lastSQLQuery = SQLQuery;

	ajaxListReq = getAjaxRequest();
	if (ajaxListReq != null) {
		var urlString = "./getGamesList.php?sql=" + escape(SQLQuery);
		ajaxListReq.open("GET",urlString,true);
		ajaxListReq.send(null);
		ajaxListReq.onreadystatechange=displayGamesDetails;
	}
}

function displayGamesDetails() {
	var header="<label class=\"gameName\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchGamesList(\"ORDER BY title " + 
		((sortOrder[0] = !sortOrder[0]) ? "DESC" : "ASC") + "\")'>Game Title</label>";
	header += "<label class=\"platform\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchGamesList(\"ORDER BY platformID " + 
		((sortOrder[1] = !sortOrder[1]) ? "DESC" : "ASC") + "\")'>Platform</label>";
	header += "<label class=\"publisher\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchGamesList(\"ORDER BY publisherID " + 
		((sortOrder[2] = !sortOrder[2]) ? "DESC" : "ASC") + "\")'>Publisher</label><br>";
	getElement("gamesList").innerHTML = header + ajaxListReq.responseText;
}

function copyDetails(gameTitle, homePage, pubID, platformID, gameID) {
	getElement("gameName").value = gameTitle;
	getElement("homePage").value = homePage;
	getElement("gameID").value = gameID;
	var pubList = getElement("pubID");
	for (var i = 0; i < pubList.length; i++) {
		if (pubList[i].value == pubID) {
			pubList[i].selected = true;
		}
	}
	var platList = getElement("platform");
	for (var i = 0; i < platList.length; i++) {
		if (platList[i].value == platformID) {
			platList[i].selected = true;
		}
	}
	getElement("addButton").disabled = true;
	getElement("updateButton").disabled = false;
}

function refreshPublisherDetails() {
	fetchGamesList(lastSQLQuery);
}
</script>
<style type="text/css">
label	{	width: 14ex;	}
input	{	width: 30ex;	}
select	{	width: 30ex;	}
label.gameName	{	width: 40ex; vertical-align: top;	}
label.platform	{	width: 20ex; vertical-align: top;	}
label.publisher	{	width: 20ex; vertical-align: top;	}
</style>
</head>
<body onLoad="getElement('updateButton').disabled = true; fetchGamesList(null)">
<div id="main">
<h1>Adminstration: Manage Games</h1>
<p>You can add, view, edit and remove details of any games features in articles 
on this site from this page.</p>
<a href="./index.php">To return to the main menu click here</a>
<h2>Games List</h2>
<div id="gamesList"></div>
<h2>Game Details</h2>
<input id="gameID" type="hidden" />
<label for="gameName">Game Name:</label>
<input id="gameName" type="text" maxlength="48"/>
<label for="pubID">Publisher:</label>
<select id="pubID">
<?php
	$dbconn = mysql_connect("localhost", "root", "alohamora") or 
		die("Failed to connect to database server: " . mysql_error());
	
	mysql_select_db("nintendorising") or 
		die("Failed to find NintendoRising database: " . mysql_error());
	
	$publisherRecords = mysql_query("SELECT companyName, id FROM publishers ORDER BY companyName ASC") or
		die("Query failed: " . mysql_error());
	
	while ($row = mysql_fetch_array($publisherRecords)) {
		echo "<option value=" . $row["id"] . ">" . $row["companyName"] . "</option>\n";
	}
?>
</select><br>
<label for="homePage">Homepage:</label>
<input id="homePage" type="text" maxlength="64"/>
<label for="platform">Platform:</label>
<select id="platform">
<?php
	$platformRecords = mysql_query("SELECT * FROM platforms ORDER BY description ASC") or
		die("Query failed: ". mysql_error());
	
	while ($row = mysql_fetch_assoc($platformRecords)) {
		echo "<option value=" . $row["platformID"] . ">" . $row["description"] . "</option>\n";
	}
		
	mysql_close($dbconn);
?>
</select><br>
<label for="pointsUK">EU Points:</label>
<input id="pointsUK" type="text" maxlength="4"/>
<label for="pointsUS">US Points:</label>
<input id="pointsUS" type="text" maxlength="4"/><br>
<label for="releaseUK">UK Release:</label>
<input id="releaseUK" type="text" maxlength="10"/>
<label for="releaseUS">US Release:</label>
<input id="releaseUS" type="text" maxlength="10"/>
<button onClick="updateDetails()" id="updateButton" style="width: 192px;margin-left: 372px; margin-top: 4px">Update Details</button>
<button onClick="addGame()" id="addButton" style="width: 192px;">Add as New Game</button>
</div>
</body>
</html>
