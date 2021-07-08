<?php

namespace app\model;
use app\model\Base;

class AttributeLanguage extends Base
{
	protected $_table = 'attribute_language';

	public function getInfo($attrId, $lanId)
	{
		return $this->getInfoByWhere(['attr_id' => $attrId, 'lan_id' => $lanId]);
	}

	public function existData($attrId, $lanId) 
	{
		return $this->getCount(['attr_id' => $attrId, 'lan_id' => $lanId]) > 0;
	}

	public function create(array $data) 
	{
		if (empty($data['attr_id']) || empty($data['lan_id']) || empty($data['name'])) {
			return false;
		}
		if ($this->existData($data['attr_id'], $data['lan_id'])) {
			return true;
		}
		return $this->insertGetId($data);
	}
}