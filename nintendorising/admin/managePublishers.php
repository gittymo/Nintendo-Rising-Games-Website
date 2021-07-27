<html lang="en" dir="ltr">
<head>
<title>Nintendo Rising: Administration Panel (Manager Publishers)</title>
<link type="text/css" rel="stylesheet" href="../style/admin.css"/>
<script type="text/javascript" src="../scripts/utils.js"></script>
<script type="text/javascript">
var ajaxListReq = null;
var sortOrder = new Array(true, true, true);
var lastSQLQuery = null;

function addDetails() {
	var companyName = escape(getElement("companyName").value);
	var homePage = escape(getElement("homePage").value);
	var sendReq = getAjaxRequest();
	if (sendReq != null) {
		var urlString = "./addPublisher.php?companyName=" + companyName +
			"&homePage=" + homePage;
		sendReq.open("GET",urlString,true);
		sendReq.send(null);
	}
	
	getElement("companyName").value = "";
	getElement("homePage").value = "";
	setTimeout(refreshPublisherDetails,2000);
}

function updateDetails() {
	var companyName = escape(getElement("companyName").value);
	var homePage = escape(getElement("homePage").value);
	var publisherID = getElement("pubID").value;
	var sendReq = getAjaxRequest();
	if (sendReq != null) {
		var urlString = "./updatePublisher.php?companyName=" + companyName +
			"&homePage=" + homePage + "&pubID=" + publisherID;
		sendReq.open("GET",urlString,true);
		sendReq.send(null);
	}
	
	getElement("companyName").value = "";
	getElement("homePage").value = "";
	getElement("pubID").value = "";
	getElement("addButton").disabled = false;
	getElement("updateButton").disabled = true;
	setTimeout(refreshPublisherDetails,2000);
}

function fetchPublisherDetails(SQLQuery) {
	if (SQLQuery == null) {
		SQLQuery = "ORDER BY companyName ASC";
	}
	lastSQLQuery = SQLQuery;
	
	ajaxListReq = getAjaxRequest();
	if (ajaxListReq != null) {
		ajaxListReq.open("GET","./getPublisherList.php?sql=" + escape(SQLQuery),true);
		ajaxListReq.send(null);
		ajaxListReq.onreadystatechange=displayPublisherDetails;
	}
}

function displayPublisherDetails() {
	var header="<label class=\"companyName\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchPublisherDetails(\"ORDER BY companyName " + 
		((sortOrder[0] = !sortOrder[0]) ? "DESC" : "ASC") + "\")'>Company Name</label>";
	header += "<label class=\"homePage\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchPublisherDetails(\"ORDER BY www " + 
		((sortOrder[1] = !sortOrder[1]) ? "DESC" : "ASC") + "\")'>Homepage</label>";
	header += "<label class=\"gamesCount\" style=\"background: rgb(192,255,192)\" " +
		"onClick='fetchPublisherDetails(\"ORDER BY gamesCount " + 
		((sortOrder[2] = !sortOrder[2]) ? "DESC" : "ASC") + "\")'>Featured Games</label><br>";
		ajaxListReq.onreadystatechange=displayPublisherDetails;
	getElement("publisherList").innerHTML = header + ajaxListReq.responseText;
}

function copyDetails(companyName, homePage, publisherID) {
	getElement("companyName").value = companyName;
	getElement("homePage").value = homePage;
	getElement("pubID").value = publisherID;
	getElement("addButton").disabled = true;
	getElement("updateButton").disabled = false;
}

function refreshPublisherDetails() {
	fetchPublisherDetails(lastSQLQuery);
}

</script>
<head>
<body onLoad="fetchPublisherDetails(null); getElement('updateButton').disabled=true">
<div id="main">
<h1>Adminstration: Manage Game Publishers</h1>
<p>You can add, view and edit and remove details of any of the game publishers 
referred to by articles on the Nintendo Rising website using this page.</p>
<p>To edit a publisher's details click on their name in the publishers list 
below.  Once you've edited their details, simply click the update button.  You 
can remove a publisher by clicking the remove button too.</p>
<p>If you click on the web link in the publishers list, you will open a second 
window containing the website at that address which could be useful for fact-
finding.  You can also sort the list in ascending/descending order by clicking 
the column headers.</p>
<a href="./index.php">To return to the main menu click here</a>
<h2>Publisher List</h2>
<div id="publisherList"></div>
<h2>Publisher Details</h2>
<label for="companyName">Company Name:</label>
<input id="companyName" type="text" maxlength="64" style="width: 548px"/><br>
<label for="homePage">Homepage:</label>
<input id="homePage" type="text" maxlength="64" style="width: 548px"/>
<input id="pubID" type="hidden"/>
<button onClick="updateDetails()" id="updateButton" style="width: 192px;margin-left: 364px; margin-top: 4px">Update Details</button>
<button onClick="addDetails()" id="addButton" style="width: 192px;">Add as New Publisher</button>
</body>
</html>
