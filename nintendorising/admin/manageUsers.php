<html lang="en" dir="ltr">
<head>
<title>Nintendo Rising: Administration Panel (User Management)</title>
<link type="text/css" rel="stylesheet" href="../style/admin.css"/>
<script type="text/javascript" src="../scripts/utils.js"></script>
<script type="text/javascript">
var ajaxReq = null;
var page = 0;
var sortOrder = new Array(true, true, true, true, true);

function fetchUserDetails(SQLQuery) {
	ajaxReq = getAjaxRequest();
	if (ajaxReq != null) {
		var urlString = "./getUserDetails.php?page=" + page;
		if (SQLQuery != null) {
			urlString += "&sql=" + escape(SQLQuery);
		}
		ajaxReq.open("GET",urlString,true);
		ajaxReq.send(null);
		ajaxReq.onreadystatechange=displayUserDetails;
	}
}

function fetchUserDetailsByName() {
	var sql = "WHERE userName LIKE \"%" + getElement("reqUserName").value + "%\" ORDER BY userName ASC";
	fetchUserDetails(sql);
}

function clearUserSearch() {
	fetchUserDetails();
	getElement("reqUserName").value = "";
}

function displayUserDetails() {
	var header="<label class=\"userName\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchUserDetails(\"ORDER BY userName " + 
		((sortOrder[0] = !sortOrder[0]) ? "DESC" : "ASC") + "\")'>User Name</label>";
	header += "<label class=\"joinDate\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchUserDetails(\"ORDER BY joinDate " + 
		((sortOrder[1] = !sortOrder[1]) ? "DESC" : "ASC") + "\")'>Joined</label>";
	header += "<label class=\"admin\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchUserDetails(\"ORDER BY admin " + 
		((sortOrder[2] = !sortOrder[2]) ? "DESC" : "ASC") + "\")'>Admin?</label>";
	header += "<label class=\"status\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchUserDetails(\"ORDER BY warnings " + 
		((sortOrder[3] = !sortOrder[3]) ? "DESC" : "ASC") + "\")'>Status</label>";
	header += "<label class=\"posts\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchUserDetails(\"ORDER BY posts " + 
		((sortOrder[4] = !sortOrder[4]) ? "DESC" : "ASC") + "\")'>Total Posts</label><br>";
	getElement("accountList").innerHTML=header + ajaxReq.responseText;
}
</script>
</head>
<body onload="fetchUserDetails()">
<div id="main">
<h1>Adminstration: User Management</h1>
<p>You can view basic user details from this page including their adminstration 
status (which can be altered to allow any user privileges for posting articles) 
and view any warnings or the blocked/banned status of their account.</p>
<p>If you wish the send a user an email, you can click their username and your 
email client should create a new message with the appropriate recipient address.</p>
<p>Finally you can search for users by using the search box below the accounts 
list or order the accounts list by ascending/descending order of interest by 
clicking on the appropriate column header.</p>
<a href="./index.php">To return to the main menu click here</a>
<h2>User Accounts:</h2>
<div id="accountList"></div>
<h2>User Search:</h2>
<label for="reqUserName">User name:</label>
<input id="reqUserName" maxlength="32"/>
<button onClick="fetchUserDetailsByName()">Search</button>
<button onClick="clearUserSearch()">Clear</button>
</div>
</body>
</html>
