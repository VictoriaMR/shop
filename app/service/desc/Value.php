<?php 

namespace app\service\attr;
use app\service\Base;

class DescriptionValue extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/attr/DescriptionValue');
	}

	public function getList($where, $page=1, $size=20)
	{
		$list = $this->getListData($where, '*', $page, $size, ['desc_id'=>'desc']);
		if (!empty($list)) {
			//翻译字段 统计
			$tempArr = array_column($list, 'desc_id');
			$tempArr = make('app/service/attr/DescriptionLanguage')->where(['desc_id'=>['in', $tempArr]])->field('count(*) as count, desc_id')->groupBy('desc_id')->get();
			$tempArr = array_column($tempArr, 'count', 'desc_id');
			//需要翻译的语言列表
			$languageList = make('app/service/Language')->getTransList();
			$len = count($languageList);
			$transArr = [
				0 => [],
				1 => [],
				2 => [],
			];
			foreach ($list as $key => $value) {
				$status = empty($tempArr[$value['desc_id']]) ? 0 : ($tempArr[$value['desc_id']] < $len ? 1 : 2);

				if ($status != $value['status']) {
					$transArr[$status][] = $value['desc_id'];
				}

				$value['is_translate'] = $status;
				$list[$key] = $value;
			}
			foreach ($transArr as $key => $value) {
				if (empty($value)) continue;
				$this->updateData(['desc_id'=>['in', $value]], ['status'=>$key]);
			}
		}
		return $list;
	}

	public function addNotExist($nameArr)
	{
		if (empty($nameArr)) return false;
		$nameArr = array_unique($nameArr);
		//获取已存在属性
		$list = $this->getListData(['name'=>['in', $nameArr]], 'desc_id,name');
		$list = array_column($list, 'desc_id', 'name');
		$diffArr = array_diff($nameArr, array_keys($list));
		if (empty($diffArr)) {
			return $list;
		}
		$tempArr = [];
		foreach ($diffArr as $value) {
			$tempArr[] = ['name' => $value];
		}
		$this->insert($tempArr);
		$list = $this->getListData(['name'=>['in', $nameArr]], 'desc_id,name');
		$list = array_column($list, 'desc_id', 'name');
		return $list;
	}
	
	public function getListById($spuId, $lanId='en')
	{
		$list = make('app/service/product/DescriptionUsed')->getListData(['spu_id'=>$spuId], 'name_id,value_id', 0, 0);
		$descIdArr = array_unique(array_merge(array_column($list, 'name_id'), array_column($list, 'value_id')));
		$descArr = $this->getListData(['desc_id'=>['in', $descIdArr]]);
		$descArr = array_column($descArr, 'name', 'desc_id');
		//获取语言
		$lanArr = make('app/service/attr/DescriptionLanguage')->getListData(['desc_id'=>['in', $descIdArr], 'lan_id'=>$lanId], 0, 0, 'desc_id,name');
		$lanArr = array_column($lanArr, 'name', 'desc_id');
		foreach ($list as $key=>$value) {
			$value['name'] = empty($lanArr[$value['name_id']]) ? $descArr[$value['name_id']] : $lanArr[$value['name_id']];
			$value['value'] = empty($lanArr[$value['value_id']]) ? $descArr[$value['value_id']] : $lanArr[$value['value_id']];
			$list[$key] = $value;
		}
		return $list;
	}
}