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
			$this->error(appT('param_error'));
		}
		$cate = $_POST['cate'] ?? '';
		$fileService = make('app/service/File');

		if (!in_array($cate, $fileService::FILE_TYPE)) {
			$this->error(appT('param_error'));
		}
		$result = $fileService->upload($file, $cate, false);
		if (empty($result)) {
			$this->error('Failed to update Avatar');
		}
		$this->success($result);
	}

	public function notice()
	{
		$url = trim(ipost('url', ''));
		$priority = (int)ipost('priority');
		if (empty($url)) {
			$this->error('url 不能为空');
		}
		make('app/service/supplier/Url')->addUrl($url, $priority);
		$this->success();
	}
}