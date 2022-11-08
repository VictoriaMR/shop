<?php 

namespace app\service\desc;
use app\service\Base;

class GroupLanguage extends Base
{
	protected $_model = 'app/model/desc/GroupLanguage';

	public function setNxLanguage($id, $lanId, $name)
	{
		if (empty($id) || empty($lanId) || empty($name)) {
			return false;
		}
		$where = ['descv_id'=>$id, 'lan_id'=>$lanId];
		if ($this->getCountData($where)) {
			return $this->updateData($where, ['name' => $name]);
		} else {
			$where['name'] = $name;
			return $this->insert($where);
		}
	}
}