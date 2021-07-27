<?php
	function renderArticleSection($sectionData, $cutoff = 0) {
		$sectionHTML = "";
		if ($sectionData["imageLocation"] > 0) {
		
			$sectionHTML .= "<img class=\"articleImage\"";
			switch ($sectionData["imageLocation"]) {
				case 1 : {
					$sectionHTML .= " style=\"margin-right: 1em; float:left\" ";
				} break;
				default : {
					$sectionHTML .= " style=\"margin-left: 1em; float:right\" ";
				}
			}
			$sectionHTML .= " src=\"/nintendorising/images/article/" . $sectionData["articleID"] . "-" .
				$sectionData["displayOrder"] . ".jpg\"/>\n";
		}
		$sectionHTML .= $sectionData["htmlContent"];
		if ($cutoff > 99 && strlen($sectionHTML) > $cutoff) {
			$sectionHTML = substr($sectionHTML,0,$cutoff);
			$sectionHTML = substr($sectionHTML,0,strrpos($sectionHTML," ")) . " ...";
		}
		return $sectionHTML;
	}
?>
