<?php
	session_start();
	require "./imageLib.php";
	
	$dbconn = mysql_connect("localhost", "root", "alohamora") or
		die("Failed to connect to database server");
	
	mysql_select_db("nintendorising") or
		die("Could not find Nintendo Rising database");
		
	$articleID = null;
	if ($_POST["articleID"] == null) {
		// Get the number of rows in the articles table
		$result = mysql_query("SELECT MAX(id) AS largestID FROM articles")
			or die("Failed to get article count");
		$row = mysql_fetch_array($result);
		$articleID = $row["largestID"] + 1;
		$insertQuery = "INSERT INTO articles (id, type, gameID, userName, subtitle, editTime) " .
			"VALUES (" . $articleID . ", " . $_POST["articleType"] . "," . $_POST["gameName"] . ",'" .
			$_SESSION["userName"] . "','" . $_POST["subtitle"] . "', NOW())";
		$result = mysql_query($insertQuery);
		if (!$result) {
			echo mysql_error();
			if (mysql_errno() == 1062) {
				$findQuery = mysql_query("SELECT id FROM articles WHERE type=" .
					$_POST["articleType"] . " AND gameID=" . $_POST["gameName"]) or
					die("Failed to find original record: " . mysql_error());
				$row = mysql_fetch_array($findQuery);
				$result = mysql_query("UPDATE articles SET subtitle='" . $_POST["subtitle"] .
					"',editTime=NOW(),userName='" . $_SESSION["userName"] . "' WHERE " .
					"id=" . $row["id"]) or
					die("Failed to update article header: " . mysql_error());
				$articleID = $row["id"];
			}
		}
	} else {
		if (isset($_POST["subtitle"])) {
			$result = mysql_query("UPDATE articles SET subtitle='" . $_POST["subtitle"] .
				"',editTime=NOW(),userName='" . $_SESSION["userName"] . "', ".
				"type=" . $_POST["articleType"] . " WHERE " .
				"id=" . $_POST["articleID"]) or
				die("Failed to update article header: " . mysql_error());
		} else {
			$result = mysql_query("UPDATE articles SET editTime=NOW(),userName='" . 
				$_SESSION["userName"] . "' WHERE id=" . $_POST["articleID"]) or
				die("Failed to update edit time: " . mysql_error());
		}
		$articleID = $_POST["articleID"];
	}
	
	$result = mysql_query("SELECT * FROM articles WHERE id=" . $articleID) or
				die("Failed to get article details: " . mysql_error());
	$articleRow = mysql_fetch_array($result);
	
	// Move a newly uploaded banner image. to it the banners directory.
	$tempFiles = "/var/www/nintendorising/temp/" . $_SESSION["userName"];
	if (file_exists($tempFiles . "tmp.jpg")) {
		$inHandle = fopen($tempFiles."tmp.jpg","r");
		$fileData = fread($inHandle, filesize($tempFiles."tmp.jpg"));
		fclose($inHandle);
		$outHandle = fopen("/var/www/nintendorising/images/banner/" . $articleID . ".jpg","w");
		$written = fwrite($outHandle,$fileData);
		fclose($outHandle);
		if ($written) {
			unlink($tempFiles."tmp.jpg");
		}
	}
	
	// Do the same for a newly uploaded splash image.
	if (file_exists($tempFiles . "spl.jpg")) {
		$inHandle = fopen($tempFiles."spl.jpg","r");
		$fileData = fread($inHandle, filesize($tempFiles."spl.jpg"));
		fclose($inHandle);
		$outHandle = fopen("/var/www/nintendorising/images/splash/" . $articleID . ".jpg","w");
		$written = fwrite($outHandle,$fileData);
		fclose($outHandle);
		if ($written) {
			unlink($tempFiles."spl.jpg");
		}
	}
