<?php

namespace app\service\tool;

class Image
{
	const FONTFILE = ROOT_PATH . 'public/font/fzht.ttf';

	public function verifyCode($code, int $width = 80, int $height = 40): bool
	{
		if (empty($code)) return false;
		$image = @imagecreatetruecolor($width, $height);
		if (!$image) {
			return false;
		}
		$bgcolor = imagecolorallocate($image, 255, 255, 255);
		imagefill($image, 0, 0, $bgcolor);
		$len = strlen($code);
		for ($i = 0; $i < $len; $i++) {
			$fontcolor = imagecolorallocate($image, random_int(0, 120), random_int(0, 120), random_int(0, 120));
			$fontsize = random_int(18, 24);
			$x = (int)($i * ($width - 10) / $len) + 5;
			$y = random_int($fontsize, $height - 4);
			imagettftext($image, $fontsize, random_int(0, 30), $x, $y, $fontcolor, self::FONTFILE, $code[$i]);
		}
		for ($i = 0; $i < 4; $i++) {
			$linecolor = imagecolorallocate($image, random_int(80, 220), random_int(80, 220), random_int(80, 220));
			imageline($image, random_int(1, $width - 1), random_int(1, $height - 1), random_int(1, $width - 1), random_int(1, $height - 1), $linecolor);
		}
		for ($i = 0; $i < 300; $i++) {
			$pointcolor = imagecolorallocate($image, random_int(50, 200), random_int(50, 200), random_int(50, 200));
			imagesetpixel($image, random_int(0, $width), random_int(0, $height), $pointcolor);
		}
		header('Content-Type: image/png');
		imagepng($image);
		return true;
	}

	public function text($src, $text, $font_size, $angle, $x, $y, array $colorRGB = [255, 255, 255], int $alpha = 0, string $fontfile = ''): bool
	{
		if (!is_file($src)) return false;
		$imgHandler = imagecreatefrompng($src);
		if (!$imgHandler) return false;
		if ($fontfile === '') {
			$fontfile = self::FONTFILE;
		}
		$color = imagecolorallocatealpha($imgHandler, (int)$colorRGB[0], (int)$colorRGB[1], (int)$colorRGB[2], $alpha);
		imagettftext($imgHandler, (float)$font_size, (float)$angle, (int)$x, (int)$y, $color, $fontfile, (string)$text);
		header('Content-Type: image/png');
		imagepng($imgHandler);
		return true;
	}

	public function compressImg($src, $moveto = '', $percent = 1): bool
	{
		if (!extension_loaded('gd') || !is_file($src)) {
			return false;
		}
		$srcImageInfo = getimagesize($src);
		if (!$srcImageInfo) {
			return false;
		}
		$srcImageWidth = (int)$srcImageInfo[0];
		$srcImageHeight = (int)$srcImageInfo[1];
		$srcImageMime = $srcImageInfo['mime'] ?? '';
		$imagecreatefromfunc = $imagefunc = '';
		$toWebp = true;
		switch ($srcImageMime) {
			case 'image/jpeg':
			case 'image/jpg':
				$imagecreatefromfunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
				$imagefunc = function_exists('imagejpeg') ? 'imagejpeg' : '';
				break;
			case 'image/gif':
				$imagecreatefromfunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
				$imagefunc = function_exists('imagegif') ? 'imagegif' : '';
				$toWebp = false;
				break;
			case 'image/png':
				$imagecreatefromfunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
				$imagefunc = function_exists('imagepng') ? 'imagepng' : '';
				break;
		}
		if (!$imagecreatefromfunc || !$imagefunc) {
			return false;
		}

		$srcImage = $imagecreatefromfunc($src);
		if (!$srcImage) {
			return false;
		}

		$new_w = max(1, (int)round($srcImageWidth * $percent));
		$new_h = max(1, (int)round($srcImageHeight * $percent));
		$returnPic = imagecreatetruecolor($new_w, $new_h);

		imagealphablending($returnPic, true);
		imagesavealpha($returnPic, true);
		$white = imagecolorallocatealpha($returnPic, 255, 255, 255, 127);
		imagefill($returnPic, 0, 0, $white);

		imagecopyresampled($returnPic, $srcImage, 0, 0, 0, 0, $new_w, $new_h, $srcImageWidth, $srcImageHeight);
		if (empty($moveto)) {
			$moveto = $src;
		}

		createDir(dirname($moveto));

		$imagefunc($returnPic, $moveto);
		if ($toWebp) {
			$ext = pathinfo($moveto, PATHINFO_EXTENSION);
			imagewebp($returnPic, substr($moveto, 0, -strlen($ext)) . 'webp');
		}
		clearstatcache();
		return true;
	}

	public function thumbImage($src, $moveto, int $outputWidth = 600): bool
	{
		if (!extension_loaded('gd') || !is_file($src)) {
			return false;
		}
		$srcImageInfo = getimagesize($src);
		if (!$srcImageInfo) {
			return false;
		}
		$srcImageWidth = (int)$srcImageInfo[0];
		$srcImageHeight = (int)$srcImageInfo[1];
		createDir(pathinfo($moveto, PATHINFO_DIRNAME));

		$imagecreatefromfunc = $imagefunc = '';
		$toWebp = true;
		switch ($srcImageInfo['mime'] ?? '') {
			case 'image/jpeg':
			case 'image/jpg':
				$imagecreatefromfunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
				$imagefunc = function_exists('imagejpeg') ? 'imagejpeg' : '';
				break;
			case 'image/gif':
				$imagecreatefromfunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
				$imagefunc = function_exists('imagegif') ? 'imagegif' : '';
				$toWebp = false;
				break;
			case 'image/png':
				$imagecreatefromfunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
				$imagefunc = function_exists('imagepng') ? 'imagepng' : '';
				break;
		}
		if (!$imagecreatefromfunc || !$imagefunc) {
			return false;
		}
		$srcImage = $imagecreatefromfunc($src);
		if (!$srcImage) {
			return false;
		}

		$ratio = $srcImageWidth / $outputWidth;
		$outputHeight = max(1, (int)round($srcImageHeight / $ratio));

		$returnPic = imagecreatetruecolor($outputWidth, $outputHeight);
		imagecopyresampled($returnPic, $srcImage, 0, 0, 0, 0, $outputWidth, $outputHeight, $srcImageWidth, $srcImageHeight);
		if ($toWebp) {
			$ext = pathinfo($moveto, PATHINFO_EXTENSION);
			imagewebp($returnPic, substr($moveto, 0, -strlen($ext)) . 'webp');
		} else {
			$imagefunc($returnPic, $moveto);
		}
		clearstatcache();
		return true;
	}
}
