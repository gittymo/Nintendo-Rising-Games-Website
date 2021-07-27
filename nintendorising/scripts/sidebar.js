var ajaxReq = null;
function fetchLastestForumPosts() {
	ajaxReq = getAjaxRequest();
	if (ajaxReq != null) {
		ajaxReq.open("GET","./FetchLatestPosts.php",true);
		ajaxReq.send(null);
		ajaxReq.onreadystatechange=displayLatestPosts;
	}
}

function displayLatestPosts() {
	getElement("forumLatest").innerHTML=ajaxReq.responseText;
}
