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

	/**
	 * 获取指定站点关联的所有分类ID列表（结合站点根品类 site.cate_id 与 site_category_used）
	 */
	public function getSiteCateIds($siteId = null)
	{
		if (is_null($siteId)) {
			$siteId = siteId();
		}
		$siteId = (int)$siteId;
		if ($siteId <= 0) {
			return [];
		}
		$allList = array_column($this->getList(), null, 'cate_id');
		$cateIds = [];
		$siteInfo = service('site/Site')->loadData($siteId, 'site_id,cate_id');
		if (!empty($siteInfo['cate_id'])) {
			$siteCates = $this->getSiteCateList($siteInfo['cate_id']);
			if (!empty($siteCates)) {
				$cateIds = array_column($siteCates, 'cate_id');
			}
		}
		$usedRows = service('site/CategoryUsed')->getListData(['site_id' => $siteId], 'cate_id');
		if (!empty($usedRows)) {
			foreach ($usedRows as $row) {
				$cid = (int)$row['cate_id'];
				if (isset($allList[$cid])) {
					$cateIds[] = $cid;
					$subCates = $this->sCate($cid);
					if (!empty($subCates)) {
						$cateIds = array_merge($cateIds, array_column($subCates, 'cate_id'));
					}
				}
			}
		}
		return array_values(array_unique(array_map('intval', $cateIds)));
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
		$lev = -1;
		if ($simple) {
			foreach ($list as $value) {
				if ($value['parent_id'] == $pid) {
					if ($value['status']) $returnData[] = $value;
				}
			}
		} else {
			foreach ($list as $value) {
				if ($lev >= 0) {
					if ($lev < $value['level']) {
						if ($value['status']) $returnData[] = $value;
					} else {
						break;
					}
				}
				if ($value['cate_id'] == $pid) {
					$lev = (int)$value['level'];
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

	public function getEnName(int $cateId, string $fallback = ''): string
	{
		$map = $this->getEnMap();
		if (isset($map[$cateId])) {
			return $map[$cateId];
		}
		return $fallback !== '' ? $fallback : 'Category';
	}

	public function getEnMap(): array
	{
		return [
			101 => 'Furniture',
			102 => 'Lighting',
			103 => 'Rugs & Mats',
			104 => 'Home Decor',
			105 => 'Window & Textiles',
			106 => 'Wall Art',
			107 => 'Baby & Kids',
			108 => 'Home Improvement',
			109 => 'Outdoor & Garden',
			110 => "Men's Clothing",
			111 => "Women's Clothing",
			112 => 'Trending Picks',
			113 => 'Personal Care',
			114 => 'Kitchen & Dining',
			115 => 'Bedding & Bath',
			116 => 'Pet Supplies',
			117 => 'Sports & Fitness',
			118 => 'Beauty & Salon',
			119 => 'Kitchen & Dining Furniture',
			120 => 'Dining Tables & Seating',
			122 => 'Dining Tables',
			123 => 'Dining Table Sets',
			124 => 'Bar Furniture',
			125 => 'Bar Stools & Counter Stools',
			126 => 'Home Bars & Bar Sets',
			128 => 'Pub & Bistro Tables',
			133 => 'Wine Racks & Cabinets',
			134 => 'Living Room Furniture',
			135 => 'Coffee & Accent Tables',
			137 => 'End & Side Tables',
			139 => 'Sofas & Couches',
			141 => 'Living Room Chairs',
			143 => 'Accent Benches',
			144 => 'Rocking Chairs',
			146 => 'Chaise Lounges',
			147 => 'Massage Chairs',
			149 => 'TV Stands & Media Consoles',
			161 => 'Home Office Furniture',
			166 => 'Office Storage Cabinets',
			168 => 'Bookcases & Bookshelves',
			170 => 'Office & Reception Sofas',
			172 => 'Bedroom Furniture',
			175 => 'Chests & Dressers',
			177 => 'Makeup Vanities',
			178 => 'Jewelry Armoires',
			179 => 'Daybeds',
			184 => 'Entryway Furniture',
			198 => 'Wall Lights & Sconces',
			206 => 'Ceiling Lights',
			216 => 'Wall Lamps',
			222 => 'Table & Floor Lamps',
			223 => 'Table & Floor Lamps',
			229 => 'Ceiling Fans & Lights',
			230 => 'Chandeliers',
			231 => 'Solar & Outdoor Lights',
			235 => 'Solar Garden Lights',
			238 => 'Outdoor Wall Lights',
			244 => 'Novelty & Accent Lights',
			249 => 'Light Bulbs',
			255 => 'Wallpapers',
			256 => 'Wall Art & Prints',
			257 => 'Wall Decals',
			258 => 'Decorative Accents',
			259 => 'Wall Hangings',
			260 => 'Curtains & Drapes',
			261 => 'Blinds & Shades',
			262 => 'Valances & Tiers',
			463 => 'Patio Furniture',
			478 => 'Outdoor Recreation',
			485 => 'Decking & Fencing',
			487 => 'Camping & Hiking',
			490 => 'Shades & Pergolas',
			494 => 'Pools, Saunas & Spas',
			497 => 'Outdoor Storage',
			502 => 'Jackets & Coats',
			514 => 'Tops & Tees',
			520 => 'Hoodies & Sweatshirts',
			521 => 'Sweaters & Knitwear',
			525 => 'Jeans & Denim',
			526 => 'Active & Swimwear',
			531 => 'Suits & Blazers',
			536 => 'Casual Pants',
			540 => 'Dresses',
			541 => 'Tops & Blouses',
			547 => 'Sweaters & Cardigans',
			552 => 'Hoodies & Sweatshirts',
			553 => 'Jeans & Denim',
			554 => 'Leggings & Tights',
			555 => 'Swimwear & Cover-Ups',
			565 => 'Coats & Jackets',
			629 => "Men's Trending",
			635 => "Women's Trending",
			643 => 'Viral Favorites',
			646 => 'Trending Fashion',
			651 => 'Creative Gadgets',
			658 => 'Gaming & Lifestyle',
			662 => 'Protective Masks',
			663 => 'Safety Goggles',
			664 => 'Protective Apparel',
			665 => 'Disposable Care',
			666 => 'Health Monitors',
			667 => 'Hand Sanitizers',
			816 => 'Salon Chairs',
			822 => 'Styling Stations',
			826 => 'Shampoo Units',
			828 => 'Manicure & Pedicure',
			830 => 'Facial & Spa Equipment',
		];
	}
}