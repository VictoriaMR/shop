<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Api extends HomeBase
{
	public function stat()
	{
		$path = [];
		$param = ipost();
		if (!empty($param['class'])) {
			$path[] = $param['class'];
			unset($param['class']);
		}
		if (!empty($param['path'])) {
			$path[] = $param['path'];
			unset($param['path']);
		}
		if (!empty($param['func'])) {
			$path[] = $param['func'];
			unset($param['func']);
		}
		$data = [
			'path' => implode('/', $path),
			'param' => json_encode($param),
		];
		$rst = make('app/service/Logger')->addLog($data);
		if ($data['path'] == 'home/Product/index') {
			//更新浏览人数
			make('app/service/product/Spu')->where(['spu_id'=>$param['id']])->increment('visit_total');
		}
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