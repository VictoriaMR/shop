<?php 

namespace app\service\category;
use app\service\Base;

class Category extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/category/Category');
	}

	public function getInfo($cateId)
	{
		$info = $this->loadData($cateId);
		if (empty($info)) return false;
		$info['avatar_format'] = empty($info['avatar']) ? siteUrl('image/common/noimg.png') : mediaUrl($info['avatar'], 200);
		return $info;
	}

	public function getListFormat()
	{
		$list = $this->getListData();
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

	public function hasChildren($id)
	{
		return $this->getCountData(['parent_id' => $id]) > 0;
	}

	public function hasProduct($id)
	{
		return make('app/service/product/Spu')->getCountData(['cate_id'=>$id]) > 0;
	}

	public function deleteDataById($cateId)
	{
		$result = $this->deleteData($cateId);
		if ($result) {
			$result = make('app/service/category/Language')->deleteData(['cate_id'=>$cateId]);
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
					$value['url'] = router()->urlFormat($value['name'], 'c', ['id'=>$value['cate_id']]);
					$value['avatar'] = empty($value['avatar']) ? siteUrl('image/common/noimg.svg') : mediaUrl($value['avatar'], 200);
					$list[$key] = $value;
				}
			}
			redis()->set($cacheKey, $list, strtotime(date('Y-m-d', strtotime('+1 day')).' 00:00:00')-time());
		}

		return $list;
	}
}