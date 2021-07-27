<?php
	function makeImageArticleLink($html, $articleID) {
		$imgTagSearch = "<img.*/>";
		$found = eregi($imgTagSearch, $html, $regs);
		$html = str_replace($regs[0],"<a href=\"./showArticle.php?art=" . $articleID . "\">" . $regs[0] . "</a>",$html);
		return $html;
	}
?>
