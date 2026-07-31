<?php 

namespace app\service\category;
use app\service\Base;

class Category extends Base
{
	protected $_list = [];

	public function getInfo($cateId)
	{
		$list = $this->getList();
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

	protected function getList($cache=true)
	{
		if (empty($this->_list)) {
			$cacheKey = $this->getCacheKey();
			if ($cache) {
				$this->_list = redis()->get($cacheKey);
				if (!empty($this->_list)) {
					return $this->_list;
				}
			}
			$tempArr = $this->getListData([], '*', 0, 0, ['sort' => 'asc', 'cate_id' => 'asc']);
			$tempArr = $this->listFormat($tempArr);
			$this->arrayFormat($tempArr, $this->_list);
			if ($cache) {
				redis()->set($cacheKey, $this->_list);
			}
		}
		return $this->_list;
	}

	public function getListFormat($cache=true)
	{
		$list = $this->getList($cache);
		$list = $this->listFormat($list, 0, 0);
		$returnData = [];
		$this->arrayFormat($list, $returnData);
		return $returnData;
	}

	/**
	 * 递归格式化分类树，并保证同级子类目严格按 sort ASC, cate_id ASC 排序
	 */
	protected function listFormat($list, $parentId=0, $lev=0) 
	{
		$returnData = [];
		foreach ($list as $value) {
			if ($value['parent_id'] == $parentId) {
				$value['level'] = $lev;
				$temp = $this->listFormat($list, $value['cate_id'], $lev + 1);
				if (!empty($temp)) {
					$value['son'] = $temp;
				}
				$returnData[] = $value;
			}
		}
		if (!empty($returnData)) {
			usort($returnData, function($a, $b) {
				if ($a['sort'] == $b['sort']) {
					return $a['cate_id'] <=> $b['cate_id'];
				}
				return $a['sort'] <=> $b['sort'];
			});
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

	/**
	 * 获取指定父分类下的直属子分类列表，按 sort ASC, cate_id ASC 排序
	 *
	 * @param int $parentId 父分类ID
	 * @param bool $onlyStatus 是否仅获取开启状态(status=1)的分类
	 * @return array
	 */
	public function getChildren($parentId, $onlyStatus = false)
	{
		$list = $this->getList();
		$children = [];
		foreach ($list as $value) {
			if ($value['parent_id'] == $parentId) {
				if ($onlyStatus && empty($value['status'])) {
					continue;
				}
				$children[] = $value;
			}
		}
		usort($children, function($a, $b) {
			if ($a['sort'] == $b['sort']) {
				return $a['cate_id'] <=> $b['cate_id'];
			}
			return $a['sort'] <=> $b['sort'];
		});
		return $children;
	}

	public function sCate($id, $simple=false)
	{
		return $this->getSubCategory($this->getList(), $id, $simple);
	}

	public function pCate($id, $self=true, $reverse=false)
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
			$returnData = array_merge($returnData, $this->pCate($id));
		}
		return $reverse ? array_reverse($returnData) : $returnData;
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
		} else {
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
		}
		usort($returnData, function($a, $b) {
			if ($a['sort'] == $b['sort']) {
				return $a['cate_id'] <=> $b['cate_id'];
			}
			return $a['sort'] <=> $b['sort'];
		});
		return $returnData;
	}

	public function hasChildren($id)
	{
		return $this->getCountData(['parent_id' => $id]) > 0;
	}

	public function hasProduct($id)
	{
		return service('product/Spu')->getCountData(['cate_id'=>$id]) > 0;
	}

	public function deleteDataById($cateId)
	{
		$result = $this->deleteData($cateId);
		if ($result) {
			$result = service('category/Language')->deleteData(['cate_id'=>$cateId]);
		}
		return $result;
	}

	protected function getCacheKey()
	{
		return 'category:list-cache';
	}
}