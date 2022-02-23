<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Api extends HomeBase
{
	public function stat()
	{
		$url = ipost('url');
		$service = make('app/service/Logger');
		$data = [
			'path' => explode('.', $url)[0],
			'type' => $service->getConst('TYPE_BEHAVIOR'),
		];
		$service->addLog($data);
		$this->success();
	}

	public function upload()
	{
		$file = $_FILES['file'] ?? [];
		if (empty($file)) {
			$this->error('上传数据为空');
		}
		$cate = $_POST['cate'] ?? '';
		$file = make('app/service/File');

		if (!in_array($cate, $file::FILE_TYPE)) {
			$this->error('没有权限操作'.$cate.'文件夹');
		}
		$result = $file->upload($file, $cate);
		if (empty($result)) {
			$this->error('上传失败');
		}
		$this->success($result);
	}

	public function notice()
	{
		$url = trim(ipost('url'));
		if (empty($url)) {
			$this->error('url 不能为空');
		}
		make('app/service/supplier/Url')->addUrl($url);
		$this->success();
	}
}