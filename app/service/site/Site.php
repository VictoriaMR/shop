<?php 

namespace app\service\site;
use app\service\Base;

class Site extends Base
{
	const CACHE_KEY = 'site:';

	public function getInfoCache($key, $type='site_id')
	{
		$cacheKey = $this->getCacheKey($type, $key);
		$info = redis(2)->get($cacheKey);
		if (!$info) {
			$info = $this->getInfo($key, $type);
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
		return $this->loadData([$type=>$key, 'status'=>1], 'site_id,type,path,name,domain,cate_id,email,view_cache,static_cache,debug');
	}

	public function getCountryCode()
	{
		$countryCode = frame('Session')->get('default_country_code');
		if (true || !$countryCode) {
			$countryCode = frame('IP')->getIpCountry()['country']['iso_code'] ?? 'US';
			frame('Session')->set('default_country_code', $countryCode);
		}
		return $countryCode;
	}
}