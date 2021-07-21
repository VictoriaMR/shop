<?php

namespace app\service;
use app\service\Base;

class TranslateService extends Base
{
	const CACHE_KEY = 'site_translate:';

	protected function getModel()
	{
		$this->baseModel = make('app/model/Translate');
	}

	public function getTranslate($text, $to = 'en', $from = 'zh')
	{
		if ($to == $from) {
			return $text;
		}
		$lanList = make('app\service\LanguageService')->getListCache();
		$lanList = array_column($lanList, 'tr_code', 'code');
		if (isset($lanList[$to])) {
			$to = $lanList[$to];
		}
		$salt = time();
		$data = [
			'q' => $text,
			'from' => $from,
			'to' => $to,
			'appid' => env('BAIDU_APPID'),
			'salt' => $salt,
			'sign' => md5(env('BAIDU_APPID').$text.$salt.env('BAIDU_SECRET_KEY')),
		];
		$http_url = 'http://api.fanyi.baidu.com/api/trans/vip/translate';
		$request = $http_url.'?'.http_build_query($data);
		for ($i = 0; $i < 5; $i ++) {
			$translateStr = \frame\Http::get($request);
			if ($translateStr !== false) {
				$translateStr = json_decode($translateStr, true);
				if (!empty($translateStr['trans_result'][0]['dst'])) {
					return trim($translateStr['trans_result'][0]['dst']);
				}
			}
		}
		return '';
	}

	public function getText($name)
	{
		if (empty($name)) return '';
		$code = session()->get('site_language_code');
		$cacheKey = $this->getCacheKey(session()->get('site_language_code'));
		$info = redis(1)->hget($cacheKey, $name);
		if (empty($info)) {
			$info = $this->setNotExist($name, $code);
			if (empty($info)) {
				return $name;
			} else {
				redis(1)->hset($cacheKey, $name, $info);
			}
		}
		return $info;
	}

	protected function getCacheKey($code)
	{
		return self::CACHE_KEY.'site_text_'.$code;
	}

	public function setNotExist($name, $code, $value='')
	{
		$data = [
			'name' => $name,
			'type' => $code, 
		];
		$info = $this->loadData($data);
		if (empty($info)) {
			$this->insert($data);
			return false;
		}
		return $info['value'];
	}

	public function reloadCache()
	{
		$list = $this->getListData(['value'=>['<>', '']]);
		$tempData = [];
		foreach ($list as $key => $value) {
			if (!isset($tempData[$value['type']])) {
				$tempData[$value['type']] = [];
			}
			$tempData[$value['type']][$value['name']] = $value['value'];
		}
		$lanlist = make('app/service/LanguageService')->getListCache();
		foreach ($list as $key => $value) {
			$cacheKey = $this->getCacheKey($value['code']);
			redis(1)->del($cacheKey);
			if (empty($tempData[$value['code']])) continue;
			redis(1)->hmset($cacheKey, $tempData[$value['code']]);
		}
		return true;
	}
}