<?php 

namespace app\service\site;
use app\service\Base;

class Domain extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/site/Domain');
	}

	public function updateCache($id)
	{
		$info = $this->loadData($id);
		if (empty($info)) return false;
		$siteInfo = make('app/service/site/Site')->loadData($info['site_id'], 'site_id,path,name');
		if (empty($siteInfo)) {
			return false;
		}
		return $this->updateCacheByDomain($info['domain'], $info['status'], $siteInfo);
		
	}

	public function updateCacheByDomain($domain, $status=0, $data=[])
	{
		if ($status) {
			redis()->hSet($this->getConst('CACHE_KEY_PATH'), $domain, $data['path']);
			redis()->hSet($this->getConst('CACHE_KEY_INFO'), $domain, $data);
		} else {
			redis()->hDel($this->getConst('CACHE_KEY_PATH'), $domain);
			redis()->hDel($this->getConst('CACHE_KEY_INFO'), $domain);
		}
		return true;
	}

	public function delCacheBySiteId($siteId)
	{
		$list = $this->getListData(['site_id'=>$siteId], 'domain');
		foreach ($list as $value) {
			$this->updateCacheByDomain($value['domain']);
		}
		return true;
	}
}