<?php

namespace app\controller\bag;
use app\controller\Controller;

class ApiController extends Controller
{
	public function stat()
	{
		$url = ipost('url');
		$service = make('app/service/LoggerService');
		$data = [
			'path' => $url,
			'type' => $service->getConst('TYPE_BEHAVIOR'),
		];
		$service->addLog($data);
		if (empty(userId())) {
			$this->error('need login');
		}
		$this->success();
	}

	public function upload()
	{	
		$file = $_FILES['file'] ?? [];
		if (empty($file)) {
			$this->error('上传数据为空');
		}
		$cate = $_POST['cate'] ?? '';
		$fileService = make('app/service/FileService');

		if (!in_array($cate, $fileService::FILE_TYPE)) {
			$this->error('没有权限操作'.$cate.'文件夹');
		}
		$result = $fileService->upload($file, $cate);
		if (empty($result)) {
			$this->error('上传失败');
		}
		$this->success($result);
	}
}