?>
<html lang="en" dir="ltr">
<head>
<title>Nintendo Rising: Administration Panel (Manager Articles)</title>
<link type="text/css" rel="stylesheet" href="../style/admin.css"/>
<link type="text/css" rel="stylesheet" href="../style/article.css"/>
<script type="text/javascript" src="../scripts/utils.js"></script>
<script type="text/javascript" src="../scripts/userMan.js"></script>
<script type="text/javascript">
function refreshBanner() {
	ajaxBannerRequest = getAjaxRequest();
	if (ajaxBannerRequest) {
		<?php
			echo "var urlString = \"../createArticleBanner.php?uname=\" + escape('" . 
			$articleRow["userName"] . "') + \"&game=" . $articleRow["gameID"] .
				"&art=" . $articleRow["type"] . "&img=" . $articleID . "\"\n";
		?>
		ajaxBannerRequest.open("GET",urlString,true);
		ajaxBannerRequest.onreadystatechange = showBanner;
		ajaxBannerRequest.send(null);
	}
}
			
function showBanner() {
	getElement("articleBanner").innerHTML = ajaxBannerRequest.responseText;
}

function addSection() {
	var detailsForm = getElement("articleDetails");
	detailsForm.action = "./editArticle.php?type=add";
	detailsForm.submit();
}

function updateSection(sectionID) {
	var updateForm = getElement("updateForm");
	updateForm.action = "./editArticle.php?type=update&section=" + sectionID;
	updateForm.submit();
}

