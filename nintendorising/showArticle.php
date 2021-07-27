<?php
	session_start();
	
	require "./renderArticleSection.php";
	
	$dbconn = mysql_connect("localhost","root","alohamora") or
		die("Failed to connect to Nintendo Rising server");
		
	mysql_select_db("nintendorising") or
		die("Failed to find Nintendo Rising database");
	
	$articleDetails = mysql_query("SELECT (SELECT title FROM games WHERE id = articles.gameID) AS gameName, ".
		"(SELECT description FROM articleTypes WHERE typeID = articles.type) AS typeName, " .
		"subtitle, type, userName, gameID FROM articles WHERE id=" . $_GET["art"]) or
		die("Failed to get article details: " . mysql_error());
	$artRow = mysql_fetch_array($articleDetails);
	
	$articleSections = mysql_query("SELECT * FROM articleSections " .
		"WHERE articleID = " . $_GET["art"]) or
		die("Failed to get article sections: " . mysql_error());
?>
<html lang="en" dir="ltr">
<head>
<?php
	echo "<title>Nintendo Rising " . $artRow["typeName"] . ": " . $artRow["gameName"] . "</title>\n";
?>
<link rel="stylesheet" type="text/css" href="./style/sidebar.css"/>
<link rel="stylesheet" type="text/css" href="./style/default.css"/>
<script type="text/javascript" src="./scripts/utils.js"></script>
<script type="text/javascript" src="./scripts/userMan.js"></script>
<script type="text/javascript" src="./scripts/sidebar.js"></script>
<script type="text/javascript">
function refreshBanner() {
	ajaxBannerRequest = getAjaxRequest();
	if (ajaxBannerRequest) {
		<?php
			echo "var urlString = \"./createArticleBanner.php?uname=\" + escape('" . 
			$artRow["userName"] . "') + \"&game=" . $artRow["gameID"] .
				"&art=" . $artRow["type"] . "&img=" . $_GET["art"] . "\"\n";
		?>
		ajaxBannerRequest.open("GET",urlString,true);
		ajaxBannerRequest.onreadystatechange = showBanner;
		ajaxBannerRequest.send(null);
	}
}
			
function showBanner() {
	getElement("articleBanner").innerHTML = ajaxBannerRequest.responseText;
}
</script>
<style type="text/css" rel="stylesheet">						
body		{	background: url("./style/BlueBG1680.png") repeat-y;	}													
</style>
</head>
<body onLoad="refreshBanner(); getLoginStatus()">
<div id="main">
<div id="articleBanner" style="position: relative; text-align: center; height: 248px"></div>
<div id="sideBar" style="margin-top: 1em; padding-right: 0px">
<h1>USER LOGIN</h1>
<div id="loginBox" style="text-align: center"></div>
<h1>GAME SCREENSHOTS</h1>
<div class="sideBox" id="mediaLatest"></div>
</div>

<div id="mainContent" style="width: 540px">
<p style="padding: 0px; margin: 1em 8px; font-weight: bold; font-size: 1.4em"><?php echo $artRow["subtitle"]; ?></p>
<?php
	while ($secRow = mysql_fetch_array($articleSections)) {
		echo "<div style=\"text-align: justify; margin-bottom: 1em\">\n";
		echo renderArticleSection($secRow);
		echo "</div>\n";
	}
	
	mysql_close($dbconn);
?>
</div>
</div>
</body>
</html>
