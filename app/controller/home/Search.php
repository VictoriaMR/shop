<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Search extends HomeBase
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();
		html()->addCss('clothes-icon');
		html()->addCss('product/list');
		html()->addJs('product/list');

		$sort = (int)iget('sort', 0);
		$keyword = trim(iget('keyword', ''));
		$page = (int)iget('page', 1);
		$size = (int)iget('size', 40);

		$crumbs[] = [
			'name'=>$keyword?:'Search',
			'url' => url('search', ['keyword'=>$keyword]),
		];
		
		$spuIdArr = [];
		$where = [];
		$spu = service('product/Spu');
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

		$this->assign('list', $list ?? []);
		$this->assign('total', $total);
		$this->assign('size', $size);

		$param = ['keyword'=>$keyword, 'sort'=>$sort];
		$this->assign('param', $param);
		$this->assign('crumbs', $crumbs);
		$this->assign('_title', distT('search', ['keyword'=>$keyword]));
		$this->view();
	}
}