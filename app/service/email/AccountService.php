<?php 

namespace app\service\email;
use app\service\Base;

class AccountService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/email/Account');
	}

	public function getInfoCache($id)
	{
		$cacheKey = $this->getCacheKey($id);
		$info = redis()->get($cacheKey);
		if (empty($info)) {
			$info = $this->loadData($id);
			redis()->set($cacheKey, $info);
		}
		return $info;
	}


	protected function getCacheKey($id)
	{
		return 'email-account:'.$id;
	}
}