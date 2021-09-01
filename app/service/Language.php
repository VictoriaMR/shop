<?php 

namespace app\service;
use app\service\Base;

class Language extends Base
{	
	const CACHE_KEY = 'language:';

	protected function getModel()
	{
		$this->baseModel = make('app/model/Language');
	}

	public function getInfo($code)
	{
		return $this->loadData($code);
	}

	public function getInfoCache($code)
	{
		$cacheKey = $this->getCacheKey($code);
		$info = redis()->get($cacheKey);
		if ($info === false) {
			$info = $this->getInfo($code);
			redis()->set($cacheKey, $info);
		}
		return $info;
	}

	protected function getCacheKey($code)
	{
		return self::CACHE_KEY.$code;
	}

	public function updateInfo($code, array $data)
	{
		$rst = $this->updateData($code, $data);
		if ($rst) {
			redis()->del($this->getCacheKey($code));
		}
		return $rst;
	}

	public function getListCache()
	{
		$cacheKey = $this->getCacheKey('list');
		$list = redis()->get($cacheKey);
		if ($list === false) {
			$list = $this->getListData();
			redis()->set($cacheKey, $list);
		}
		return $list;
	}

	public function getTransList()
	{
		$list = $this->getListCache();
		$list = array_column($list, null, 'code');
		unset($list['zh']);
		return $list;
	}
}