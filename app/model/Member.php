<?php

namespace app\model;
use app\model\Base;

class Member extends Base
{
	protected $_table = 'member';
	protected $_primaryKey = 'mem_id';
	const INFO_CACHE_TIMEOUT = 3600 *24;

	public function isExistUserByMobile($mobile)
	{
		if (empty($mobile)) return false;
		return $this->getCount(['mobile' => $mobile])['count'] ?? 0;
	}

	public function getInfoByMobile($mobile)
	{
		return $this->getInfoByWhere(['mobile' => $mobile]);
	}

	public function getInfo($userId)
	{
		return $this->loadData($userId);
	}
}