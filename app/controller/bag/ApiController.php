<?php

namespace app\controller\bag;

use app\controller\Controller;

class ApiController extends Controller
{
	public function stat()
	{
		$url = ipost('url');
		if (empty($url)) {
			$this->error('参数不正确');
		}
		$url = parse_url($url);
		$data = [
			'path' => trim($url['path'] ?? '', '/'),
			'query' => $url['query'] ?? '',
		];
		make('app/service/LoggerService')->addLog($data);
		$this->success();
	}
}