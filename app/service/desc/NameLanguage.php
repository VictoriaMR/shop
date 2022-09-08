<?php 

namespace app\service\desc;
use app\service\Base;

class NameLanguage extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/desc/NameLanguage');
	}

	public function setNxLanguage($id, $lanId, $name)
	{
		if (empty($id) || empty($lanId) || empty($name)) {
			return false;
		}
		$where = ['descn_id'=>$id, 'lan_id'=>$lanId];
		if ($this->getCountData($where)) {
			return $this->updateData($where, ['name' => $name]);
		} else {
			$where['name'] = $name;
			return $this->insert($where);
		}
	}
}