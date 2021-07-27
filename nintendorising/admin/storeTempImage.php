<?php
	header("Cache-Control: no-cache, must-revalidate");
	 // Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	session_start();
	
	require "./imageLib.php";
	$responseText = "";
	$baseDir = "/var/www/nintendorising/";
	$sourceFilename = $_FILES["myfile"]["tmp_name"];
	$suffix = (isset($_GET["suffix"])) ? $_GET["suffix"] : "tmp";
	$destFilename = (isset($_GET["dest"])) ? $_GET["dest"] : $baseDir . "temp/" . $_SESSION["userName"] . $suffix . ".jpg";
	
	$uploadable = decodeUploadFile($sourceFilename,$destFilename);
	if ($uploadable) {
		$img = loadImage($destFilename);
		if ($img != null) {
			if (isset($_GET["swidth"])) {
				$img = scaleImage($img, $_GET["swidth"]);
			}
			
			if (isset($_GET["crop"])) {
				$width = (int) substr($_GET["crop"],0,strpos($_GET["crop"],"x"));
				$height = (int) substr($_GET["crop"],strpos($_GET["crop"],"x") + 1);
				$img = cropImage($img, $width, $height);
			}
			
			$responseText = "Image file uploaded";
			$saved = imagejpeg($img,$destFilename,70);
			if (!$saved) {
				$responseText = "Could not save processed image";
			}
		} else {
			$responseText = "Uploaded image is not in a format that can be read.";
		}
	} else {
		$responseText = "File too large for upload";
	}
	
	echo $responseText;
?>
