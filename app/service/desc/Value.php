<?php 

namespace app\service\desc;
use app\service\Base;

class Value extends Base
{
	protected $_model = 'app/model/desc/Value';

	public function addNotExist($nameArr)
	{
		if (empty($nameArr)) return false;
		if (!is_array($nameArr)) $nameArr = [$nameArr];
		$nameArr = array_unique($nameArr);
		//获取已存在属性
		$list = $this->getListData(['name'=>['in', $nameArr]], 'descv_id,name');
		$list = array_column($list, 'descv_id', 'name');
		$diffArr = array_diff($nameArr, array_keys($list));
		if (empty($diffArr)) {
			return $list;
		}
		$tempArr = [];
		foreach ($diffArr as $value) {
			$tempArr[] = ['name' => $value];
		}
		$this->insert($tempArr);
		$tempArr = $this->getListData(['name'=>['in', $diffArr]], 'descv_id,name');
		return $list + array_column($tempArr, 'descv_id', 'name');
	}

	public function getList($where, $page=1, $size=20)
	{
		$list = $this->getListData($where, '*', $page, $size, ['descv_id'=>'desc']);
		if (!empty($list)) {
			//翻译字段 统计
			$tempArr = array_column($list, 'descv_id');
			$tempArr = make('app/service/desc/ValueLanguage')->where(['descv_id'=>['in', $tempArr]])->field('count(*) as count, descv_id')->groupBy('descv_id')->get();
			$tempArr = array_column($tempArr, 'count', 'descv_id');
			//需要翻译的语言列表
			$languageList = make('app/service/Language')->getTransList();
			$len = count($languageList);
			$transArr = [
				0 => [],
				1 => [],
				2 => [],
			];
			foreach ($list as $key => $value) {
				$status = empty($tempArr[$value['descv_id']]) ? 0 : ($tempArr[$value['descv_id']] < $len ? 1 : 2);

				if ($status != $value['status']) {
					$transArr[$status][] = $value['descv_id'];
				}

				$value['is_translate'] = $status;
				$list[$key] = $value;
			}
			foreach ($transArr as $key => $value) {
				if (empty($value)) continue;
				$this->updateData(['descv_id'=>['in', $value]], ['status'=>$key]);
			}
		}
		return $list;
	}

	public function getListById($id, $lanId=1)
	{
		$list = $this->getListData(['descv_id'=>['in', $id]], 'descv_id,name');
		$lanArr = make('app/service/desc/ValueLanguage')->getListData(['descv_id'=>['in', $id], 'lan_id'=>$lanId], 'descv_id,name');
		$lanArr = array_column($lanArr, 'name', 'descv_id');
		foreach ($list as $key => $value) {
			if (isset($lanArr[$value['descv_id']])) {
				$list[$key]['name'] = $lanArr[$value['descv_id']];
			}
		}
		return $list;
	}
}