function removeSection(sectionID) {
	var updateForm = getElement("updateForm");
	updateForm.action = "./editArticle.php?type=remove&section=" + sectionID;
	updateForm.submit();
}
</script>
</head>
<body onLoad="refreshBanner()">
<div id="main">
<h1>Article Editor:</h1>
<a href="./manageArticles.php">Go back to Article Management</a><br><br>
<div id="articleBanner" style="position: relative; text-align: center; height: 248px"></div>
<p style="padding: 0px; margin: 1em 8px; font-weight: bold; font-size: 1.4em"><?php echo $articleRow["subtitle"]; ?></p>
<?php
	$editing = -1;
	if (strcmp($_GET["type"],"update") == 0) {
		// Upload any image files first
		$destFilename = "/var/www/nintendorising/images/article/" . $articleID . "-" . $_GET["section"] . ".jpg";
		if (strlen($_FILES["imageFile"]["name"]) > 0) {
			$img = @imagecreatefromjpeg($_FILES["imageFile"]["tmp_name"]);
			$img = scaleImage($img, 240);
			imagejpeg($img,$destFilename);
			imagedestroy($img);
		}
		
		mysql_query("UPDATE articleSections SET htmlContent='" . $_POST["content"] .
			"',imageLocation=" . $_POST["imagePos"] . " WHERE displayOrder=" . $_GET["section"] . " AND articleID=" . $articleID) or
			die ("Failed to update section: " . mysql_error());
	} else if (strcmp($_GET["type"],"edit") == 0) {
		$editing = $_GET["section"];
	} else if (strcmp($_GET["type"],"add") == 0) {
		$result = mysql_query("SELECT MAX(displayOrder) AS sectCount FROM articleSections WHERE articleID=" . $articleID) or
			die("Failed to get article section count: " . mysql_error());
		$row = mysql_fetch_array($result);
		$addQuery = "INSERT INTO articleSections (articleID, displayOrder, htmlContent, imageLocation)" .
			" VALUES (" . $articleID . "," . ($row["sectCount"] + 1) . ",'" . $_POST["content"] .
			"', " . $_POST["imagePos"] . ")";
		mysql_query($addQuery) or
			die ("Failed to add section: " . mysql_error());
			
		// Upload any image files
		$destFilename = "/var/www/nintendorising/images/article/" . $articleID . "-" . ($row["sectCount"] + 1) . ".jpg";
		if (strlen($_FILES["imageFile"]["name"]) > 0) {
			$img = @imagecreatefromjpeg($_FILES["imageFile"]["tmp_name"]);
			$img = scaleImage($img, 200);
			imagejpeg($img,$destFilename);
			imagedestroy($img);
		}
	} else if (strcmp($_GET["type"],"remove") == 0) {
		mysql_query("DELETE FROM articleSections WHERE displayOrder=" . $_GET["section"]);
		$destFilename = "/var/www/nintendorising/images/article/" . $articleID . "-" . $_GET["section"] . ".jpg";
		if (file_exists($destFilename)) {
			unlink($destFilename);
		}
	}
	
	$result = mysql_query("SELECT displayOrder, htmlContent, imageLocation FROM articleSections " .
		"WHERE articleID = " . $articleID . " ORDER BY displayOrder ASC") or
		die("Failed to find sections: " . mysql_error());
		
	while ($row = mysql_fetch_array($result)) {
		echo "<div class=\"articleSection\" style=\"border: solid 1px rgb(40, 80 ,255)\">\n";
		if ($row["displayOrder"] != $editing) {
			if ($row["imageLocation"] > 0) {
				echo "<img class=\"articleImage\"";
				switch ($row["imageLocation"]) {
					case 1 : {
						echo " style=\"float: left; margin-right: 1em\"";
					} break;
					default : {
						echo " style=\"float: right; margin-left: 1em\"";
					}
				}
				echo " src=\"/nintendorising/images/article/" . $articleID . "-" .
					$row["displayOrder"] . ".jpg\"/>\n";
			}
			echo $row["htmlContent"];
			echo "<form action=\"editArticle.php?type=edit&section=" . $row["displayOrder"] . "\" method=\"POST\">\n";
			echo "<input type=\"hidden\" name=\"articleID\" value=\"" . $articleID . "\"/>\n";
			echo "<button type=\"submit\" style=\"margin: 4px 4px -12px 4px\">Edit</button>\n";
			echo "</form>\n";
		} else {
			echo "<form enctype=\"multipart/form-data\" id=\"updateForm\" method=\"POST\">\n";
			echo "<input type=\"hidden\" name=\"articleID\" value=\"" . $articleID . "\"/>\n";
			echo "<textarea style=\"width: 100%; height: 192px; padding: 4px 8px; border: 1px solid rgb(40, 80, 255)\"" .
				" name=\"content\">" . $row["htmlContent"] . "</textarea>\n";
			echo "<button onClick=\"updateSection(" . $row["displayOrder"] . ")\">Update Section</button>\n";
			echo "<button onClick=\"removeSection(" . $row["displayOrder"] . ")\">Remove Section</button>\n";
			echo "<label style=\"margin-left: 2em; width: 6em; text-align: right\" for=\"imageFile\">" .
				"New Image:</label><input type=\"file\" name=\"imageFile\"/>\n";
			echo "<select name=\"imagePos\">\n" .
				"<option value=\"0\">NoImage</option>\n" .
				"<option value=\"1\">Left</option>\n" .
				"<option value=\"2\">Right</option>\n</select>\n";
			echo "</form>\n";
		}
		echo "</div>\n";
	}
	
	echo "<div class=\"articleSection\">\n";
	echo "<form enctype=\"multipart/form-data\" id=\"articleDetails\" method=\"POST\">\n";
	echo "<input type=\"hidden\" name=\"articleID\" value=\"" . $articleID . "\"/>\n";
	echo "<textarea style=\"width: 100%; height: 192px; padding: 4px 8px; border: 1px solid rgb(40, 80, 255)\"" .
			" name=\"content\"></textarea>\n";
	echo "<button onClick=\"addSection()\">Add Section</button>\n";
	echo "<label style=\"margin-left: 2em; width: 6em; text-align: right\" for=\"imageFile\">Image:</label><input type=\"file\" name=\"imageFile\"/>\n";
	echo "<select name=\"imagePos\">\n" .
		"<option value=\"0\">NoImage</option>\n" .
		"<option value=\"1\">Left</option>\n" .
		"<option value=\"2\">Right</option>\n</select>\n";
	echo "</form>\n";
	echo "</div>\n";
?>
</div>
</body>
<?php
	mysql_close($dbconn); 
?>
</html>
