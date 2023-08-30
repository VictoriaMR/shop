<?php 

namespace app\service\currency;
use app\service\Base;

class Currency extends Base
{	
	const CACHE_KEY = 'currency:';

	public function getInfoCache($code)
	{
		$cacheKey = $this->getCacheKey($code);
		$info = redis()->get($cacheKey);
		if ($info === false) {
			$info = $this->loadData($code);
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
		$info = $this->getInfoCache($this->currencyId());
		switch ($type) {
			case '1':
				return $info['code'];
			case '2':
				return $info['symbol'];
		}
		return '';
	}

	public function priceFormat($price, $type=null)
	{
		$info = $this->getInfoCache($this->currencyId());
		$price = sprintf('%.2f', $price*$info['rate']);
		$arr = [
			1 => $price,
			2 => $info['symbol'].$price,
			3 => $info['code'].$price,
		];
		if (is_null($type)) {
			return $arr;
		}
		return $arr[$type];
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

	public function updateRate()
	{
		$result = make('frame/Http')->get('https://www.bankofchina.com/sourcedb/whpj/enindex_1619.html?timestamprand='.time());
        if (!$result) {
            return false;
        }
        $arr = [];
        $result = preg_match_all('/<tr align=\"center\">([\s\r\n]+)<td bgcolor=\"#FFFFFF\">([A-Z]{3})<\/td>([\s\r\n]+)<td bgcolor=\"#FFFFFF\">([0-9\.]+)<\/td>/', $result, $arr);
        if (!$result) {
            return false;
        }
        $result = [];
        foreach ($arr[2] as $k => $v) {
            $result[$v] = number_format(1/$arr[4][$k]*100, 6, '.', '');
        }
        $logger = make('app/service/currency/Logger');
        $site = make('app/service/site/Site');
        $currencyArr = $this->getListData();
        foreach ($currencyArr as $value) {
            if (!isset($result[$value['code']]) || $value['rate'] == $result[$value['code']]) {
                continue;
            }
            $this->updateData($value['code'], ['rate'=>$result[$value['code']]]);
            $logger->insert([
                'code' => $value['code'],
                'old_rate' => $value['rate'],
                'new_rate' => $result[$value['code']],
            ]);
            $site->deleteTemplateCache(0, true, false, $value['code']);
        }
        return false;
	}
}