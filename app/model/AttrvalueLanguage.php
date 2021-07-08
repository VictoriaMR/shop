<?php

namespace app\model;
use app\model\Base;

class AttrvalueLanguage extends Base
{
	protected $_table = 'attrvalue_language';

	public function getInfo($attvId, $lanId)
	{
		return $this->getInfoByWhere(['attv_id' => $attvId, 'lan_id' => $lanId]);
	}

	public function existData($attvId, $lanId) 
	{
		return $this->getCount(['attv_id' => $attvId, 'lan_id' => $lanId]) > 0;
	}

	public function create(array $data) 
	{
		if (empty($data['attv_id']) || empty($data['lan_id']) || empty($data['name'])) {
			return false;
		}
		if ($this->existData($data['attv_id'], $data['lan_id'])) {
			return true;
		}
		return $this->insertGetId($data);
	}
}