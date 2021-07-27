<?php
	header("Cache-Control: no-cache, must-revalidate");
	 // Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	// Connect to the database server
	$dbconn = mysql_connect("localhost", "root", "alohamora");
	if (!$dbconn) {
		die("Could not connect to server. " + mysql_error());
	}
	
	// Select the amychat database.
	$dbase = mysql_select_db("nintendorising");
	if (!$dbase) {
		die("Could not find database. " + mysql_error());
	}
	
	// Query to fetch the last five posts
	$postsQuery = "SELECT userName, UNIX_TIMESTAMP(editTime), anchorID, " .
		"postID, comment FROM forumPosts ORDER BY editTime DESC LIMIT 5";
	$result = mysql_query($postsQuery);
	if (!$result) {
		die("Could not retrieve posts: " + mysql_error());
	}
	
	// Send the header
	echo "<h1>LATEST FORUM POSTS</h1>";
	
	// Iterate throug the results and return the response.
	while ($row = mysql_fetch_array($result)) {
		$topicQuery = "SELECT title, url FROM forumTopics WHERE id=" . $row[3];
		$topicResult = mysql_query($topicQuery);
		if (!$topicResult) {
			die("Could not topic: " + mysql_error());
		}
		$topicRow = mysql_fetch_array($topicResult);
		echo "<div class=\"forumEntry\" title=\"" . $row[0] . ": ";
		$titleLen = strlen($row[4] . $row[0]);
		if ($titleLen < 252) {
			echo $row[4];
		} else {
			$spacePos = strrpos($row[4]," ",252 - $titleLen);
			echo substr($row[4],0,$spacePos) . " ...";
		}	
		echo "\"><a href=\"" . $topicRow[1] . "#" . $row[2] . "\">" . 
	 	$topicRow[0] . "</a><br>Posted ";
	 	$timeDiff = time() - $row[1];
	 	if ($timeDiff < 60) {
	 		echo $timeDiff . " seconds";
	 	} else {
	 		$timeDiff /= 60;
	 		if ($timeDiff < 60) {
	 			$floorTime = floor($timeDiff);
	 			echo $floorTime . (($floorTime > 1) ? " minutes" : " minute");
	 		} else {
	 			$timeDiff /= 60;
	 			if ($timeDiff < 24) {
	 				$floorTime = floor($timeDiff);
					if ($timeDiff > $floorTime) {
						echo "over ";
					}
	 				echo floor($timeDiff) . (($floorTime > 1) ? " hours" : " hour");
	 			} else {
	 				$timeDiff /= 24;
	 				$floorTime = floor($timeDiff);
	 				echo floor($timeDiff) . (($floorTime > 1) ? " days" : " day");
	 			}
	 		}
	 	}
	 	echo " ago</div>\n";
	}
	
	// Close the connection
	mysql_close($dbconn);
?>
