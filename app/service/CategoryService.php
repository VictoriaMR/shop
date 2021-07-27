<?php 

namespace app\service;
use app\service\Base;

class CategoryService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/Category');
	}

	public function getInfo($cateId)
	{
		$info = $this->loadData($cateId);
		if (empty($info)) return false;
		$info['avatar_format'] = empty($info['avatar']) ? siteUrl('image/common/noimg.png') : mediaUrl($info['avatar'], 200);
		return $info;
	}

	public function getLanguage($cateId)
	{
		return make('app/model/CategoryLanguage')->getListData(['cate_id' => $cateId]);
	}

	public function getList(array $where=[])
	{
		$list = $this->getListData($where, '*', 0, 0, ['sort'=>'asc']);
		if (empty($list)) return false;
		$cateIdArr = array_column($list, 'cate_id');
		$lanArr = make('app/model/CategoryLanguage')->where(['cate_id'=>['in', $cateIdArr], 'lan_id'=>1])->field('cate_id,name')->get();
		$lanArr = array_column($lanArr, 'name', 'cate_id');
		foreach ($list as $key => $value) {
			$value['name'] = $lanArr[$value['cate_id']] ?? '';
			$list[$key] = $value;
		}
		foreach ($list as $key => $value) {
			if (!empty($value['avatar'])) {
				$value['avatar'] = mediaUrl($value['avatar'], 200);
			}
			$list[$key] = $value;
		}
		return $list;
	}

	public function getListFormat()
	{
		$list = $this->getList();
		if (!$list) return false;
		$list = $this->listFormat($list, 0, 0);
		$returnData = [];
		$this->arrayFormat($list, $returnData);
		return $returnData;
	}

	protected function listFormat($list, $parentId=0, $lev=0) 
	{
		$returnData = [];
		foreach ($list as $value) {
			$value['level'] = $lev;
			if ($value['parent_id'] == $parentId) {
				$temp = $this->listFormat($list, $value['cate_id'], $value['level'] + 1);
				if (!empty($temp)) {
					$value['son'] = $temp;
				}
				$returnData[] = $value;
			}
		}
		return $returnData;
	}

	protected function arrayFormat($list, &$returnData)
	{
		foreach ($list as $value) {
			$temp = $value;
			unset($temp['son']);
			$returnData[] = $temp;
			if (!empty($value['son'])) {
				$this->arrayFormat($value['son'], $returnData);
			}
		}
		return true;
	}

	public function setNxLanguage($cateId, $lanId, $name)
	{
		if (empty($cateId) || empty($lanId) || empty($name)) {
			return false;
		}
		$model = make('app/model/CategoryLanguage');
		$where = ['cate_id'=>$cateId, 'lan_id'=>$lanId];
		if ($model->getCount($where)) {
			return $model->where($where)->update(['name' => $name]);
		} else {
			$where['name'] = $name;
			return $model->insert($where);
		}
	}

	public function hasChildren($id)
	{
		return $this->where('parent_id', $id)->count() > 0;
	}

	public function hasProduct($id)
	{
		return make('app/model/product/Spu')->where('cate_id', $id)->count() > 0;
	}

	protected function deleteDataById($cateId)
	{
		$result = $this->deleteData($cateId);
		if ($result) {
			$result = make('app/model/CategoryLanguage')->where('cate_id', $cateId)->delete();
		}
		return $result;
	}

    public function updateStat()
    {
		$result = make('app/model/product/Spu')->field('cate_id, SUM(sale_total) AS sale_total, SUM(visit_total) AS visit_total')->where('status', 1)->groupBy('cate_id')->get();
		if (!empty($result)) {
			$result = array_column($result, null, 'cate_id');
			foreach ($result as $key => $value) {
				$data = [
					'sale_total' => $value['sale_total'],
					'visit_total' => $value['visit_total'],
				];
				$this->updateData($key, $data);
			}
		}
		return true;
    }

    protected function getCacheKey($suffix='')
    {
    	return 'category:list-cache'.$suffix;
    }

	public function getHotCategory($size=8)
	{
		$lanId = lanId();
		$cacheKey = $this->getCacheKey('-hot-'.$lanId);
		$list = redis()->get($cacheKey);
		if (empty($list)) {
			$list = $this->field('cate_id, avatar')->where('parent_id', '>', 0)->orderBy(['(sale_total+visit_total)'=>'desc', 'sort'=>'asc'])->page(1, $size)->get();
			if (!empty($list)) {
				$cateIdArr = array_column($list, 'cate_id');
				$lanArr = make('app/model/CategoryLanguage')->where(['cate_id'=>['in', $cateIdArr], 'lan_id'=>$lanId])->field('cate_id,name')->get();
				$lanArr = array_column($lanArr, 'name', 'cate_id');
				foreach ($list as $key => $value) {
					$value['name'] = $lanArr[$value['cate_id']] ?? '';
					$value['url'] = router()->siteUrl($value['name'], 'category-'.$value['cate_id']);
					$value['avatar'] = empty($value['avatar']) ? siteUrl('image/common/noimg.svg') : mediaUrl($value['avatar'], 200);
					$list[$key] = $value;
				}
			}
			redis()->set($cacheKey, $list, strtotime(date('Y-m-d', strtotime('+1 day')).' 00:00:00')-time());
		}

		return $list;
	}
}