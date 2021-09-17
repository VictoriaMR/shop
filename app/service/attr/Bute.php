<?php 

namespace app\service\attr;
use app\service\Base;

class Bute extends Base
{
	const CACHE_KEY = 'product-attr:bute_cache:';

	protected function getModel()
	{
		return $this->baseModel = make('app/model/attr/Bute');
	}

	public function addNotExist($name)
	{
		if (empty($name)) {
			return false;
		}
		$name = trim($name);
		$info = $this->loadData(['name'=>$name], 'attr_id');
		if (!empty($info)) {
				return $info['attr_id'];
		}
		return $this->insertGetId(['name'=>$name]);
	}

	public function getList($where, $page=1, $size=20)
	{
		$list = $this->getListData($where, '*', $page, $size, ['attr_id'=>'desc']);
		if (!empty($list)) {
			//翻译字段 统计
			$tempArr = array_column($list, 'attr_id');
			$tempArr = make('app/service/attr/ButeLanguage')->where(['attr_id'=>['in', $tempArr]])->field('count(*) as count, attr_id')->groupBy('attr_id')->get();
			$tempArr = array_column($tempArr, 'count', 'attr_id');
			//需要翻译的语言列表
			$languageList = make('app/service/Language')->getTransList();
			$len = count($languageList);
			$transArr = [
				0 => [],
				1 => [],
				2 => [],
			];
			foreach ($list as $key => $value) {
				$status = empty($tempArr[$value['attr_id']]) ? 0 : ($tempArr[$value['attr_id']] < $len ? 1 : 2);

				if ($status != $value['status']) {
					$transArr[$status][] = $value['attr_id'];
				}

				$value['is_translate'] = $status;
				$list[$key] = $value;
			}
			foreach ($transArr as $key => $value) {
				if (empty($value)) continue;
				$this->updateData(['attr_id'=>['in', $value]], ['status'=>$key]);
			}
		}
		return $list;
	}

	public function getListById($attrId, $lanId=1)
	{
		$list = $this->getListData(['attr_id'=>['in', $attrId]], 'attr_id,name');
		$lanArr = make('app/service/attr/ButeLanguage')->getListData(['attr_id'=>['in', $attrId], 'lan_id'=>$lanId], 'attr_id,name');
		$lanArr = array_column($lanArr, 'name', 'attr_id');
		foreach ($list as $key => $value) {
			if (!empty($lanArr[$value['attr_id']])) {
				$list[$key]['name'] = $lanArr[$value['attr_id']];
			}
		}
		return $list;
	}
}