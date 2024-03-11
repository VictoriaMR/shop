<?php 

namespace app\service\site;
use app\service\Base;

class Domain extends Base
{
	const CACHE_KEY = 'site-currency-used:';

	public function getInfo($siteId=null)
	{
		if (is_null($siteId)) {
			$siteId = $this->siteId();
		}
		$cacheKey = $this->getCacheKey($siteId);
		$list = redis()->get($cacheKey);
		if (false === $list) {
			$list = $this->getList($siteId);
			redis()->set($cacheKey, $list);
		}
		return $list;
	}

	public function delCache($siteId)
	{
		return redis()->del($this->getCacheKey($siteId));
	}

	protected function getCacheKey($siteId)
	{
		return self::CACHE_KEY.(is_null($siteId) ? $this->siteId() : $siteId);
	}
}