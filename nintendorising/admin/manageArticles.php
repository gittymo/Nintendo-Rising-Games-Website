<?php
	session_start();
?>
<html lang="en" dir="ltr">
<head>
<title>Nintendo Rising: Administration Panel (Manager Articles)</title>
<link type="text/css" rel="stylesheet" href="../style/admin.css"/>
<style type="text/css">
label	{ 	width: 7em; }
label.gameName	{	width: 20em; }
label.platform	{	width: 7em;	}
label.artType	{	width: 7em;	}
label.editTime	{	width: 10em; text-align: right}
input	{	margin-top: 4px; }
</style>
<script type="text/javascript" src="../scripts/utils.js"></script>
<script type="text/javascript" src="../scripts/userMan.js"></script>
<script type="text/javascript">
var ajaxListReq = null;
var ajaxBannerReq = null;
var sortOrder = new Array(true, true, true, true);
var lastSQLQuery = null;
var bannerUpdate = false;
var splashUpdate = false;
function updateBannerImage() {
	var fileInputElement = getElement("imageURL");
	uploadFile(fileInputElement.value, fileInputElement, "storeTempImage.php?swidth=768&crop=768x248");
	bannerUpdate = true;
}

function updateSplashImage() {
	var fileInputElement = getElement("splashURL");
	uploadFile(fileInputElement.value, fileInputElement, "storeTempImage.php?swidth=530&crop=530x302&suffix=spl");
	splashUpdate = true;
}

function uploadDone(responseText) {	
	if (bannerUpdate) {
		setTimeout(refreshBanner,2000);
		bannerUpdate = false;
	}
}

function refreshBanner() {
	ajaxBannerRequest = getAjaxRequest();
	if (ajaxBannerRequest) {
		<?php
			echo "var urlString = \"../createArticleBanner.php?uname=\" + escape('" 
				. $_SESSION["userName"] .
				"') + \"&game=\" + getElement('gameName').value + \"" .
				"&art=\" + getElement('articleType').value;";
		?>
		ajaxBannerRequest.open("GET",urlString,true);
		ajaxBannerRequest.onreadystatechange = showBanner;
		ajaxBannerRequest.send(null);
	}
}
			
function showBanner() {
	getElement("articleBanner").innerHTML = ajaxBannerRequest.responseText;
}

function submitNew() {
	getElement("articleID").value = null;
	getElement("headerForm").submit();
}

function submitUpdate() {
	getElement("headerForm").submit();
}

function fetchArticleList(SQLQuery) {
	if (SQLQuery == null) {
		SQLQuery = "ORDER BY gameName ASC";
	}
	lastSQLQuery = SQLQuery;

	ajaxListReq = getAjaxRequest();
	if (ajaxListReq != null) {
		var urlString = "./getArticleList.php?sql=" + escape(SQLQuery);
		ajaxListReq.open("GET",urlString,true);
		ajaxListReq.send(null);
		ajaxListReq.onreadystatechange=displayArticleList;
	}
}

function displayArticleList() {
	var header="<label class=\"gameName\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchArticleList(\"ORDER BY gameName " + 
		((sortOrder[0] = !sortOrder[0]) ? "DESC" : "ASC") + "\")'>Game Title</label>";
	header += "<label class=\"platform\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchArticleList(\"ORDER BY platName " + 
		((sortOrder[1] = !sortOrder[1]) ? "DESC" : "ASC") + "\")'>Platform</label>";
	header += "<label class=\"artType\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchArticleList(\"ORDER BY typeName " + 
		((sortOrder[2] = !sortOrder[2]) ? "DESC" : "ASC") + "\")'>Article Type</label>";
	header += "<label class=\"editTime\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchArticleList(\"ORDER BY editTime " + 
		((sortOrder[3] = !sortOrder[3]) ? "DESC" : "ASC") + "\")'>Edit Time</label><br>";
	getElement("articlesList").innerHTML = header + ajaxListReq.responseText;
}

function copyDetails(articleID, subtitle, typeID, gameID) {
	getElement("articleID").value = articleID;
	getElement("subtitle").value = subtitle;
	var gameList = getElement("gameName");
	for (var i = 0; i < gameList.length; i++) {
		if (gameList[i].value == gameID) {
			gameList[i].selected = true;
		}
	}
	var typeList = getElement("articleType");
	for (var i = 0; i < typeList.length; i++) {
		if (typeList[i].value == typeID) {
			typeList[i].selected = true;
		}
	}
	getElement("addButton").disabled = true;
	getElement("updateButton").disabled = false;
}
</script>
</head>
<?php
	if (isset($_SESSION["userName"]) && $_SESSION["admin"] == 1) {
?>
<body onLoad="fetchArticleList(null)">
<div id="main">
<h1>Adminstration: Manage Articles</h1>
<p>You can add, view, edit and remove details of any articles featured on this 
site using this page.</p>
<a href="./index.php">To return to the main menu click here</a>
<h2>Articles List</h2>
<div id="articlesList"></div>
<h2>Article Details</h2>
<form id="headerForm" method="POST" action="editArticle.php">
<input id="articleID" name="articleID" type="hidden" />
<label for="gameName">Game Name:</label>
<select id="gameName" name="gameName" style="width: 30em" onChange="refreshBanner()"/>
<?php
	$dbconn = mysql_connect("localhost", "root", "alohamora") or 
		die("Failed to connect to database server: " . mysql_error());
	
	mysql_select_db("nintendorising") or 
		die("Failed to find NintendoRising database: " . mysql_error());
		
	$gamesList = mysql_query("SELECT id, title, (SELECT description FROM platforms WHERE games.platformID = platformID) AS platform FROM games ORDER BY title ASC") or
		die("Failed to fetch games list: " . mysql_error());
		
	while ($row = mysql_fetch_array($gamesList)) {
		echo "<option value=" . $row["id"] . ">" . $row["title"] . " (" . $row["platform"] . ")</option>\n";
	}
?>
</select>
<label for="articleType">Article Type:</label>
<select id="articleType" name="articleType" style="width: 12em" onChange="refreshBanner()">
<?php
	$publisherRecords = mysql_query("SELECT description, typeID FROM articleTypes ORDER BY description ASC") or
		die("Query failed: " . mysql_error());
	
	while ($row = mysql_fetch_array($publisherRecords)) {
		echo "<option value=" . $row["typeID"] . ">" . $row["description"] . "</option>\n";
	}
	
	mysql_close($dbconn);
?>
</select><br>
<label for="subtitle">Subtitle:</label>
<input id="subtitle" name="subtitle" type="text" maxlength="192" style="width: 52.5em"/></br>
<label for="imageURL">Header Image:</label>
<input id="imageURL" type="text" style="width: 23em" onchange="updateBannerImage()"/><br>
<label for="splashURL">Splash Image:</label>
<input id="splashURL" type="text" style="width: 23em" onchange="updateSplashImage()"/>
<button type="submit" onClick="submitUpdate()" id="updateButton" style="width: 152px; margin: 4px 4px 0px 3em" disabled>Update Article</button>
<button type="submit" onClick="submitNew()" id="addButton" style="width: 152px;">Add New Article</button>
</form>
<h2>Article Header Preview</h2>
<div id="articleBanner" style="position: relative; text-align: center; height: 256px">
No article details have been changed
</div>
<?php
	} else {
?>
<body>
<div id="main">
<h1>Adminstration: Manage Articles</h1>
<p>You are not currently logged in.  Please login using the box below:</p>
<div id="loginBox"></div>
<script language="javascript">
getLoginStatus();
</script>
<?php } ?>
</div>
</body>
</html>
