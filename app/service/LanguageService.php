<?php 

namespace app\service;

use app\service\Base as BaseService;

/**
 * 	语言类
 */
class LanguageService extends BaseService
{	
	const CACHE_KEY = 'SITE_LANGUAGE_CACHE';

	public function create(array $data)
	{
		if (empty($data['code']) || empty($data['name'])) {
			return false;
		}
        $data = [
            'code' => $data['code'],
            'name' => $data['name'],
            'sort' => $data['sort'] ?? 0,
        ];
        return make('App\Models\Language')->create($data);
	}

    public function getInfo($lanId = '')
    {
    	$info = make('app/model/Language')->getListData();
    	if (!empty($info)) {
    		$info = array_column($info, null, 'lan_id');
    	}
    	if (empty($lanId)) {
    		return $info;
    	}
    	return $info[$lanId] ?? [];
    }

    protected function getCacheKey()
    {
    	return self::CACHE_KEY;
    }

    public function getInfoCache($lanId = '')
    {
    	$info = redis()->get($this->getCacheKey());
    	if (empty($info)) {
    		$info = $this->getInfo();
    		redis()->set($this->getCacheKey(), $info, -1);
    	}
    	if (empty($lanId)) {
    		return $info;
    	}
    	return $info[$lanId] ?? '';
    }

    public function deleteCache()
    {
    	return redis()->delete($this->getCacheKey());
    }

    public function priceFormat($price, $lanId)
    {
        if ($price <= 0) {
            return [
                'price' => 0,
                'symbol' => '',
            ];
        }
        $info = $this->getInfoCache($lanId);
        return [
            'price' => sprintf('%.2f', $price * $info['rate']),
            'symbol' => $info['symbol'],
        ];
    }
}