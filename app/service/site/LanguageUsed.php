<?php 

namespace app\service\site;
use app\service\Base;

class LanguageUsed extends Base
{
	const CACHE_KEY = 'site-language-used:';

	protected function getModel()
	{
		$this->baseModel = make('app/model/site/LanguageUsed');
	}

	public function getList($siteId=null)
	{
		if (is_null($siteId)) {
			$siteId = $this->siteId();
		}
		$list = $this->getListData(['site_id'=>$siteId], '*', 0, 0, ['sort'=>'asc']);
		$service = make('app/service/Language');
		foreach ($list as $key => $value) {
			$list[$key] += $service->getInfoCache($value['code']);
		}
		return $list;
	}

	public function getListCache($siteId=null)
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