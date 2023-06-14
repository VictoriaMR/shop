<?php

namespace app\service;

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
		$attachment = make('app/service/attachment/Attachment');
		$data = $attachment->getAttachmentByName($name, $cate);
		if (empty($data)) {
			$path = ROOT_PATH.$this->savePath.DS.$cate.DS;
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
		$tempArr = [];
		foreach ($urlArr as $value) {
			if (empty($value)) continue;
			$tempArr[$value] = md5($this->filterUrl($value));
		}
		if (empty($tempArr)) {
			return false;
		}
		$urlArr = $tempArr;

		$attachUrl = make('app/service/attachment/Url');
		$http = make('frame/Http');
		$attachment = make('app/service/attachment/Attachment');
		$image = make('app/service/Image');

		$list = $attachUrl->getListData(['url_md5'=>['in', array_values($urlArr)]], 'attach_id,url_md5');
		if (!empty($list)) {
			$list = array_column($list, 'attach_id', 'url_md5');
		}
		$root_path = ROOT_PATH.$this->savePath.DS;
		$path = $root_path.$cate.DS;
		//创建目录
		if (!is_dir($path)) {
			mkdir($path, 0755, true);
		}

		$insert = [];
		foreach ($urlArr as $key => $value) {
			if (isset($list[$value])) {
				$urlArr[$key] = $list[$value];
				//查找类目下是否有对应的cate
				$info = $attachment->loadData($list[$value]);
				if ($info['cate'] != $cate) {
					$name = $info['name'];
					$ext = $info['type'];
					$tempName = $root_path.$info['cate'].DS.$name.'.'.$ext;
					$data = $attachment->getAttachmentByName($name, $cate);
					if (empty($data)) {
						$file = $path.$name.'.'.$ext;
						//存入压缩文件
						$image->compressImg($tempName, $file);
						$data = [
							'name' => $name,
							'type' => $ext,
							'cate' => $cate,
						];
						$attachId = $attachment->insertGetId($data);
						$urlArr[$key] = $attachId;
						//图片缩略
						if ($thumb) {
							$thumb = ['600', '400', '200'];
							foreach ($thumb as $tv) {
								$to = $path.$name.DS.$tv.'.'.$ext;
								$image->thumbImage($file, $to, $tv, $tv);
							}
						}
					}
				}
			} else {
				$url = $key;
				//生成临时文件
				$ext = explode('?', pathinfo($url, PATHINFO_EXTENSION))[0];
				$tempName = $path.'temp_'.$value.'.'.$ext;
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
					$data = $attachment->getAttachmentByName($name, $cate);
					if (empty($data)) {
						$file = $path.$name.'.'.$ext;
						//存入压缩文件
						$image->compressImg($tempName, $file);
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
							foreach ($thumb as $tv) {
								$to = $path.$name.DS.$tv.'.'.$ext;
								$image->thumbImage($file, $to, $tv, $tv);
							}
						}
					}
					$urlArr[$key] = $data['attach_id'];
					$insert[$data['attach_id']] = [
						'attach_id' => $data['attach_id'],
						'url_md5' => $value,
						'url' => $key,
					];
					@unlink($tempName);
				} else {
					unset($urlArr[$key]);
					continue;
				}
			}
		}
		$attachUrl->insert($insert);
		return $urlArr;
	}

	protected function filterUrl($url)
	{
		return str_replace(['.200x200', '.400x400', '.600x600', '.800x800', '_.webp'], '', explode('?', $url)[0]);
	}
}
