<?php 

namespace app\service\site;
use app\service\Base;

class Site extends Base
{
	const CACHE_KEY = 'site:';

	protected function getModel()
	{
		$this->baseModel = make('app/model/site/Site');
	}

	public function getInfoCache($key, $type='site_id')
	{
		$cacheKey = $this->getCacheKey($type, $key);
		$info = redis(2)->get($cacheKey);
		if (!$info) {
			$info = $this->getInfo($key, $type);
			if (empty($info)) {
				return $info;
			}
			redis(2)->set($cacheKey, $info);
		}
		return $info;
	}

	protected function getCacheKey($type, $key)
	{
		return self::CACHE_KEY.$type.':'.$key;
	}

	public function getInfo($key, $type='site_id')
	{
		return $this->loadData([$type=>$key]);
	}
}