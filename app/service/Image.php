<?php

namespace app\service;

class Image
{
	const FONTFILE = ROOT_PATH . 'public' . DS . 'font' . DS . 'fzht.ttf';

	public function verifyCode($code, $width = 80, $height = 40)
	{
		if (empty($code)) return false;
		//创建画布
		$image = @imagecreatetruecolor($width, $height) or die('Cannot Initialize new GD image stream');
		//填充背景色
		$bgcolor = imagecolorallocate($image, 255, 255, 255);
		imagefill($image, 0, 0, $bgcolor);
		// 生成随机码
		$len = strlen($code);
		for ($i = 0; $i < $len; $i++) {
			//设置随机字体颜色
			$fontcolor = imagecolorallocate($image, rand(0, 120), rand(0, 120), rand(0, 120));
			//随机码宽度
			$fontsize = rand(18, 24);
			$angle = rand(0, 30);
			$x = intval($i * ($width - 10) / $len) + 5;
			//随机码高度
			$y = rand($fontsize, $height - 4);
			//填充当前字符入画布
			imagettftext($image, $fontsize, rand(0, 30), $x, $y, $fontcolor, self::FONTFILE, $code[$i]);
		}
		//加入干扰线
		for($i = 0; $i < 4; $i++) {
			//设置线的颜色
			$linecolor = imagecolorallocate($image, rand(80, 220), rand(80, 220),rand(80, 220));
			//设置线，两点一线
			imageline($image, rand(1, $width - 1), rand(1, $height - 1), rand(1, $width - 1), rand(1, $height - 1), $linecolor);
		}
		//加入干扰象素
		for ($i = 0; $i < 300; $i++) {
			//设置点的颜色
			$pointcolor = imagecolorallocate($image, rand(50, 200), rand(50, 200), rand(50, 200));
			//imagesetpixel画一个单一像素
			imagesetpixel($image, rand(0, $width), rand(0, $height), $pointcolor);
		}
		header('Content-Type: image/png');
		//生成png图片
		imagepng($image);
		//销毁$image
		imagedestroy($image);
		return true;
	}

	public function text($src, $text, $font_size, $angle, $x, $y, $colorRGB= [255,255,255], $alpha = 0, $fontfile = '')
	{
		if (!is_file($src)) return false;
		$imgHandler = imagecreatefrompng($src);
		if(empty($fontfile)){
			$fontfile = self::FONTFILE;
		}
		$color = imagecolorallocatealpha($imgHandler,$colorRGB[0],$colorRGB[1],$colorRGB[2],$alpha);
		imagettftext($imgHandler,$font_size, $angle, $x, $y, $color, $fontfile, $text);
		header('Content-Type: image/png');
		//生成png图片
		imagepng($imgHandler);
		//销毁$imgHandler
		imagedestroy($imgHandler);
		return true;
	}

	public function compressImg($src, $moveto = '', $percent = 1)
	{
		if (!extension_loaded('gd')) {
			return false;
		}
		if (!is_file($src)) return false;
		//图片信息
		$srcImageInfo = getimagesize($src);
		$srcImageWidth = $srcImageInfo[0];
		$srcImageHeight = $srcImageInfo[1];
		$srcImageMime = $srcImageInfo['mime'];
		$imagecreatefromfunc = $imagefunc = null;
		switch($srcImageMime) {
			case 'image/jpeg':
				$imagecreatefromfunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
				$imagefunc = function_exists('imagejpeg') ? 'imagejpeg' : '';
				break;
			case 'image/gif':
				$imagecreatefromfunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
				$imagefunc = function_exists('imagegif') ? 'imagegif' : '';
				break;
			case 'image/png':
				$imagecreatefromfunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
				$imagefunc = function_exists('imagepng') ? 'imagepng' : '';
				break;
		}
		if (empty($imagecreatefromfunc) || empty($imagefunc)) {
			return false;
		}
	 
		$srcImage = $imagecreatefromfunc($src);

		$new_w = $srcImageWidth * $percent;
		$new_h = $srcImageHeight * $percent;
		$returnPic = imagecreatetruecolor($new_w, $new_h);

		imagealphablending($returnPic, true);
		imagesavealpha($returnPic, true);
		$white = imagecolorallocatealpha($returnPic, 255, 255, 255, 127);//白色
		imagefill($returnPic, 0, 0, $white);

		imagecopyresampled($returnPic, $srcImage, 0, 0, 0, 0, $new_w, $new_h, $srcImageWidth, $srcImageHeight);
		if (empty($moveto))
			$moveto = $src;

		$dirPath = dirname($moveto);
		if (!is_dir($dirPath)) {
			mkdir($dirPath, 0755, true);
		}

		$imagefunc($returnPic, $moveto);
		imagedestroy($returnPic);
		imagedestroy($srcImage);
		clearstatcache();
		return true;
	}

