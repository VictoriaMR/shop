<?php

namespace app\service;

class File
{
	const FILE_TYPE = ['avatar', 'product', 'banner', 'introduce', 'category'];
	const FILE_ACCEPT = ['jpg', 'jpeg', 'png'];
	const FILE_COMPERSS = ['jpg', 'jpeg', 'png'];
	const MAX_OFFSET = 1200;

	public function upload($file, $cate, $thumb = true)
	{
		if (!in_array($cate, self::FILE_TYPE)) return false;
		$ext = explode('/', $file['type'])[1] ?? '';
		if (!in_array($ext, self::FILE_ACCEPT)) return false;
		$name = md5_file($file['tmp_name']);
		$attachment = make('app/service/attachment/Attachment');
		$data = $attachment->getAttachmentByName($name);
		if (empty($data)) {
			$path = ROOT_PATH.config('env.FILE_CENTER').DS.$cate.DS;
			//创建目录
			if (!is_dir($path)) {
				mkdir($path, 0755, true);
			}
			$saveUrl = $path.$name.'.'.$ext;
			$result = move_uploaded_file($file['tmp_name'], $saveUrl);
			if (!$result) {
				return false;
			}
			$image = make('app/service/Image');
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
					$image->thumbImage($saveUrl, $to, $value, $value);
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
		$attachmentUrlService = make('app/service/attachment/Url');
		$list = $attachmentUrlService->getListData(['url_md5'=>['in', array_values($urlArr)]]);
		if (!empty($list)) {
			$list = array_column($list, 'attach_id', 'url_md5');
		}
		$dir = ROOT_PATH.config('env.FILE_CENTER').DS;
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}
		$path = $dir.$cate.DS;
		//创建目录
		if (!is_dir($path)) {
			mkdir($path, 0755, true);
		}
		$http = make('frame/Http');
		$attachmentService = make('app/service/attachment/Attachment');
		$imageService = make('app/service/Image');
		$insert = [];
		foreach ($urlArr as $key => $value) {
			if (isset($list[$value])) {
				$urlArr[$key] = $list[$value];
			} else {
				$url = $this->filterUrl($key);
				//生成临时文件
				$ext = pathinfo($url, PATHINFO_EXTENSION);
				$tempName = $dir.randString(32).'.'.$ext;
				//获取文件
				if (strpos($url, 'http') === false) {
					$url = 'https:'.$url;
				}
				$result = $http->get($url);
				if (empty($result)) {
					unset($urlArr[$key]);
					continue;
				}
				if (file_put_contents($tempName, $result)) {
					$name = md5_file($tempName);
					$data = $attachmentService->getAttachmentByName($name);
					if (empty($data)) {
						$file = $path.$name.'.'.$ext;
						//存入压缩文件
						$imageService->compressImg($tempName, $file);
						$data = [
							'name' => $name,
							'type' => $ext,
							'cate' => $cate,
						];
						$attachId = $attachmentService->insertGetId($data);
						$data['attach_id'] = $attachId;
						//图片缩略
						if ($thumb) {
							$thumb = ['600', '400', '200'];
							foreach ($thumb as $tv) {
								$to = $path.$name.DS.$tv.'.'.$ext;
								$imageService->thumbImage($file, $to, $tv, $tv);
							}
						}
					}
					$urlArr[$key] = $data['attach_id'];
					$insert[$data['attach_id']] = [
						'attach_id' => $data['attach_id'],
						'url_md5' => $value,
					];
					unlink($tempName);
				} else {
					unset($urlArr[$key]);
					continue;
				}
			}
		}
		$attachmentUrlService->insert($insert);
		return $urlArr;
	}

	protected function filterUrl($url)
	{
		return str_replace(['.200x200', '.400x400', '.600x600', '.800x800', '_.webp'], '', explode('?', $url)[0]);
	}
}
