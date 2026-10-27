<?php 

namespace app\service\system;
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

	public function insertCurrency(array $data)
	{
		$rst = $this->insertData($data);
		if ($rst) {
			redis()->del($this->getCacheKey($data['code']));
		}
		return $rst;
	}

	public function deleteCurrency($code)
	{
		$rst = $this->deleteData($code);
		if ($rst) {
			redis()->del($this->getCacheKey($code));
		}
		return $rst;
	}

	public function priceSymbol($type)
	{
		$info = $this->getInfoCache($this->currencyId());
		if (empty($info) || !is_array($info)) {
			$info = ['code' => 'USD', 'symbol' => '$', 'rate' => 6.6814];
		}
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
		if (empty($info) || !is_array($info)) {
			$info = ['code' => 'USD', 'symbol' => '$', 'rate' => 6.6814];
		}
		$rate = (!empty($info['rate']) && (float)$info['rate'] > 0) ? (float)$info['rate'] : 1.0;
		$numPrice = (float)str_replace(',', '', (string)$price);
		$formattedPrice = sprintf('%.2f', $numPrice / $rate);
		$arr = [
			1 => $formattedPrice,
			2 => $info['symbol'].$formattedPrice,
			3 => $info['code'].$formattedPrice,
		];
		if (is_null($type)) {
			return $arr;
		}
		return $arr[$type] ?? $formattedPrice;
	}

	public function getSymbolByCode($code, $type=2)
	{
		$info = $this->getInfoCache($code);
		if (empty($info) || !is_array($info)) {
			return '';
		}
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
		$result = frame('Http')->get('https://www.bankofchina.com/sourcedb/whpj/enindex_1619.html?timestamprand='.time());
        if (!$result) {
            return false;
        }
        $arr = [];
        $matchCount = preg_match_all('/<tr align=\"center\"[^>]*>\s*<td[^>]*>([A-Z]{3})<\/td>\s*<td[^>]*>([0-9\.]+)<\/td>/i', $result, $arr);
        if (!$matchCount) {
            return false;
        }
        $rates = [];
        foreach ($arr[1] as $k => $v) {
            $rates[$v] = number_format($arr[2][$k] / 100, 6, '.', '');
        }
        $logCurrency = service('log/Currency');
        $currencyArr = $this->getListData();
        foreach ($currencyArr as $value) {
            if (!isset($rates[$value['code']]) || $value['rate'] == $rates[$value['code']]) {
                continue;
            }
            $this->updateInfo($value['code'], ['rate'=>$rates[$value['code']]]);
            $logCurrency->insert([
                'code' => $value['code'],
                'old_rate' => $value['rate'],
                'new_rate' => $rates[$value['code']],
            ]);
        }
        return true;
	}
}