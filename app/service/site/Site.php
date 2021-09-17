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

	public function getInfo()
	{
		$info = $this->loadData($this->siteId());
		if (!empty($info)) {
			//获取翻译
			$trInfo = make('app/service/site/Language')->getListData(['site_id'=>$this->siteId(), 'lan_id'=>$this->lanId()]);
			if (!empty($trInfo)) {
				$trInfo = array_column($trInfo, 'name', 'type');
				$info = array_merge($info, $trInfo);
			}
		}
		return $info;
	}

	public function getName()
	{
		$info = $this->getInfoCache();
		return $info['name'] ?? '';
	}

	public function getInfoCache()
	{
		$cacheKey = $this->getCacheKey();
		$info = redis()->get($cacheKey);
		if (false === $info) {
			$info = $this->getInfo();
			redis()->set($cacheKey, $info);
		}
		return $info;
	}

	public function delCache()
	{
		return redis()->del($this->getCacheKey());
	}

	public function delSiteCache($siteId)
	{
		//语言列表
		$list = make('app/service/Language')->getListData();
		foreach ($list as $value) {
			redis()->del($this->getCacheKey($siteId, $value['code']));
		}
		return true;
	}

	protected function getCacheKey($siteId=null, $lanId=null)
	{
		return self::CACHE_KEY.(is_null($siteId) ? $this->siteId() : $siteId).'-'.(is_null($lanId) ? $this->lanId() : $lanId);
	}
}