<?php

namespace app\task\main;
use app\task\TaskDriver;

class CompressStatic extends TaskDriver
{
	public function __construct($process=[])
	{
		parent::__construct($process);
		if ($process !== false) {
			$this->lockTimeout = config('task.timeout');
			// 每运行6小时退出一次
			$this->runTimeLimit = 60*60*6;
		}
		$this->config['info'] = '静态文件压缩任务';
		$this->config['cron'] = ['* 3 * * *']; //每天3点整运行
	}

	public function run()
	{
		$systemStaticFile = make('app/service/SystemStaticFile');
		$staticList = $systemStaticFile->getListData(['status'=>0]);
		if (!empty($staticList)) {
			$urlArr = [
				'js' => 'https://javascript-minifier.com/raw',
				'css' => 'https://tool.oschina.net/action/jscompress/css_compress',
			];
			$http = make('frame/Http');
			foreach ($staticList as $value) {
				$file = ROOT_PATH.$value['name'].'.'.$value['type'];
				if (is_file($file)) {
					$source = file_get_contents($file);
					if (empty($source)) {
						$systemStaticFile->updateData($value['static_id'], ['status'=>1]);
						continue;
					}
					switch($value['type']) {
						case 'js':
							$reponse = $http->post($urlArr[$value['type']], ['input' => $source], ['Content-Type: application/x-www-form-urlencoded']);
							if (!empty($reponse['error'])) {
								$reponse = '';
							}
							break;
						case 'css':
							$reponse = $http->post($urlArr[$value['type']], $source, ['Content-Type: text/html;charset=utf-8']);
							$reponse = isJson($reponse)['result'] ?? '';
							break;
					}
					if (!empty($reponse)) {
						file_put_contents($file, str_replace(PHP_EOL, '', $reponse));
						$systemStaticFile->updateData($value['static_id'], ['status'=>1]);
					}
				}
			}
		}
	}
}