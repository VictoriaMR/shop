<?php

namespace app\controller\admin;
use app\controller\Base;

class Product extends Base
{
	public function __construct()
	{
		$this->_arr = [
			'index' => 'SPU列表',
		];
		$this->_default = '产品管理';
	}

	public function index()
	{	
		html()->addCss();
		
		$status = iget('status', -1);
		$site = iget('site', -1);
		$cate = iget('cate', 0);
		$stime = iget('stime');
		$etime = iget('etime');
		$spuId = iget('spu_id');
		$page = iget('page', 1);
		$size = iget('size', 30);
		//spu状态
		$spu = make('app/service/product/Spu');
		$statusList = $spu->getStatusList();
		//站点
		$siteList = make('app/service/site/Site')->getListData([], 'site_id,name');
		$siteList = array_column($siteList, 'name', 'site_id');
		//分类
		$cateList = make('app/service/category/Category')->getListFormat();
		$cateList = array_column($cateList, null, 'cate_id');
		$where = [];
		if (in_array($status, array_keys($statusList), true)) {
			$where['status'] = $status;
		}
		if ($site >= 0) {
			$where['site_id'] = $site;
		}
		if ($cate > 0) {
			$spuIdArr = make('app/service/category/Category')->getSpuIdByCateId($cate);
			if (empty($spuIdArr)) {
				$where = ['spu_id' => 0];
			} else {
				$where['spu_id'] = ['in', $spuIdArr];
			}
		}

		$total = $spu->getCountData($where);
		if ($total > 0) {
			$list = $spu->getAdminList($where, $page, $size);
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
		$this->_init();
		
		$this->view();
	}

	public function detail()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['editInfo', 'getSpuNameLanguage', 'transfer', 'editSpuLanguage', 'modifySpuImage', 'addSpuImage', 'deleteSpuImage', 'editSkuInfo'])) {
				$this->$opn();
			}
			$this->error('未知请求');
		}

		html()->addCss();
		html()->addJs();
		$spuService = make('app/service/product/Spu');
		$info = $spuService->getAdminInfo(iget('id'));
		if (!empty($info)) {
			//spu状态
			$statusList = $spuService->getStatusList();
			//站点分类
			$category = make('app/service/category/Category')->getList();
			$category = array_column($category, 'name', 'cate_id');
			$siteCate = make('app/service/site/CategoryUsed')->getListData(['site_id'=>$info['site_id']], 'site_id,cate_id', 0, 0, ['sort'=>'asc']);
			foreach ($siteCate as $key => $value) {
				$siteCate[$key]['name'] = $category[$value['cate_id']];
			}

			$this->assign('info', $info);
			$this->assign('statusList', $statusList);
			$this->assign('siteCate', $siteCate);
		}
		$this->_arr['detail'] = 'SPU详情';
		$this->_init();
		$this->view();
	}

	protected function getSpuNameLanguage()
	{
		$id = ipost('id');
		$info = make('app/service/product/Language')->getListData(['spu_id'=>$id]);
		$info = array_column($info, null, 'lan_id');
		$languageList = make('app/service/Language')->getListCache();
		foreach ($languageList as $key => $value) {
			$info[$value['code']] = [
				'lan_id' => $value['code'],
				'tr_code' => $value['tr_code'],
				'name' => empty($info[$value['code']]) ? '' : $info[$value['code']]['name'],
				'language_name' => $value['name2'],
			];
		}
		$this->success($info, '');
	}

	protected function editSpuLanguage()
	{
		$spuId = ipost('spu_id');
		if (empty($spuId)) {
			$this->error('ID值不正确');
		}
		$language = ipost('language');
		if (!empty($language)) {
			$services = make('app/service/product/Language');
			foreach ($language as $key => $value) {
				$services->setNxLanguage($spuId, $key, strTrim($value));
			}
		}
		$this->addLog('修改产品语言-'.$spuId);
		$this->success('操作成功');
	}

	protected function modifySpuImage()
	{
		$spuId = ipost('spu_id');
		$attachId = ipost('attach_id');
		if (empty($spuId) || empty($attachId)) {
			$this->error('参数不正确');
		}
		$sort = ipost('sort');
		$rst = make('app/service/product/SpuImage')->updateData(['spu_id'=>$spuId, 'attach_id'=>$attachId], ['sort'=>$sort]);
		if ($rst) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function deleteSpuImage()
	{
		$spuId = ipost('spu_id');
		$attachId = ipost('attach_id');
		if (empty($spuId) || empty($attachId)) {
			$this->error('参数不正确');
		}
		$rst = make('app/service/product/SpuImage')->deleteData(['spu_id'=>$spuId, 'attach_id'=>$attachId]);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function addSpuImage()
	{
		$spuId = ipost('spu_id');
		$attachId = ipost('attach_id');
		if (empty($spuId) || empty($attachId)) {
			$this->error('参数不正确');
		}
		$imageService = make('app/service/product/SpuImage');
		$data = ['spu_id'=>$spuId, 'attach_id'=>$attachId];
		if ($imageService->getCountData($data)) {
			$this->success('上传成功');
		}
		$data['sort'] = $imageService->getCountData(['spu_id'=>$spuId])+1;
		$rst = $imageService->insert($data);
		if ($rst) {
			$this->success('上传成功');
		}
		$this->error('上传失败');
	}

	protected function editInfo()
	{
		$data = [];
		$param = ipost();
		$id = $param['spu_id'];
		if ($id <= 0) {
			$this->error('ID不能为空');
		}
		if (isset($param['status'])) {
			$data['status'] = $param['status'];
		}
		if (isset($param['free_ship'])) {
			$data['free_ship'] = $param['free_ship'];
		}
		if (isset($param['cate_id'])) {
			$data['cate_id'] = $param['cate_id'];
		}
		if (isset($param['attach_id'])) {
			$data['attach_id'] = $param['attach_id'];
		}
		if (empty($data)) {
			$this->error('参数不正确');
		}
		$rst = make('app/service/product/Spu')->updateData(['spu_id'=>$id], $data);
		if ($rst) {
			if (isset($data['attach_id'])) {
				make('app/service/product/SpuImage')->updateData(['spu_id'=>$id, 'attach_id'=>$data['attach_id']], ['sort'=>0]);
			}
			$this->success('修改成功');
		} else {
			$this->error('修改失败');
		}
	}

	protected function editSkuInfo()
	{
		$skuId = ipost('sku_id');
		$spuId = ipost('spu_id');
		if (empty($skuId) && empty($spuId)) {
			$this->error('参数不正确');
		}
		if (!empty($spuId)) {
			$skuId = make('app/service/product/Sku')->getListData(['spu_id'=>$spuId], 'sku_id');
			$skuId = array_column($skuId, 'sku_id');
		} elseif (!is_array($skuId)) {
			$skuId = [$skuId];
		}
		$param = ipost();
		$data = [];
		if (isset($param['attach_id'])) {
			$data['attach_id'] = $param['attach_id'];
		}
		if (isset($param['status'])) {
			$data['status'] = $param['status'];
		}
		if (isset($param['price'])) {
			$data['price'] = $param['price'];
		}
		if (isset($param['original_price'])) {
			$data['original_price'] = $param['original_price'];
		}
		if (isset($param['stock'])) {
			$data['stock'] = $param['stock'];
		}
		if (isset($param['cost_price'])) {
			$data['cost_price'] = $param['cost_price'];
		}
		if (empty($data)) {
			$this->error('更新的参数为空');
		}
		if (isset($data['cost_price'])) {
			$service = make('app/service/product/SkuData');
		} else {
			$service = make('app/service/product/Sku');
		}
		$rst = $service->updateData(['sku_id'=>['in', $skuId]], $data);
		if ($rst) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}
}