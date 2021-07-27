<?php
	session_start();
	if (!isset($_SESSION["userName"]) && isset($_POST["userName"])) {
		$dbconn = mysql_connect("localhost","root","alohamora") or
			die("Failed to connect to nintendorising host" . mysql_error());
		
		mysql_select_db("nintendorising") or
			die("Failed to find nintendorising database");
		
		mysql_query("INSERT INTO users (userName, realName, password, email) VALUES ('" .
			$_POST["userName"] . "','" . $_POST["realName"] . "','" .
			crypt($_POST["password"]) . "','" . $_POST["email"] . "')") or 
			die ("Failed to add user: " . mysql_error());
		
		mysql_close($dbconn);
		
		$_SESSION["userName"] = $_POST["userName"];
	}
?>
<html lang="en" dir="ltr">
<head>
<title>Nintendo Rising: Account Details</title>
<link rel="stylesheet" type="text/css" href="./style/sidebar.css"/>
<link rel="stylesheet" type="text/css" href="./style/default.css"/>
<script type="text/javascript" src="./scripts/utils.js"></script>
<script type="text/javascript" src="./scripts/userMan.js"></script>
<script type="text/javascript" src="./scripts/sidebar.js"></script>
<style type="text/css" rel="stylesheet">						
body		{	background: url("./style/BlueBG1680.png") repeat-y;	}														
</style>
</head>
<body onLoad="setInterval(fetchLastestForumPosts, 10000); fetchLastestForumPosts(); getLoginStatus()">
<div id="main">

<div id="sideBar">
<div class="sideBox">
<h1>USER LOGIN</h1>
<div id="loginBox"></div>
</div>
<div class="sideBox" id="forumLatest">
<h1>NO FORUM POSTS!</h1>
</div>
<div class="sideBox" id="mediaLatest">
<h1>LATEST MEDIA</h1>
</div>
</div>

<div id="mainContent">
<?php
	if (!isset($_SESSION["userName"])) {
?>
<h1>Create New Account</h1>
<p>Welcome to Nintendo Rising!  We hope you've had chance to browse some of our 
articles and check out the galleries.  Of course, you can do a whole lot more if 
you register as a user which is why we guess you're on this page!</p>
<p>We don't require a great deal of information - essentially we just need a 
user name, password and email address but you can also tell us your real name 
and which country you live in.  You can even post an image to use as your 
avatar so your forum posts and comments are instantly recognisable and for an 
extra touch of personalisation, you can include a signature too.</p>
<h2>Let's Get Started</h2>
<p>Please enter your details below.  Any fields marked with an asterix (*) are 
essential to the registration process.  You'll need to enter the same password 
for both the 'Password' and 'Confirm' fields for the registration to be successful.</p>
<form id="newUserForm" action="userDetails.php" method="POST">
<label for="userName">User name*:</label>
<input name="userName" type="text" maxlength="32"/><br>
<label for="realName">Real name:</label>
<input name="realName" type="text" maxlength="64"/><br>
<label for="password">Password*:</label>
<input name="password" type="password" maxlength="32"/><br>
<label for="conpass">Confirm*:</label>
<input name="conpass" type="password" maxlength="32"/><br>
<label for="email">Email:*</label>
<input name="email" type="type" maxlength="64"/><br>
<button id="regButton" type="submit" onClick="return checkRegistration(newUserForm)" style="margin-left : 29em">Register Me!</button>
<?php
	}
?>
</div>

</div>
</body>
</html>
