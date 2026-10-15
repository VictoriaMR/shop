<?php 

namespace app\service\site;
use app\service\Base;

class LanguageUsed extends Base
{
	const CACHE_KEY = 'site-language-used:';

	public function getList($siteId = null)
	{
		if (is_null($siteId)) {
			$siteId = siteId();
		}
		$list = $this->getListData(['site_id' => $siteId], '*', 0, 0, ['sort' => 'asc']);
		if (empty($list)) {
			return [];
		}
		$service = service('system/Language');
		foreach ($list as $key => $value) {
			$info = $service->getInfoCache($value['code']);
			if (is_array($info)) {
				$list[$key] += $info;
			}
		}
		return $list;
	}

	public function getListCache($siteId = null)
	{
		if (is_null($siteId)) {
			$siteId = siteId();
		}
		$cacheKey = $this->getCacheKey($siteId);
		$list = redis()->get($cacheKey);
		if (false === $list) {
			$list = $this->getList($siteId);
			redis()->set($cacheKey, $list);
		}
		return is_array($list) ? $list : [];
	}

	public function delCache($siteId)
	{
		return redis()->del($this->getCacheKey($siteId));
	}

	protected function getCacheKey($siteId)
	{
		return self::CACHE_KEY . (is_null($siteId) ? siteId() : $siteId);
	}
}