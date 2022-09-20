<?php 

namespace app\service\category;
use app\service\Base;

class Category extends Base
{
	protected $_list = [];

	protected function getModel()
	{
		$this->baseModel = make('app/model/category/Category');
	}

	public function getInfo($cateId)
	{
		$list = $this->getListCache();
		$list = array_column($list, null, 'cate_id');
		if (!isset($list[$cateId])) {
			return false;
		}
		$info = $list[$cateId];
		$info['image'] = empty($info['avatar']) ? '' : mediaUrl($info['avatar'], 200);
		return $info;
	}

	public function getSiteCateList($siteCateId)
	{
		$list = $this->getList();
		$tempArr = [];
		$cateId = 0;
		foreach ($list as $value) {
			if ($value['parent_id'] == 0) {
				$cateId = $value['cate_id'];
				$tempArr[$cateId] = [];
			}
			$tempArr[$cateId][] = $value;
		}
		return $tempArr[$siteCateId] ?? [];
	}

	protected function getList()
	{
		if (empty($this->_list)) {
			$this->_list = $this->getListCache();
		}
		return $this->_list;
	}

	public function getListCache()
	{
		$cacheKey = $this->getCacheKey();
		$list = redis()->get($cacheKey);
		if (!$list) {
			$tempArr = $this->getListData();
			$tempArr = $this->listFormat($tempArr);
			$list = [];
			$this->arrayFormat($tempArr, $list);
			redis()->set($cacheKey, $list);
		}
		return $list;
	}

	public function getListFormat()
	{
		$list = $this->getList();
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

	public function getSubCategoryById($id, $simple=false)
	{
		return $this->getSubCategory($this->getList(), $id, $simple);
	}

	public function getParentCategoryById($id, $self=true, $siteId=0)
	{
		$returnData = [];
		$list = $this->getList();
		$list = array_column($list, null, 'cate_id');
		if (!isset($list[$id])) {
			return $returnData;
		}
		if ($self) $returnData[] = $list[$id];
		$id = $list[$id]['parent_id'];
		if (isset($list[$id])) {
			$returnData = array_merge($returnData, $this->getParentCategoryById($id));
		}
		return $returnData;
	}

	protected function getSubCategory($list, $pid, $simple=false) 
	{
		$returnData = [];
		$lev = 0;
		if ($simple) {
			foreach ($list as $value) {
				if ($value['parent_id'] == $pid && $value['level'] == 1) {
					if ($value['status']) $returnData[] = $value;
				}
			}
		}
		foreach ($list as $value) {
			if ($lev > 0) {
				if ($lev < $value['level']) {
					if ($value['status']) $returnData[] = $value;
				} else {
					break;
				}
			}
			if ($value['cate_id'] == $pid) {
				$lev = $value['level'];
			}
		}
		return $returnData;
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

	protected function getCacheKey()
	{
		return 'category:list-cache';
	}
}