<?php
session_start();
?>
<html lang="en" dir="ltr">
<head>
<title>Nintendo Rising: Administration Panel (Manager Articles)</title>
<link type="text/css" rel="stylesheet" href="../style/admin.css"/>
<script type="text/javascript" src="../scripts/utils.js"></script>
<script type="text/javascript" src="../scripts/userMan.js"></script>
<script type="text/javascript">
function refreshBanner() {
	ajaxBannerRequest = getAjaxRequest();
	if (ajaxBannerRequest) {
		<?php
			echo "var urlString = \"../createArticleBanner.php?uname=\" + escape('" 
				. $_SESSION["userName"] .
				"') + \"&game=\" + getElement('gameName').value + \"" .
				"&art=\" + getElement('articleType').value + \"&img=\" + escape(getElement('imageURL').value);";
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
</head>
<body>

