<?php

namespace app\service\tool;

class File
{
	const FILE_TYPE = ['avatar', 'product', 'banner', 'introduce', 'category'];
	const FILE_ACCEPT = ['jpg', 'jpeg', 'png'];
	const FILE_COMPERSS = ['jpg', 'jpeg', 'png'];
	const MAX_OFFSET = 1200;

	protected $savePath = 'storage';

	public function upload($file, $cate, $thumb = true)
	{
		if (!in_array($cate, self::FILE_TYPE)) return false;
		$ext = explode('/', $file['type'])[1] ?? '';
		if (!in_array($ext, self::FILE_ACCEPT)) return false;
		$name = md5_file($file['tmp_name']);
		$attachment = service('attachment/Attachment');
		$data = $attachment->getAttachmentByName($name, $cate);
		if (empty($data)) {
			$path = createDir(ROOT_PATH.$this->savePath.DS.$cate.DS);
			$saveUrl = $path.$name.'.'.$ext;
			$result = move_uploaded_file($file['tmp_name'], $saveUrl);
			if (!$result) {
				return false;
			}
			$image = service('Image');
			$image->compressImg($saveUrl);
			$data = [
				'name' => $name,
				'type' => $ext,
				'cate' => $cate,
			];
			$attachId = $attachment->insertGetId($data);
			$data['attach_id'] = $attachId;
			//图片缩略
			if ($thumb) {
				$thumb = ['600', '400', '200'];
				foreach ($thumb as $value) {
					$to = $path.$name.DS.$value.'.'.$ext;
					$image->thumbImage($saveUrl, $to, $value);
				}
			}
			$data = $attachment->urlInfo($data);
		}
		return $data;
    }

	public function uploadUrlImage($urlArr, $cate, $thumb=true)
	{
		if (!in_array($cate, self::FILE_TYPE)) return false;
		if (!is_array($urlArr)) $urlArr = [$urlArr];
		$urlArr = array_flip($urlArr);
		foreach ($urlArr as $key=>$value) {
			$urlArr[$key] = md5($this->filterUrl($key));
		}
		$http = frame('Http');
		$image = service('tool/Image');
		$attachment = service('attachment/Attachment');

		$imageList = $attachment->getListData(['name'=>['in', $urlArr]], 'attach_id,name');
		$imageList = array_column($imageList, 'attach_id', 'name');

		$root_path = ROOT_PATH.$this->savePath.DS;
		$path = createDir($root_path.$cate.DS);
		$cachePath = createDir($root_path.'cache'.DS);

		foreach ($urlArr as $key => $value) {
			if (!isset($imageList[$value])) {
				//生成临时文件
				$ext = explode('?', pathinfo($key, PATHINFO_EXTENSION))[0];
				$tempName = $cachePath.'temp_'.$value.'.'.$ext;
				//获取文件
				if (strpos($key, 'http') === false) {
					$key = 'https:'.$key;
				}
				$rst = $http->get($key);
				if (!$rst) {
					continue;
				}
				if (file_put_contents($tempName, $rst)) {
					$tmpPath = date('Y').DS.date('m').date('d');
					$file = createDir($path.$tmpPath.DS).$value.'.'.$ext;
					//存入压缩文件
					$image->compressImg($tempName, $file);
					$data = [
						'name' => $value,
						'type' => $ext,
						'path' => $cate.DS.$tmpPath,
					];
					$imageList[$value] = $attachment->insertGetId($data);
					//图片缩略
					if ($thumb) {
						$thumb = ['600', '400', '200'];
						foreach ($thumb as $tv) {
							$to = $path.$tmpPath.DS.$value.'_'.$tv.'.'.$ext;
							$image->thumbImage($file, $to, $tv);
						}
					}
					unlink($tempName);
				}
			}
		}
		foreach ($urlArr as $key=>$value) {
			if (isset($imageList[$value])) {
				$urlArr[$key] = $imageList[$value];
			} else {
				unset($urlArr[$key]);
			}
		}
		return $urlArr;
	}

	protected function filterUrl($url)
	{
		return str_replace(['.200x200', '.400x400', '.600x600', '.800x800', '_.webp'], '', explode('?', $url)[0]);
	}
}
