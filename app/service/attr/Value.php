<?php 

namespace app\service\attr;
use app\service\Base;

class Value extends Base
{
	const CACHE_KEY = 'product-attr:value_cache:';

	protected function getModel()
	{
		return $this->baseModel = make('app/model/attr/Value');
	}

	public function addNotExist($nameArr)
	{
		if (empty($nameArr)) return false;
		if (!is_array($nameArr)) $nameArr = [$nameArr];
		$nameArr = array_unique($nameArr);
		//获取已存在属性
		$list = $this->getListData(['name'=>['in', $nameArr]], 'attrv_id,name');
		$list = array_column($list, 'attrv_id', 'name');
		$diffArr = array_diff($nameArr, array_keys($list));
		if (empty($diffArr)) {
			return $list;
		}
		$tempArr = [];
		foreach ($diffArr as $value) {
			$tempArr[] = ['name' => $value];
		}
		$this->insert($tempArr);
		$tempArr = $this->getListData(['name'=>['in', $diffArr]], 'attrv_id,name');
		return array_merge($list, array_column($tempArr, 'attrv_id', 'name'));
	}

	public function getList($where, $page=1, $size=20)
	{
		$list = $this->getListData($where, '*', $page, $size, ['attv_id'=>'desc']);
		if (!empty($list)) {
			//翻译字段 统计
			$tempArr = array_column($list, 'attv_id');
			$tempArr = make('app/service/attr/ValueLanguage')->where(['attv_id'=>['in', $tempArr]])->field('count(*) as count, attv_id')->groupBy('attv_id')->get();
			$tempArr = array_column($tempArr, 'count', 'attv_id');
			//需要翻译的语言列表
			$languageList = make('app/service/Language')->getTransList();
			$len = count($languageList);
			$transArr = [
				0 => [],
				1 => [],
				2 => [],
			];
			foreach ($list as $key => $value) {
				$status = empty($tempArr[$value['attv_id']]) ? 0 : ($tempArr[$value['attv_id']] < $len ? 1 : 2);

				if ($status != $value['status']) {
					$transArr[$status][] = $value['attv_id'];
				}

				$value['is_translate'] = $status;
				$list[$key] = $value;
			}
			foreach ($transArr as $key => $value) {
				if (empty($value)) continue;
				$this->updateData(['attv_id'=>['in', $value]], ['status'=>$key]);
			}
		}
		return $list;
	}

	public function getListById($attvId, $lanId=1)
	{
		$list = $this->getListData(['attv_id'=>['in', $attvId]], 'attv_id,name');
		$lanArr = make('app/service/attr/ValueLanguage')->getListData(['attv_id'=>['in', $attvId], 'lan_id'=>$lanId], 'attv_id,name');
		$lanArr = array_column($lanArr, 'name', 'attv_id');
		foreach ($list as $key => $value) {
			if (!empty($lanArr[$value['attv_id']])) {
				$list[$key]['name'] = $lanArr[$value['attv_id']];
			}
		}
		return $list;
	}
}