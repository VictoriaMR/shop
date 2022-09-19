<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Category extends HomeBase
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();
		html()->addCss('clothes-icon');
		html()->addCss('product/list');
		html()->addJs('product/list');

		$cateId = (int)iget('cid', 0);
		$rid = (int)iget('rid', 0);
		$vid = (int)iget('vid', '');
		$sort = (int)iget('sort', 0);
		$keyword = trim(iget('keyword', ''));
		$page = (int)iget('page', 1);
		$size = (int)iget('size', 36);
		
		$category = make('app/service/category/Category');
		$cateInfo = $category->getInfo($cateId);
		$crumbs = [];
		if ($cateInfo) {
			$lanId = lanId();
			//desc, keyword
			$cateLanguage = make('app/service/category/Language');
			$lanArr = array_unique([1, $lanId]);
			$lanArr = $cateLanguage->getListData(['cate_id'=>$cateId, 'lan_id'=>['in', $lanArr]], 'type,name', 0, 0, ['lan_id'=>'asc']);
			$lanArr = array_column($lanArr, 'name', 'type');

			$cateInfo['name'] = $lanArr[0] ?? $cateInfo['name_en'];
			$this->assign('_title', $cateInfo['name']);
			$this->assign('_keyword', $lanArr[1]??distT('_keyword', ['name'=>$cateInfo['name']]));
			$this->assign('_desc', $lanArr[2]??distT('_desc', ['name'=>$cateInfo['name']]));
			//父级面包屑导航
			$cateParent = $category->getParentCategoryById($cateId);
			$cateParent = array_reverse($cateParent);
			if ($lanId == 1) {
				$lanArr = array_column($cateParent, 'name_en', 'cate_id');
			} else {
				//分类语言
				$where = ['cate_id'=>['in', array_column($cateParent, 'cate_id')]];
				$where['lan_id'] = $lanId;
				$lanArr = $cateLanguage->getListData($where, 'cate_id,name');
				$lanArr = array_column($lanArr, 'name', 'cate_id');
			}
			foreach ($cateParent as $value) {
				if ($value['is_show']) {
					$crumbs[] = [
						'name' => $lanArr[$value['cate_id']] ?? $value['name_en'],
						'url' => url($value['name_en'], ['c'=>$value['cate_id']]),
					];
				}
			}

			//子集
			$cateSon = $category->getSubCategoryById($cateId);
			foreach ($cateSon as $key=>$value) {
				if (!$value['is_show']) {
					unset($cateSon[$key]);
				}
			}
			if (empty($cateSon)) {
				$where['cate_id'] = $cateId;
			} else {
				$cateSonIdArr = array_column($cateSon, 'cate_id');
				if ($lanId == 1) {
					$lanArr = array_column($cateSon, 'name_en', 'cate_id');
				} else {
					$lanArr = $cateLanguage->getListData(['cate_id'=>['in', $cateSonIdArr], 'lan_id'=>$lanId, 'type'=>0], 'cate_id,name');
					$lanArr = array_column($lanArr, 'name', 'cate_id');
				}
				foreach ($cateSon as $key=>$value) {
					$cateSon[$key]['name'] = $lanArr[$value['cate_id']] ?? $value['name_en'];
				}
				$cateSonIdArr[] = $cateId;
				$where['cate_id'] = ['in', $cateSonIdArr];
			}
			//获取左侧过滤列表
			$attrUsed = make('app/service/product/AttrUsed');
			$filter = $attrUsed->getSiteAttr();
			$spuIdArr = [];
			if ($vid) {
				$spuIdArr = $attrUsed->getSpuId([$vid]);
			}
			$spu = make('app/service/product/Spu');
			if ($keyword) {
				$tempArr = $spu->getSpuIdByKeyword($keyword);
				if ($tempArr !== false) {
					if (empty($spuIdArr)) {
						$spuIdArr = $tempArr;
					} else {
						$spuIdArr = array_intersect($spuIdArr, $tempArr);
					}
					if (empty($spuIdArr)) {
						$spuIdArr = [0];
					}
				}
			}
			if (!empty($spuIdArr)) {
				$where['spu_id'] = ['in', $spuIdArr];
			}
			$where['status'] = $spu->getConst('STATUS_OPEN');
			$total = $spu->getCountData($where);
			if ($total >0) {
				//排序
				switch ($sort){
					case '2':
						$orderBy = ['visit_total'=>'desc'];
						break;
					case '3':
						$orderBy = ['min_price'=>'asc'];
						break;
					case '4':
						$orderBy = ['min_price'=>'desc'];
						break;
					default:
						$orderBy = ['grade'=>'desc'];
						break;
				}
				$where['site_id'] = siteId();
				$list = $spu->getList($where, 'spu_id,gender,attach_id,min_price,max_price,free_ship,is_hot', $page, $size, $orderBy, lanId(), true, true);
				foreach ($list as $key=>$value) {
					$list[$key]['url'] = url($value['name'], ['p'=>$value['spu_id']]);
				}
			}

			$this->assign('cateSon', $cateSon);
			$this->assign('filter', $filter);
			$this->assign('list', $list ?? []);
			$this->assign('total', $total);
			$this->assign('size', $size);
		}
		$param = ['vid'=>$vid, 'rid'=>$rid, 'sort'=>$sort];
		$this->assign('cate_id', $cateId);
		$this->assign('param', $param);
		$this->assign('cateInfo', $cateInfo);
		$this->assign('crumbs', $crumbs);
		$this->view(!$keyword);
	}
}