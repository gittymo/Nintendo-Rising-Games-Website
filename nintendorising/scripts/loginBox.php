<div id="loginBox" style="width: 241px; background: rgb(224,224,224);
	border: 2px solid rgb(64,64,64); font-family: 'Arial','Helvetica',sans-serif;
	font-size: 0.8em; padding: 8px; margin: none">
<?php
	if (isset($_SESSION["userName"])) {
		echo "<p>Welcome " . $_SESSION["userName"] . "</p>\n";
		if ($_SESSION["admin"] == 1) {
			echo "<a href=\"./admin/index.php\">Admin Menu</a>\n";
		}
	} else {
?>
<label style="color: rgb(64,128,255); font-weight: bold" for="loginUserName">Username:</label>
<input style="border: 1px solid rgb(64,128,255); padding: 2px 4px; width: 134px" id="loginUserName" type="text" maxlength="32" value="user name"/><br>
<label style="color: rgb(64,128,255); font-weight: bold" for="loginPassword">Password:</label>
<input style="border: 1px solid rgb(64,128,255); padding: 2px 4px; width: 134px" id="loginPassword" type="password" maxlength="32"/><br>
<button onClick="login()" style="margin: 8px 0px 0px 110px">Login</button><br>
<a href="/nintendorising/userDetails.php">Register</a>
<?php
	}
?>
</div>
