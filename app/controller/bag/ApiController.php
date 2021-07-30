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
}