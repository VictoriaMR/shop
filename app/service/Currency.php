<?php 

namespace app\service;
use app\service\Base;

class Currency extends Base
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

	public function priceSymbol($type)
	{
		$info = $this->getInfoCache(currencyId());
		switch ($type) {
			case '1':
				return $info['code'];
			case '2':
				return $info['symbol'];
		}
		return '';
	}

	public function priceFormat($price)
	{
		$info = $this->getInfoCache(currencyId());
		$price = sprintf('%.2f', $price*$info['rate']);
		return [
			1 => $price,
			2 => $info['symbol'].$price,
			3 => $info['code'].$price,
		];
	}

	public function getSymbolByCode($code, $type=2)
	{
		$info = $this->getInfoCache($code);
		switch ($type) {
			case '1':
				return $info['code'];
			case '2':
				return $info['symbol'];
		}
		return '';
	}
}