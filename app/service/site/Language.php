<?php 

namespace app\service\site;
use app\service\Base;

class Language extends Base
{
	protected $_model = 'app/model/site/Language';

	public function updateCache($siteId, $lanId, $data=[])
	{
		if (empty($data)) {
			$this->deleteData(['site_id'=>$siteId, 'lan_id'=>$lanId]);
			redis()->hDel($this->getCacheKey($siteId, $lanId));
		} else {
			$where = ['site_id'=>$siteId, 'lan_id'=>$lanId];
			foreach ($data as $key => $value) {
				$tempWhere = $where;
				$tempWhere['type'] = $key;
				if ($this->getCountData($tempWhere)) continue;
				$tempWhere['name'] = $value;
				$this->insert($tempWhere);
			}
		}
	}

	protected function getCacheKey($siteId, $lanId)
	{
		return $this->getConst('CACHE_KEY').$siteId.'-'.$lanId;
	}

	public function setNxLanguage($siteId, $lanId, $type, $name='')
	{
		$data = [
			'site_id' => $siteId,
			'lan_id' => $lanId,
			'type' => $type,
		];
		if ($this->getCountData($data)) {
			$this->updateData($data, ['name'=>$name]);
		} else {
			$data['name'] = $name;
			$this->insert($data);
		}
		redis()->hDel($this->getCacheKey($siteId, $lanId));
		return true;
	}
}