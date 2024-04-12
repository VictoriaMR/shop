<?php

namespace app\task\main;
use app\task\TaskDriver;

class CompressStatic extends TaskDriver
{
	public $config = [
        'name' => '静态文件压缩任务',
        'cron' => ['0 */6 * * *'],
    ];

	public function run()
	{
		$list = service('site/Site')->getListData(['status'=>1], 'path');
		if (empty($list)) {
			return false;
		}
		$list = array_unique(array_column($list, 'path'));
		$basePath = ROOT_PATH.'template'.DS;
		$urlArr = [
			'js' => 'https://www.toptal.com/developers/javascript-minifier/api/raw',
			'css' => 'https://tool.oschina.net/action/jscompress/css_compress',
		];
		$http = frame('Http');
		foreach ($list as $value) {
			$dir = $basePath.$value.DS.'static';
			if (!is_dir($dir)) continue;
			$fileList = getDirFile($dir);
			foreach ($fileList as $file) {
				$source = trim(file_get_contents($file));
				if (strpos($source, PHP_EOL) === false) continue;
				$type = pathinfo($file)['extension'];
				$reponse = '';
				$count = 0;
				while (!$reponse && $count < 5) {
					switch($type) {
						case 'js':
							$reponse = $http->post($urlArr[$type], ['input' => $source], ['Content-Type: application/x-www-form-urlencoded']);
							if (isset($reponse['error'])) $reponse = '';
							break;
						case 'css':
							$reponse = $http->post($urlArr[$type], $source, ['Content-Type: text/html;charset=utf-8']);
							$reponse = isJson($reponse)['result'] ?? '';
							break;
					}
					$count++;
				}
				if ($reponse) {
					file_put_contents($file, str_replace(PHP_EOL, '', $reponse));
				}
			}
		}
		return false;
	}
}
