<?php

namespace app\controller\admin;
use app\controller\Controller;

class ProductController extends Controller
{
	public function __construct()
	{
        $this->_arr = [
            'index' => 'SPU列表',
        ];
		$this->_default = '产品管理';
		$this->_init();
	}

	public function index()
	{	
		html()->addCss();
		
		$status = (int)iget('status', -1);
		$site = (int)iget('site', -1);
		$cate = (int)iget('cate', 0);
		$stime = trim(iget('stime'));
		$etime = trim(iget('etime'));
		$spuId = (int)iget('spu_id');
		$page = (int)iget('page', 1);
		$size = (int)iget('size', 20);
		//spu状态
		$spuService = make('app/service/ProductSpuService');
		$statusList = $spuService->getStatusList();
		//站点
		$siteList = make('app/service/SiteService')->getList();
		$siteList = array_column($siteList, 'name', 'site_id');
		//分类
		$cateList = make('app/service/CategoryService')->getListFormat();

		$where = [];
		if (in_array($status, array_keys($statusList), true)) {
			$where['status'] = $status;
		}
		if (in_array($site, array_keys($siteList), true)) {
			$where['site_id'] = $site;
		}
		if ($cate > 0) {
			$spuIdArr = make('app/service/CategoryService')->getSpuIdByCateId($cate);
			if (empty($spuIdArr)) {
				$where = ['spu_id' => 0];
			} else {
				$where['spu_id'] = ['in', $spuIdArr];
			}
		}

		$total = $spuService->getCountData($where);
		if ($total > 0) {
			$list = $spuService->getAdminList($where, $page, $size);
		}

		$this->assign('spuId', $spuId);
		$this->assign('status', $status);
		$this->assign('statusList', $statusList);
		$this->assign('site', $site);
		$this->assign('siteList', $siteList);
		$this->assign('cate', $cate);
		$this->assign('cateList', $cateList);
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);
		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);

		return view();
	}

	public function view()
	{
		service();
		Html::addCss();
		$id = (int)iget('id');

		$spuService = make('app/service/ProductSpuService');
		$info = $spuService->getInfo($id);
		dd($info);
		$this->_arr['view'] = 'SPU详情';
		$this->_init();
		return view();
	}
}