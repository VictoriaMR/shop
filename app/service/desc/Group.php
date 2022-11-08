<?php 

namespace app\service\desc;
use app\service\Base;

class Group extends Base
{
	protected $_model = 'app/model/desc/Group';

	public function addNotExist($nameArr)
	{
		if (empty($nameArr)) return false;
		if (!is_array($nameArr)) $nameArr = [$nameArr];
		$nameArr = array_unique($nameArr);
		//获取已存在属性
		$list = $this->getListData(['name'=>['in', $nameArr]], 'descg_id,name');
		$list = array_column($list, 'descg_id', 'name');
		$diffArr = array_diff($nameArr, array_keys($list));
		if (empty($diffArr)) {
			return $list;
		}
		$tempArr = [];
		foreach ($diffArr as $value) {
			$tempArr[] = ['name' => $value];
		}
		$this->insert($tempArr);
		$tempArr = $this->getListData(['name'=>['in', $diffArr]], 'descg_id,name');
		return $list + array_column($tempArr, 'descg_id', 'name');
	}

	public function getList($where, $page=1, $size=20)
	{
		$list = $this->getListData($where, '*', $page, $size, ['descg_id'=>'desc']);
		if (!empty($list)) {
			//翻译字段 统计
			$tempArr = array_column($list, 'descg_id');
			$tempArr = make('app/service/desc/GroupLanguage')->where(['descg_id'=>['in', $tempArr]])->field('count(*) as count, descg_id')->groupBy('descg_id')->get();
			$tempArr = array_column($tempArr, 'count', 'descg_id');
			//需要翻译的语言列表
			$languageList = make('app/service/Language')->getTransList();
			$len = count($languageList);
			$transArr = [
				0 => [],
				1 => [],
				2 => [],
			];
			foreach ($list as $key => $value) {
				$status = empty($tempArr[$value['descg_id']]) ? 0 : ($tempArr[$value['descg_id']] < $len ? 1 : 2);

				if ($status != $value['status']) {
					$transArr[$status][] = $value['descg_id'];
				}

				$value['is_translate'] = $status;
				$list[$key] = $value;
			}
			foreach ($transArr as $key => $value) {
				if (empty($value)) continue;
				$this->updateData(['descg_id'=>['in', $value]], ['status'=>$key]);
			}
		}
		return $list;
	}

	public function getListById($id, $lanId=1)
	{
		$list = $this->getListData(['descg_id'=>['in', $id]], 'descg_id,name');
		$lanArr = make('app/service/desc/GroupLanguage')->getListData(['descg_id'=>['in', $id], 'lan_id'=>$lanId], 'descg_id,name');
		$lanArr = array_column($lanArr, 'name', 'descg_id');
		foreach ($list as $key => $value) {
			if (isset($lanArr[$value['descg_id']])) {
				$list[$key]['name'] = $lanArr[$value['descg_id']];
			}
		}
		return $list;
	}
}