	public function thumbImage($src, $moveto, $outputWidth = 600, $outputHeight = 600)
	{
		if (!extension_loaded('gd')) {
			return false;
		}
		if (!is_file($src)) return false;
		//图片信息
		$srcImageInfo = getimagesize($src);
		$srcImageWidth = $srcImageInfo[0];
		$srcImageHeight = $srcImageInfo[1];
		$srcImageMime = $srcImageInfo['mime'];

		$movetoPath = pathinfo($moveto, PATHINFO_DIRNAME);
		if (!is_dir($movetoPath)) {
			mkdir($movetoPath, 0755, true);
		}

		if ($srcImageWidth == $outputWidth && $srcImageHeight == $outputHeight) {
			if ($src != $moveto) {
				copy($src, $moveto);
			}
			return true;
		}
		$imagecreatefromfunc = $imagefunc = null;
		switch($srcImageMime) {
			case 'image/jpeg':
				$imagecreatefromfunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
				$imagefunc = function_exists('imagejpeg') ? 'imagejpeg' : '';
				break;
			case 'image/gif':
				$imagecreatefromfunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
				$imagefunc = function_exists('imagegif') ? 'imagegif' : '';
				break;
			case 'image/png':
				$imagecreatefromfunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
				$imagefunc = function_exists('imagepng') ? 'imagepng' : '';
				break;
		}
		if ($imagecreatefromfunc == '' || $imagefunc == '') {
			return false;
		}
		$srcImage = $imagecreatefromfunc($src);

		//计算有效长度
		if ($outputWidth > $srcImageWidth && $outputHeight > $srcImageHeight) {
			if ($srcImageWidth > $srcImageHeight) {
				$outputWidth = $outputHeight = $srcImageWidth;
			} else {
				$outputWidth = $outputHeight = $srcImageHeight;
			}
		} 

		//创建画布
		$returnPic = imagecreatetruecolor($outputWidth, $outputHeight);

		//returnPic-输出图,img-拷贝的原图,dst_x-目标X坐标,dst_y-目标Y坐标,src_x-源X坐标,src_y-源Y坐标,dst_w-目标宽,dst_h-目标高,src_w-源宽,src_h-源高
		$dst_x = $dst_y = $src_x = $src_y = $diff_x = $diff_y = 0;

		$src_w = $srcImageWidth;
		$src_h = $srcImageHeight;

		if ($srcImageWidth > $srcImageHeight) {
			$ratio = $outputWidth / $srcImageWidth;
		} else {
			$ratio = $outputHeight / $srcImageHeight;
		}
		$real_h = $srcImageHeight * $ratio;
		//上下留白
		$diff_y = ($outputHeight - $real_h) / 2;

		$real_w = $srcImageWidth * $ratio;
		//左右留白
		$diff_x = ($outputWidth - $real_w) / 2;

		imagealphablending($returnPic, true);
		imagesavealpha($returnPic, true);
		$white = imagecolorallocatealpha($returnPic, 255, 255, 255, 127);//白色
		imagefill($returnPic, 0, 0, $white);

		imagecopyresampled($returnPic, $srcImage, $dst_x + $diff_x, $dst_y + $diff_y, $src_x, $src_y, $real_w, $real_h, $src_w, $src_h);
		$dirPath = dirname($moveto);
		if (!is_dir($dirPath)) {
			mkdir($dirPath, 0755, true);
		}
		$imagefunc($returnPic, $moveto);
		imagedestroy($returnPic);
		imagedestroy($srcImage);
		clearstatcache();
		return true;
	}
}