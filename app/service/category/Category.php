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

	public function getSiteInfoCache($cateId)
	{
		$list = $this->getSiteList();
		$list = array_column($list, null, 'cate_id');
		$info = $list[$cateId] ?? [];
		if (empty($info)) return false;
		$info['image'] = empty($info['avatar']) ? '' : mediaUrl($info['avatar'], 200);
		return $info;
	}

	public function getSiteList()
	{
		if (empty($this->_list)) {
			$this->_list = $this->getSiteListCache();
		}
		return $this->_list;
	}

	public function getSiteListCache()
	{
		$cacheKey = $this->getCacheKey(siteId());
		$list = redis()->get($cacheKey);
		if (!$list) {
			$tempArr = $this->getListData();
			$tempArr = $this->listFormat($tempArr);
			$list = [];
			$this->arrayFormat($tempArr, $list);
			$tempArr = [];
			$cateId = 0;
			foreach ($list as $value) {
				if ($value['parent_id'] == 0) {
					$cateId = $value['cate_id'];
					$tempArr[$cateId] = [];
				}
				$tempArr[$cateId][] = $value;
			}
			$list = $tempArr[cateId()] ?? [];
			redis()->set($cacheKey, $list);
		}
		return $list;
	}

	public function getListFormat()
	{
		$list = $this->getListData();
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

	public function getSiteSubCategoryById($id)
	{
		$list = $this->getSiteList();
		$list = $this->listFormat($list, $id, 0);
		if (empty($list)) {
			return [];
		}
		return $this->getSubCategory($list);
	}

	public function getSubCategoryById($id)
	{
		$list = $this->getListData();
		$list = $this->listFormat($list, $id, 0);
		if (empty($list)) {
			return [];
		}
		return $this->getSubCategory($list);
	}

	public function getParentCategoryById($id, $self=true)
	{
		$returnData = [];
		$list = $this->getSiteList();
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

	protected function getSubCategory($list) 
	{
		$returnData = [];
		foreach ($list as $value) {
			if (empty($value['son'])) {
				$returnData[] = $value['cate_id'];
			} else {
				$returnData = array_merge($returnData, $this->getSubCategory($value['son']));
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

	protected function getCacheKey($suffix='')
	{
		return 'category:list-cache:'.$suffix;
	}

	public function getInCategory(array $cateIdArr=[])
	{
		if (empty($cateIdArr)) {
			return $this->listFormat($cateList);
		} else {
			$returnData = [];
			foreach ($cateIdArr as $value) {
				$data = $this->getParentCategoryById($value);
				$data = array_reverse($data);
				foreach ($data as $sKey=>$sValue) {
					$data[$sKey]['level'] = $sKey;
				}
				$returnData = array_merge($returnData, $data);
			}
			return array_values(array_column($returnData, null, 'cate_id'));
		}
	}
}