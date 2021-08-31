<?php 

namespace app\service\site;
use app\service\Base;

class LanguageRelation extends Base
{
	const CACHE_KEY = 'site_language:';

	protected function getModel()
	{
		$this->baseModel = make('app/model/site/LanguageRelation');
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
		$cacheKey = $this->getCacheKey($siteId);
		$list = redis()->get($cacheKey);
		if (false === $list) {
			$list = $this->getList($siteId);
			redis()->set($cacheKey, $list);
		}
		return $list;
	}

	public function delCache($siteId=null)
	{
		return resdis()->del($this->getCacheKey($siteId));
	}

	protected function getCacheKey($siteId=null)
	{
		return self::CACHE_KEY.(is_null($siteId) ? $this->siteId() : $siteId);
	}
}