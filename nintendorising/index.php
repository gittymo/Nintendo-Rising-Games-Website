<?php
	session_start();
	
	require "./renderArticleSection.php";
	require "./makeImageArticleLink.php";
	
	$dbconn = mysql_connect("localhost","root","alohamora") or
		die("Failed to connect to Nintendo Rising server");
		
	mysql_select_db("nintendorising") or
		die("Failed to find Nintendo Rising database");
	
	$gameDetailsQuery = "SELECT subtitle, id, " .
		"(SELECT title FROM games WHERE id=articles.gameID) AS gameTitle, " .
		"(SELECT description FROM articleTypes WHERE typeID=articles.type) AS artType " .
		"FROM articles ORDER BY editTime DESC LIMIT 4";
	$result = mysql_query($gameDetailsQuery) or
		die("SQL error: " . mysql_error());
	
	$artType = array();
	$gameTitle = array();
	$subtitle = array();
	$image = array();
	$artID = array();
	$index = 0;
	while ($row = mysql_fetch_assoc($result)) {
		$artType[$index] = $row["artType"];
		$gameTitle[$index] = $row["gameTitle"];
		$image[$index] = "/nintendorising/images/splash/" . $row["id"] . ".jpg";
		$subtitle[$index] = $row["subtitle"];
		$artID[$index] = $row["id"];
		$index++;
	}
?>
<html lang="en" dir="ltr">
<head>
<title>Nintendo Rising: Homepage</title>
<link rel="stylesheet" type="text/css" href="./style/FeaturesSlideshow.css"/>
<link rel="stylesheet" type="text/css" href="./style/sidebar.css"/>
<link rel="stylesheet" type="text/css" href="./style/default.css"/>
<script type="text/javascript" src="./scripts/utils.js"></script>
<script type="text/javascript" src="./scripts/userMan.js"></script>
<script type="text/javascript" src="./scripts/FeaturesSlideshow.js"></script>
<script type="text/javascript" src="./scripts/sidebar.js"></script>
<script type="text/javascript">
function showArticle(artID) {
	window.location = "./showArticle.php?art=" + artID;
}
</script>
<style type="text/css" rel="stylesheet">						
body		{	background: url("./style/BlueBG1680.png") repeat-y;	}
span.hoverText:hover	{	text-decoration: underline;	}
a.ArticleLink	{	color: black;
					text-decoration: none;
				}
				
a.ArticleLink:visited	{	color: black;
							text-decoration: none;
						}

a.ArticleLink:hover		{	text-decoration: underline;	}														
</style>
</head>
<?php
	echo "<body onLoad=\"initSlideshow(" . $index . "); " .
	"setInterval(animateSlideshow, 20); setInterval(fetchLastestForumPosts, 30000); " .
	"fetchLastestForumPosts();getLoginStatus()\">\n";
?>
<div id="main">
<div id="sideBar">
<div class="sideBox">
<h1>USER LOGIN</h1>
<div id="loginBox" style="text-align: center">
</div>
</div>
<div class="sideBox" id="forumLatest">
<h1>NO FORUM POSTS!</h1>
</div>
<div class="sideBox" id="mediaLatest">
<h1>LATEST MEDIA</h1>
</div>
</div>

<div id="mainContent" style="width: 540px">
<h1>LASTEST FEATURES</h1>
<div id="slideshowContainer">
<?php
	$slideID = 1; $slidePos = 4;
	for ($index = 0; $index < count($gameTitle); $index++) {
		echo "<div class=\"slide\" id=\"slide" . ($slideID++) . "\" " .
			"style=\"background: url('" . $image[$index] . "') no-repeat;" .
			"left: " . $slidePos . "px\" onClick=\"showArticle(" . $artID[$index] . ")\">\n";
		echo "<div class=\"slideTextBox\">\n" .
			"<span style=\"font-size: 1.4em; font-weight: bold; color: rgb(64,128,255)\">" . 
			$artType[$index] . ": </span>\n" .
			"<span class=\"hoverText\" style=\"font-size: 1.4em; font-weight: bold; color: white\">" .
			$gameTitle[$index] . "</span><br>\n"; 
		echo "<span>" . $subtitle[$index] . "</span>\n";
		echo "</div></div>\n";
		$slidePos += 540;
	}
	
	echo "<div id=\"thumbsBox\">\n";
	for ($index = 0; $index < count($gameTitle); $index++) {
		echo "<img src=\"" . $image[$index] . "\" onClick=\"setSlideToShow(this," . 
			($index+1) .");\" title=\"" . $artType[$index] . ": " . $gameTitle[$index] . "\"/>\n";
	}
	echo "</div>\n";
?>
<img class="slideShowBorder" src="./style/FeaturesSlideshowLB.png" style="left:0px"/>
<img class="slideShowBorder" src="./style/FeaturesSlideshowRB.png" style="left:526px; width: 10px;"/>
</div>
</div>
<h1>FEATURES</h1>
<?php	
	$articlesQuery = mysql_query("SELECT *,(SELECT description FROM articleTypes WHERE typeID=articles.type) AS typeName, (SELECT title FROM games WHERE id=articles.gameID) AS gameName, (SELECT realName FROM users WHERE userName=articles.userName) AS realName, UNIX_TIMESTAMP(editTime) AS timeStamp FROM articles ORDER BY editTime DESC LIMIT 8") or
		die("Failed to get articles: " . mysql_error());
	while ($row = mysql_fetch_array($articlesQuery)) {
		$introQuery = mysql_query("SELECT * FROM articleSections WHERE articleID = " . $row["id"] . " ORDER BY displayOrder ASC LIMIT 1") or
			die("Failed to get article content.");
		if (mysql_num_rows($introQuery) > 0) {
			echo "<div style=\"width: 524px; background: url('./style/featuresListHeader.png') repeat-x;" .
				"font-family: 'Arial', 'Helvetica', sans-serif; font-size: 0.8	em;" .
				"padding: 2px 6px; color: rgb(64,64,64)\">\n";
			echo date("l jS F Y - H:i",$row["timeStamp"]);
			echo "</div>\n<div style=\"font-family: 'Arial', 'Helvetica', sans-serif; font-size: 1.6em; margin: 4px 0px\">\n";
			echo "<span style=\"color: rgb(40, 80, 255)\">" . $row["typeName"] . ": </span>\n";
			echo "<a class=\"ArticleLink\" href=\"./showArticle.php?art=" .
				$row["id"] . "\">" . $row["gameName"] . "</a>\n";
			echo "</div>\n";
			$secRow = mysql_fetch_array($introQuery);
			echo "<div class=\"featureBox\" style=\"width: 535px; height: 144px; margin-bottom: 1em; text-align: justify; font-size: 0.9em\">\n";
			echo makeImageArticleLink(renderArticleSection($secRow,300), $row["id"]);
			echo "</div>\n";
		}
	}
	

	mysql_close($dbconn);
?>
</div>
</body>
</html>
