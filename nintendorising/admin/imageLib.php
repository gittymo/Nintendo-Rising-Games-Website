<?php

define(UPLOAD_SIZE_LIMIT, 512*1024);

function decodeUploadFile($srcFile, $destFile) {
	$srcHandle = fopen($srcFile,"r");
	$srcData = fread($srcHandle, filesize($srcFile));
	fclose($srcHandle);

	$srcData = preg_replace("/\+/", "%2B", $srcData);

	$destHandle = fopen($destFile, "w");
	fwrite($destHandle, urldecode($srcData));
	fclose($destHandle);
	
	if (filesize($destFile) >= UPLOAD_SIZE_LIMIT) {
		unlink($destFile);
		return false;
	}
	
	return true;
}

function getImageFormat($imageFile) {
	$extensions = array("GIF","BMP","JPG","PNG","XPM");
	$check = array(IMG_GIF, IMG_WBMP, IMG_JPG, IMG_PNG, IMG_XPM);
	
	$valid = null;
	for ($i = 0; $i < count($extensions); $i++) {
		if (stripos($imageFile, "." . $extensions[$i]) !== false) {
			if (imageTypes() & $check[$i]) {
				$valid = $extensions[$i];
				break;
			}
		}
	}
	
	if ($valid == null) {
		unlink($imageFile);
	}
	
	return $valid;
}

function loadImage($imageFile) {
	$img = null;
	$imgType = getImageFormat($imageFile);
	if ($imgType != null) {
		if (strcmp($imgType,"JPG") == 0) {
			$img = @imagecreatefromjpeg($imageFile);
		} else if (strcmp($imgType,"BMP") == 0) {
			$img = @imagecreatefromwbmp($imageFile);
		} else if (strcmp($imgType,"PNG") == 0) {
			$img = @imagecreatefrompng($imageFile);
		} else if (strcmp($imgType,"GIF") == 0) {
			$img = @imagecreatefromgif($imageFile);
		} else if (strcmp($imgType,"XPM") == 0) {
			$img = @imagecreatefromxpm($imageFile);
		}
	}
	
	return $img;
}

function scaleImage($img, $scaledWidth, $scaledHeight = -1) {
	if ($scaledHeight < 1) {
		$scale = (float) $scaledWidth / (float) imagesx($img);
		$scaledHeight = (float) imagesy($img) * $scale;
	}
	
	$scaledImg = imagecreatetruecolor($scaledWidth, $scaledHeight);
	imagecopyresized($scaledImg, $img, 0, 0, 0, 0, $scaledWidth, $scaledHeight, imagesx($img), imagesy($img));
	imagedestroy($img);
	
	return $scaledImg;
}

function cropImage($img, $cropWidth, $cropHeight) {
	if ($cropWidth > imagesx($img)) {
		$cropWidth = imagesx($img);
	}
	
	if ($cropHeight > imagesy($img)) {
		$cropHeight = imagesy($img);
	}
		
	$croppedImg = imagecreatetruecolor($cropWidth, $cropHeight);
	$left = (imagesx($img) - $cropWidth) / 2;
	$top = (imagesy($img) - $cropHeight) / 2;
	imagecopy($croppedImg, $img, 0, 0, $left, $top, $cropWidth, $cropHeight);
	imagedestroy($img);
	
	return $croppedImg;
}
?>
