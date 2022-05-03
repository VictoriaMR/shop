<?php 

namespace app\service\attr;
use app\service\Base;

class DescriptionValueLanguage extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/attr/DescriptionValueLanguage');
	}

	public function setNxLanguage($id, $lanId, $name)
	{
		if (empty($id) || empty($lanId) || empty($name)) {
			return false;
		}
		$where = ['desc_id'=>$id, 'lan_id'=>$lanId];
		if ($this->getCountData($where)) {
			return $this->updateData($where, ['name' => $name]);
		} else {
			$where['name'] = $name;
			return $this->insert($where);
		}
	}
}