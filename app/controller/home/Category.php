<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Category extends HomeBase
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();
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
			//desc, keyword
			$cateLanguage = make('app/service/category/Language');
			$lanArr = array_unique([1, lanId()]);
			$lanArr = $cateLanguage->getListData(['cate_id'=>$cateId, 'lan_id'=>['in', $lanArr]], 'type,name', 0, 0, ['lan_id'=>'asc']);
			$lanArr = array_column($lanArr, 'name', 'type');

			$cateInfo['name'] = $lanArr[0] ?? $cateInfo['name_en'];
			$this->assign('_title', $cateInfo['name']);
			if (isset($lanArr[1])) {
				$this->assign('_keyword', $lanArr[1]);
			}
			if (isset($lanArr[2])) {
				$this->assign('_desc', $lanArr[2]);
			}

			$crumbs[] = [
				'name' => $cateInfo['name_en'],
				'url' => url($cateInfo['name_en'], ['c'=>$cateInfo['cate_id']]),
			];
			$cateSon = $category->getSubCategoryById($cateId);
			foreach ($cateSon as $key=>$value) {
				$info = $category->getInfo($value);
				if ($info['is_show']) {
					$cateSon[$key] = $info;
				} else {
					unset($cateSon[$key]);
				}
			}
			if (!empty($cateSon)) {
				$cateSonIdArr = array_column($cateSon, 'cate_id');
				$lanArr = $cateLanguage->getListData(['cate_id'=>['in', $cateSonIdArr], 'lan_id'=>['in', $lanArr], 'type'=>0], 'cate_id,name', 0, 0, ['lan_id'=>'asc']);
				$lanArr = array_column($lanArr, 'name', 'cate_id');
				foreach ($cateSon as $key=>$value) {
					$cateSon[$key]['name'] = $lanArr[$value['cate_id']] ?? $value['name_en'];
				}

				$cateSonIdArr[] = $cateId;
				$where['cate_id'] = ['in', array_unique($cateSonIdArr)];
			}
			//获取左侧过滤列表
			$attrUsed = make('app/service/product/AttrUsed');
			$filter = $attrUsed->getSiteAttr();
			if ($vid) {
				$spuIdArr = $attrUsed->getSpuId([$vid]);
				if ($spuIdArr) {
					$where['spu_id'] = ['in', $spuIdArr];
				} else {
					$where = ['spu_id'=>0];
				}
			}
			$spu = make('app/service/product/Spu');
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