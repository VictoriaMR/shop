<?php 

namespace app\service;
use app\service\Base;

class LanguageService extends Base
{	
	const CACHE_KEY = 'site_language:';
	protected $info;

	protected function getModel()
	{
		$this->baseModel = make('app/model/Language');
	}

	public function getInfoCache($lanId)
	{
		$cacheKey = $this->getCacheKey($lanId);
		$info = redis()->get($cacheKey);
		if (empty($info)) {
			$info = $this->loadData($lanId);
			redis()->set($cacheKey, $info);
		}
		return $info;
	}

	public function getListCache()
	{
		$cacheKey = $this->getCacheKey();
		$list = redis()->get($cacheKey);
		if (empty($list)) {
			$list = $this->getListData();
			redis()->set($cacheKey, $list);
		}
		return $list;
	}

	protected function getCacheKey($lanId='')
	{
		if (empty($lanId)) {
			return self::CACHE_KEY.'lan_list';
		}
		return self::CACHE_KEY.'lan_id_'.$lanId;
	}

	public function deleteCache($lanId)
	{
		redis()->del($this->getCacheKey($lanId));
		redis()->del($this->getCacheKey());
		return true;
	}

	public function priceFormat($price, $type=1)
	{
		if (empty($this->info)) {
			$this->info = $this->getInfoCache(lanId());
		}
		$price = sprintf('%.2f', $price * $this->info['rate']);
		if ($type == 2) {
			$price = $this->info['symbol'].$price;
		} elseif ($type == 3) {
			$price = $this->info['currency'].$price;
		}
		return $price;
	}
}