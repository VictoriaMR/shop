<?php 

namespace app\service;
use app\service\Base;

class CurrencyService extends Base
{	
	const CACHE_KEY = 'currency:';

	protected function getModel()
	{
		$this->baseModel = make('app/model/Currency');
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
}