<?php 

namespace app\service\attr;
use app\service\Base;

class ButeLanguage extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/attr/ButeLanguage');
	}

	public function setNxLanguage($id, $lanId, $name)
	{
		if (empty($id) || empty($lanId) || empty($name)) {
			return false;
		}
		$where = ['attr_id'=>$id, 'lan_id'=>$lanId];
		if ($this->getCountData($where)) {
			return $this->updateData($where, ['name' => $name]);
		} else {
			$where['name'] = $name;
			return $this->insert($where);
		}
	}
}