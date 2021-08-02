<?php 

namespace app\service;
use app\service\Base;

class SiteService extends Base
{
	const CACHE_KEY = 'site_list:';

	protected function getModel()
	{
		$this->baseModel = make('app/model/Site');
	}

	public function getInfo($siteId, $lanId)
	{
		$info = $this->loadData($siteId);
		if (!empty($info)) {
			$inArr = ['title', 'keyword', 'description'];
			$lanInfo = make('app/model/SiteLanguage')->whereIn('name', $inArr)->where('lan_id', $lanId)->get();
			if (!empty($lanInfo)) {
				$lanInfo = array_column($lanInfo, 'value', 'name');
				foreach ($inArr as $value) {
					$info[$value] = $lanInfo[$value] ?? '';
				}	
			}
		}
		return $info;
	}

	public function getName()
	{
		$info = $this->getInfoCache(siteId(), lanId());
		return $info['name'] ?? '';
	}

	public function getInfoCache($siteId, $lanId=2)
	{
		$cacheKey = $this->getCacheKey($siteId, $lanId);
		$info = redis()->get($cacheKey);
		if (empty($info)) {
			$info = $this->getInfo($siteId, $lanId);
			redis()->set($cacheKey, $info);
		}
		return $info;
	}

	public function deleteCache($siteId, $lanId)
	{
		return redis()->del($this->getCacheKey($siteId, $lanId));
	}

	protected function getCacheKey($siteId, $lanId)
	{
		return self::CACHE_KEY.'site_'.$siteId.'_'.$lanId;
	}

	public function setNxLanguage($siteId, $name, $lanId, $value)
	{
		$where = ['site_id'=>$siteId, 'name'=>$name, 'lan_id'=>$lanId];
		if ($this->getCount($where)) {
			return $this->where($where)->update(['value' => $value]);
		} else {
			$where['value'] = $value;
			return $this->insert($where);
		}
	}
}