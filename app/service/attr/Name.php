<?php 

namespace app\service\attr;
use app\service\Base;

class Name extends Base
{
	const CACHE_KEY = 'product:attr:name_cache:';

	protected function getModel()
	{
		return $this->baseModel = make('app/model/attr/Name');
	}

	public function addNotExist($nameArr)
	{
		if (empty($nameArr)) return false;
		if (!is_array($nameArr)) $nameArr = [$nameArr];
		$nameArr = array_unique($nameArr);
		//获取已存在属性
		$list = $this->getListData(['name'=>['in', $nameArr]], 'attrn_id,name');
		$list = array_column($list, 'attrn_id', 'name');
		$diffArr = array_diff($nameArr, array_keys($list));
		if (empty($diffArr)) {
			return $list;
		}
		$tempArr = [];
		foreach ($diffArr as $value) {
			$tempArr[] = ['name' => $value];
		}
		$this->insert($tempArr);
		$tempArr = $this->getListData(['name'=>['in', $diffArr]], 'attrn_id,name');
		return $list+array_column($tempArr, 'attrn_id', 'name');
	}

	public function getList($where, $page=1, $size=20)
	{
		$list = $this->getListData($where, '*', $page, $size, ['attrn_id'=>'desc']);
		if (!empty($list)) {
			//翻译字段 统计
			$tempArr = array_column($list, 'attrn_id');
			$tempArr = make('app/service/attr/ButeLanguage')->where(['attrn_id'=>['in', $tempArr]])->field('count(*) as count, attrn_id')->groupBy('attrn_id')->get();
			$tempArr = array_column($tempArr, 'count', 'attrn_id');
			//需要翻译的语言列表
			$languageList = make('app/service/Language')->getTransList();
			$len = count($languageList);
			$transArr = [
				0 => [],
				1 => [],
				2 => [],
			];
			foreach ($list as $key => $value) {
				$status = empty($tempArr[$value['attrn_id']]) ? 0 : ($tempArr[$value['attrn_id']] < $len ? 1 : 2);

				if ($status != $value['status']) {
					$transArr[$status][] = $value['attrn_id'];
				}

				$value['is_translate'] = $status;
				$list[$key] = $value;
			}
			foreach ($transArr as $key => $value) {
				if (empty($value)) continue;
				$this->updateData(['attrn_id'=>['in', $value]], ['status'=>$key]);
			}
		}
		return $list;
	}

	public function getListById($attrId, $lanId=1)
	{
		$where = ['attrn_id'=>['in', $attrId]];
		$list = $this->getListData($where, 'attrn_id,name');
		$sort = [];
		if ($lanId == 1) {
			$where['lan_id'] = $lanId;
		} else {
			$sort = ['lan_id'=>'asc'];
			$where['lan_id'] = ['in', [1, $lanId]];
		}
		$lanArr = make('app/service/attr/NameLanguage')->getListData($where, 'attrn_id,name', 0, 0, $sort);
		$lanArr = array_column($lanArr, 'name', 'attrn_id');
		foreach ($list as $key => $value) {
			if (isset($lanArr[$value['attrn_id']])) {
				$list[$key]['name'] = $lanArr[$value['attrn_id']];
			}
		}
		return $list;
	}
}