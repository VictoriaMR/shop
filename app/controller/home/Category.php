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

		$cateId = iget('id', 0);
		$vid = iget('vid', '');
		$rid = iget('rid', 0);
		$sort = iget('sort', 0);
		$page = iget('page', 1);
		$size = iget('size', 36);
		$search = false;
		
		$category = make('app/service/category/Category');
		$cateInfo = $category->getSiteInfoCache($cateId);
		$crumbs = [];
		if ($cateInfo) {
			$crumbs[] = [
				'name' => $cateInfo['name_en'],
				'url' => url($cateInfo['name_en'].'-c', ['id'=>$cateInfo['cate_id']]),
			];
			$cateSon = $category->getSubCategoryById($cateId);
			foreach ($cateSon as $key=>$value) {
				$info = $category->getSiteInfoCache($value);
				if ($info) {
					$cateSon[$key] = $info;
				} else {
					unset($cateSon[$key]);
				}
			}
			//获取左侧过滤列表
			$attrUsed = make('app/service/product/AttrUsed');
			$filter = $attrUsed->getSiteAttr();
			if ($vid) {
				$search = true;
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
					$list[$key]['url'] = url($value['name'].'-p', ['id'=>$value['spu_id']]);
				}
			}

			$this->assign('cateSon', $cateSon);
			$this->assign('filter', $filter);
			$this->assign('list', $list ?? []);
			$this->assign('total', $total);
		}
		$param = ['vid'=>$vid, 'rid'=>$rid, 'sort'=>$sort];
		$this->assign('cate_id', $cateId);
		$this->assign('param', $param);
		$this->assign('cateInfo', $cateInfo);
		$this->assign('crumbs', $crumbs);
		$this->view(!$search);
	}
}