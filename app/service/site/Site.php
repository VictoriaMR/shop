<?php 

namespace app\service\site;
use app\service\Base;

class Site extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/site/Site');
	}

	public function updateCache($id, $status=0, $data=[])
	{
		if ($status) {
			return redis()->hMset($this->getCacheKey($id), $data);
		} else {
			make('app/service/site/LanguageUsed')->delCache($id);
			make('app/service/site/CurrencyUsed')->delCache($id);
			make('app/service/site/Domain')->delCacheBySiteId($id);
			return redis()->hDel($this->getCacheKey($id));
		}
	}

	protected function getCacheKey($id)
	{
		return $this->getConst('CACHE_KEY_LANGUAGE').$id;
	}
}