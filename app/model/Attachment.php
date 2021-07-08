<?php

namespace app\model;
use app\model\Base;

class Attachment extends Base
{
	protected $_table = 'attachment';
	protected $_primaryKey = 'attach_id';

	public function create($data)
	{
		if (empty($data['name'])) return false;
		$insert = [
			'name' => $data['name'],
			'type' => $data['type'],
			'cate' => $data['cate'],
			'size' => $data['size'] ?? 0,
			'add_time' => now(),
		];
		return $this->insertGetId($insert);
	}

	public function getAttachmentByName($name)
	{
		if (empty($name)) return false;
		return $this->getInfoByWhere(['name' => $name]);
	}

	public function isExist($name)
	{
		if (empty($name)) return false;
		return $this->getCount(['name' => $name]) > 0;
	}

	public function getListById($idArr = [])
	{
		if (empty($idArr)) return [];

		if (!is_array($idArr))
		$idArr = [(int) $idArr];

		return $this->whereIn($this->_primaryKey, $idArr)
					->field('attach_id, name, type, cate')
					->get();
	}
}