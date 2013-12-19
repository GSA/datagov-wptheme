<?php
/*
	Custom Contact Forms Plugin
	By Taylor Lovett - http://www.taylorlovett.com
	Plugin URL: http://www.taylorlovett.com/wordpress-plugins
*/

if (!class_exists('CustomContactFormsImages')) {
	class CustomContactFormsImages {
		function createImageWithText($str){
			$image = imagecreate(96,24);
			$src = imagecreatefrompng('images/gd' . rand(1, 4) . '.png');
			$textcolor = imagecolorallocate($src, 10, 0, 0);
			imagestring($src, 14, 5, 1, $str, $textcolor);
			imagecopyresampled($image, $src, 0, 0, 0, 0, 96, 24, 63, 18);
			imagepng($image);
			imagedestroy($image);
			imagedestroy($src);
			return $str;
		}
	}
}
?>