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
		$vid = array_filter(explode(',', iget('vid', '')));
		$rid = iget('rid', 0);
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
				$spuIdArr = $attrUsed->getSpuId($vid);
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
				$list = $spu->getListData($where);
			}

			$this->assign('cateSon', $cateSon);
			$this->assign('filter', $filter);
			$this->assign('list', $list ?? []);
		}
		$param = ['cate_id' => $cateId, 'vid'=>$vid, 'rid'=>$rid];
		$this->assign('param', $param);
		$this->assign('cateInfo', $cateInfo);
		$this->assign('crumbs', $crumbs);
		$this->view(!$search);
	}
}