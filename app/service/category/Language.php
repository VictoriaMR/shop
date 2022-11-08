<?php 

namespace app\service\category;
use app\service\Base;

class Language extends Base
{
	protected $_model = 'app/model/category/Language';

	public function setNxLanguage($cateId, $lanId, $type, $name)
	{
		if (empty($cateId) || empty($name)) {
			return false;
		}
		$where = ['cate_id'=>$cateId, 'lan_id'=>$lanId, 'type'=>$type];
		if ($this->getCountData($where)) {
			return $this->updateData($where, ['name' => $name]);
		} else {
			$where['name'] = $name;
			return $this->insert($where);
		}
	}
}