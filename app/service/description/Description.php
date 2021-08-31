<?php 

namespace app\service\description;
use app\service\Base;

class Description extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/description/Description');
	}

	public function addNotExist($name)
	{
		if (empty($name)) {
			return false;
		}
		$name = trim($name);
		$info = $this->loadData(['name'=>$name], 'desc_id');
		if (!empty($info)) {
			return $info['desc_id'];
		}
		return $this->insertGetId(['name'=>$name]);
	}

	public function getListById($spuId, $lanId=1)
	{
		$list = make('app/service/product/DescriptionRelation')->getListData(['spu_id'=>$spuId], 'name_id,value_id', 0, 0);
		$descIdArr = array_unique(array_merge(array_column($list, 'name_id'), array_column($list, 'value_id')));
		$descArr = $this->getListData(['desc_id'=>['in', $descIdArr]]);
		$descArr = array_column($descArr, 'name', 'desc_id');
		//获取语言
		$lanArr = make('app/service/product/DescriptionLanguage')->getListData(['desc_id'=>['in', $descIdArr], 'lan_id'=>$lanId], 0, 0, 'desc_id,name');
		$lanArr = array_column($lanArr, 'name', 'desc_id');
		foreach ($list as $key=>$value) {
			$value['name'] = empty($lanArr[$value['name_id']]) ? $descArr[$value['name_id']] : $lanArr[$value['name_id']];
			$value['value'] = empty($lanArr[$value['value_id']]) ? $descArr[$value['value_id']] : $lanArr[$value['value_id']];
			$list[$key] = $value;
		}
		return $list;
	}
}