<?php

namespace app\task\main;
use app\task\TaskDriver;

class CompressStatic extends TaskDriver
{
	public $config = [
        'name' => '静态文件压缩任务',
        'cron' => ['0 3 * * *'],
    ];

	public function run()
	{
		$systemStaticFile = make('app/service/site/StaticFile');
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
		return false;
	}
}
