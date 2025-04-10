<?php

namespace app\service\tool;

class Socket
{
	private $token;
	private $workerUrl;

	public function __construct()
	{
		$this->token = config('token');
		$config = config('socket');
		$this->workerUrl = ($config['ssl']?'https':'http').'://'.$config['domain'].':'.$config['worker_port'];
	}

	protected function request($act, $param='')
	{
		$http = frame('Http');
		if (is_string($param)) parse_str($param, $param);
		$param['act'] = $act;
		$param['token'] = $this->token;
		$rst = $http->post($this->workerUrl, $param);
		if ($rst) $rst = isArray($rst);
		return $rst;
	}

	public function getAutoOnlineList()
	{
		$list = $this->request('online', ['type'=>'crawler']);
		return $list['data'] ?? [];
	}

	public function pushToAuto($uuid, $param)
	{
		$param = ['data'=>$param];
		$param['type'] = 'crawler';
		$param['toType'] = 'reload';
		$param['uuid'] = $uuid;
		return $this->request('push', $param);
	}
}