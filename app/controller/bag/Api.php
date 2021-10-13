<?php

namespace app\controller\bag;
use app\controller\Base;

class Api extends Base
{
	public function stat()
	{
		$url = ipost('url');
		$service = make('app/service/Logger');
		$data = [
			'path' => $url,
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
}