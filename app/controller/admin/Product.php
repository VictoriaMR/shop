<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Product extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => 'SPU列表',
			'detail' => 'SPU详情',
		];
		$this->_ignore = ['detail'];
		$this->_default = '产品管理';
		parent::_init();
	}

	public function index()
	{	
		html()->addJs();
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
		//分类
		$cateList = make('app/service/category/Category')->getListFormat();
		$cateArr = array_column($cateList, 'name', 'cate_id');
		//站点
		$siteList = make('app/service/site/Site')->getListData(['site_id'=>['>=', 80]], 'site_id,name,cate_id');
		$siteList = array_column($siteList, null, 'site_id');
		$tempArr = [];
		$cateId = 0;
		foreach ($cateList as $value) {
			if ($value['parent_id'] == 0) {
				$cateId = $value['cate_id'];
				$tempArr[$cateId] = [];
			}
			$tempArr[$cateId][] = $value;
		}
		$cateList = [];
		foreach ($siteList as $key=>$value) {
			$cateList[$key] = $tempArr[$value['cate_id']] ?? [];
		}
		$where = [];
		if ($status > -1) {
			$where['status'] = $status;
		}
		if ($site >= 0) {
			$where['site_id'] = $site;
		}
		if ($cate > 0) {
			$where['cate_id'] = $cate;
		}
		if ($stime && $etime) {
			$where['add_time'] = ['between', [date('Y-m-d', strtotime($stime)).' 00:00:00', date('Y-m-d', strtotime($etime)).' 23:59:59']];
		} elseif ($stime) {
			$where['add_time'] = ['>=', date('Y-m-d', strtotime($stime)).' 00:00:00'];
		} elseif ($etime) {
			$where['add_time'] = ['<=', date('Y-m-d', strtotime($etime)).' 23:59:59'];
		}
		$total = $spu->getCountData($where);
		if ($total > 0) {
			$list = $spu->getList($where, '*', $page, $size);
			foreach ($list as $key=>$value) {
				$list[$key]['status_text'] = $statusList[$value['status']];
			}
		}

		$this->assign('spuId', $spuId);
		$this->assign('status', $status);
		$this->assign('statusList', $statusList);
		$this->assign('site', $site);
		$this->assign('siteList', $siteList);
		$this->assign('cate', $cate);
		$this->assign('cateList', $cateList);
		$this->assign('cateArr', $cateArr);
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);
		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->view();
	}

	public function detail()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['editInfo', 'getSpuNameLanguage', 'transfer', 'editSpuLanguage', 'modifySpuImage', 'addSpuImage', 'deleteSpuImage', 'editSkuInfo', 'modifySkuAttrImage', 'modifySpuDesc', 'deleteSpuDesc', 'getSpuDescInfo', 'modifySpuIntroduceImage', 'deleteSpuIntroduceImage', 'addSpuIntroduceImage', 'modifySpuData', 'editDescGroupInfo'])) {
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
			$category = make('app/service/category/Category');
			$cateArr = $category->getListFormat();
			$siteArr = make('app/service/site/Site')->getListData([], 'site_id,name,cate_id');
			$tempArr = [];
			$cateId = 0;
			foreach ($cateArr as $value) {
				if ($value['parent_id'] == 0) {
					$cateId = $value['cate_id'];
					$tempArr[$cateId] = [];
				}
				$tempArr[$cateId][] = $value;
			}
			$cateArr = [];
			foreach ($siteArr as $value) {
				$cateArr[$value['site_id']] = $tempArr[$value['cate_id']] ?? [];
			}
			$siteCate = $cateArr[$info['site_id']];

			$info['category'] = array_reverse($category->getParentCategoryById($info['cate_id']));

			$tempArr = [];
			foreach ($info['desc'] as $value) {
				if (!isset($tempArr[$value['descg_id']])) {
					$tempArr[$value['descg_id']] = [
						'descg_id' => $value['descg_id'],
						'group' => $value['group'],
						'list' => [],
					];
				}
				$tempArr[$value['descg_id']]['list'][] = $value;
			}
			$info['desc'] = $tempArr;

			//分组列表
			$groupList = make('app/service/desc/Group')->getListData();
			$this->assign('info', $info);
			$this->assign('statusList', $statusList);
			$this->assign('groupList', $groupList);
			$this->assign('siteCate', $siteCate);
		}
		$this->view();
	}

	protected function getSpuNameLanguage()
	{
		$id = ipost('id');
		$list = make('app/service/product/Language')->getListData(['spu_id'=>$id]);
		$list = array_column($list, null, 'lan_id');
		$list[0]['language_name'] = '名称';
		$languageList = make('app/service/Language')->getListData();
		foreach ($languageList as $key => $value) {
			$list[$value['lan_id']] = [
				'lan_id' => $value['lan_id'],
				'tr_code' => $value['tr_code'],
				'name' => empty($list[$value['lan_id']]) ? '' : $list[$value['lan_id']]['name'],
				'language_name' => $value['name2'],
			];
		}
		$this->success($list, '');
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
		$itemId = ipost('item_id');
		if (empty($spuId) || empty($itemId)) {
			$this->error('参数不正确');
		}
		$rst = make('app/service/product/SpuImage')->deleteData(['spu_id'=>$spuId, 'item_id'=>$itemId]);
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
		$data['sort'] = $imageService->loadData(['spu_id'=>$spuId], 'max(sort) as sort')['sort']+1;
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
		if (isset($param['gender'])) {
			$data['gender'] = $param['gender'];
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
		if (isset($param['volume'])) {
			$data['volume'] = $param['volume'];
		}
		if (isset($param['weight'])) {
			$data['weight'] = $param['weight'];
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

	protected function modifySkuAttrImage()
	{
		$spuId = ipost('spu_id');
		$attrId = ipost('attrn_id');
		$attvId = ipost('attrv_id');
		$attachId = ipost('attach_id');
		if (empty($spuId) || empty($attrId) || empty($attvId)) {
			$this->error('参数不正确');
		}
		$skuService = make('app/service/product/Sku');
		$skuId = $skuService->getListData(['spu_id'=>$spuId], 'sku_id');
		if (empty($skuId)) {
			$this->error('SKU ID不能为空');
		}
		$skuId = array_column($skuId, 'sku_id');
		$attrUsed = make('app/service/product/AttrUsed');
		$list = $attrUsed->getListData(['sku_id'=>['in', $skuId], 'attrn_id'=>$attrId, 'attrv_id'=>$attvId], 'item_id,sku_id');
		if (empty($list)) {
			$this->error('找不到sku属性');
		}
		$rst = $attrUsed->updateData(['item_id'=>['in', array_column($list, 'item_id')]], ['attach_id'=>$attachId]);
		$skuService->updateData(['sku_id'=>['in', array_unique(array_column($list, 'sku_id'))]], ['attach_id'=>$attachId]);
		if ($rst) {
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}

	protected function modifySpuDesc()
	{
		$id = ipost('item_id');
		$param = ipost();
		$data = [];
		if (isset($param['sort'])) {
			$data['sort'] = $param['sort'];
		}
		if (empty($data)) {
			$this->error('参数不正确');
		}
		if (!empty($param['spu_id'])) {
			if (empty($param['name'])) {
				$this->error('描述名不能为空');
			}
			if(empty($param['value'])) {
				$this->error('描述值不能为空');
			}
			$tempArr = make('app/service/desc/Name')->addNotExist($param['name']);
			$data['descn_id'] = $tempArr[$param['name']];
			$tempArr = make('app/service/desc/Value')->addNotExist($param['value']);
			$data['descv_id'] = $tempArr[$param['value']];
			$data['spu_id'] = $param['spu_id'];
		}
		if (empty($id)) {
			$rst = make('app/service/product/DescUsed')->addDescUsed($param['spu_id'], $data);
		} else {
			$rst = make('app/service/product/DescUsed')->updateData($id, $data);
		}
		if ($rst) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function deleteSpuDesc()
	{
		$id = ipost('item_id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$rst = make('app/service/product/DescUsed')->deleteData($id);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function getSpuDescInfo()
	{
		$id = ipost('item_id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = make('app/service/product/DescUsed')->loadData($id);
		if ($info) {
			$tempArr = make('app/service/desc/Name')->loadData($info['descn_id'], 'name');
			$info['name'] = $tempArr['name'];
			$tempArr = make('app/service/desc/Value')->loadData($info['descv_id'], 'name');
			$info['value'] = $tempArr['name'];
			$this->success($info, '获取成功');
		}
		$this->error('获取失败');
	}

	protected function modifySpuIntroduceImage()
	{
		$id = ipost('item_id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$sort = ipost('sort');
		$rst = make('app/service/product/IntroduceUsed')->updateData($id, ['sort'=>$sort]);
		if ($rst) {
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}

	protected function deleteSpuIntroduceImage()
	{
		$id = ipost('item_id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$sort = ipost('sort');
		$rst = make('app/service/product/IntroduceUsed')->deleteData($id);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function addSpuIntroduceImage()
	{
		$spuId = ipost('spu_id');
		$attachId = ipost('attach_id');
		if (empty($spuId) || empty($attachId)) {
			$this->error('参数不正确');
		}
		$imageService = make('app/service/product/IntroduceUsed');
		$data = ['spu_id'=>$spuId, 'attach_id'=>$attachId];
		if ($imageService->getCountData($data)) {
			$this->success('上传成功');
		}
		$data['sort'] = $imageService->loadData(['spu_id'=>$spuId], 'max(sort) as sort')['sort']+1;
		$rst = $imageService->insert($data);
		if ($rst) {
			$this->success('上传成功');
		}
		$this->error('上传失败');
	}

	protected function modifySpuData()
	{
		$spuId = ipost('spu_id');
		$name = ipost('name');
		$value = ipost('value');
		if (empty($spuId) || empty($name)) {
			$this->error('参数不正确');
		}
		$rst = make('app/service/product/SpuData')->updateData($spuId, [$name=>$value]);
		if ($rst) {
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}

	protected function editDescGroupInfo()
	{
		$idArr = ipost('id');
		$groupId = ipost('group_id');
		if (empty($idArr)) {
			$this->error('ID值不正确');
		}
		if (empty($groupId)) {
			$this->error('分组ID值不正确');
		}
		$rst = make('app/service/product/DescUsed')->updateData(['item_id'=>['in', $idArr]], ['descg_id'=>$groupId]);
		if ($rst) {
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}
}