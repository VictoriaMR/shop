<?php 

namespace app\service\category;
use app\service\Base;

class Language extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/category/Language');
	}

	public function setNxLanguage($cateId, $lanId, $name)
	{
		if (empty($cateId) || empty($lanId) || empty($name)) {
			return false;
		}
		$where = ['cate_id'=>$cateId, 'lan_id'=>$lanId];
		if ($this->getCountData($where)) {
			return $this->updateData($where, ['name' => $name]);
		} else {
			$where['name'] = $name;
			return $this->insert($where);
		}
	}
}