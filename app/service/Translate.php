<?php
namespace app\service;
class Translate
{
	public function getText($text, $to = 'en', $from = 'zh')
	{
		if ($to == $from) return $text;
		$salt = time();
		$data = [
			'q' => $text,
			'from' => $from,
			'to' => $to,
			'appid' => config('env.BAIDU_APPID'),
			'salt' => $salt,
			'sign' => md5(config('env.BAIDU_APPID').$text.$salt.config('env.BAIDU_SECRET_KEY')),
		];
		$http_url = 'http://api.fanyi.baidu.com/api/trans/vip/translate';
		$request = $http_url.'?'.http_build_query($data);
		$http = make('frame/Http');
		for ($i = 0; $i < 5; $i ++) {
			$translateStr = $http->get($request);
			if ($translateStr !== false) {
				$translateStr = json_decode($translateStr, true);
				if (isset($translateStr['trans_result'][0]['dst'])) {
					return trim($translateStr['trans_result'][0]['dst']);
				}
			}
		}
		return '';
	}
}