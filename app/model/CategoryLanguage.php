<?php

namespace app\model;
use app\model\Base;

class CategoryLanguage extends Base
{
	protected $_table = 'category_language';

	public function getInfo($cateId, $lanId)
	{
		return $this->getInfoByWhere(['cate_id' => $cateId, 'lan_id' => $lanId]);
	}

	public function existData($cateId, $lanId) 
	{
		return $this->getCount(['cate_id' => $cateId, 'lan_id' => $lanId]) > 0;
	}

	public function create(array $data) 
	{
		if (empty($data['cate_id']) || empty($data['lan_id']) || empty($data['name'])) {
			return false;
		}
		if ($this->existData($data['cate_id'], $data['lan_id'])) {
			return true;
		}
		return $this->insertGetId($data);
	}
}