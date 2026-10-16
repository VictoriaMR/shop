<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Product extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => 'SPU列表',
			'purchaseList' => '采购产品列表',
			'detail' => 'SPU详情',
			'operate' => '添加产品',
			'purchaseShop' => '供应商店铺',
		];
		$this->_ignore = ['detail', 'operate'];
		$this->_default = '产品管理';
		$this->_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['editInfo'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addJs();
		frame('Html')->addCss();
		
		$status = iget('status/d', -1);
		$cate = iget('cate/d', 0);
		$stime = iget('stime/t', '');
		$etime = iget('etime/t', '');
		$spuId = iget('spu_id/d', 0);
		$itemId = trim(iget('item_id', ''));
		$shopId = iget('shop_id/d', 0);
		$keyword = trim(iget('keyword', ''));
		$page = iget('page/d', 1);
		$size = iget('size/d', 30);

		// spu状态
		$spu = service('product/Spu');
		$statusList = $spu->getStatusList();

		// 分类
		$cateService = service('category/Category');
		$cateList = $cateService->getListFormat(false);
		$cateArr = array_column($cateList, 'name', 'cate_id');

		$where = [];
		$filterSpuIds = null;

		if ($spuId > 0) {
			$filterSpuIds = [$spuId];
		}
		if ($itemId !== '' || $shopId > 0) {
			$dataWhere = [];
			if ($itemId !== '') {
				$dataWhere['item_id'] = $itemId;
			}
			if ($shopId > 0) {
				$dataWhere['shop_id'] = $shopId;
			}
			$dataSpuRows = service('product/SpuData')->getListData($dataWhere, 'spu_id');
			$dataSpuIds = array_column($dataSpuRows ?: [], 'spu_id');
			$filterSpuIds = $filterSpuIds === null ? $dataSpuIds : array_values(array_intersect($filterSpuIds, $dataSpuIds));
		}
		if ($keyword !== '') {
			$lanSpuRows = service('product/Language')->getListData(['lan_id' => 0, 'name' => ['like', '%' . $keyword . '%']], 'spu_id');
			$lanSpuIds = array_column($lanSpuRows ?: [], 'spu_id');
			$filterSpuIds = $filterSpuIds === null ? $lanSpuIds : array_values(array_intersect($filterSpuIds, $lanSpuIds));
		}
		if ($filterSpuIds !== null) {
			if (empty($filterSpuIds)) {
				$where['spu_id'] = 0;
			} elseif (count($filterSpuIds) === 1) {
				$where['spu_id'] = (int)$filterSpuIds[0];
			} else {
				$where['spu_id'] = ['in', $filterSpuIds];
			}
		}

		if ($cate > 0) {
			$subCates = $cateService->sCate($cate);
			$cateIds = array_values(array_unique(array_merge([$cate], array_column($subCates ?: [], 'cate_id'))));
			$where['cate_id'] = count($cateIds) === 1 ? $cate : ['in', $cateIds];
		}
		if ($stime && $etime) {
			$where['add_time'] = ['between', [date('Y-m-d', strtotime($stime)).' 00:00:00', date('Y-m-d', strtotime($etime)).' 23:59:59']];
		} elseif ($stime) {
			$where['add_time'] = ['>=', date('Y-m-d', strtotime($stime)).' 00:00:00'];
		} elseif ($etime) {
			$where['add_time'] = ['<=', date('Y-m-d', strtotime($etime)).' 23:59:59'];
		}

		// 统计各状态商品数量（基于当前非状态筛选条件）
		$baseWhere = $where;
		$statusGroupRows = $spu->getListData($baseWhere, 'status, count(*) as c_total', 0, 0, [], 'status');
		$statusCounts = [-1 => 0];
		foreach ($statusList as $sk => $sv) {
			$statusCounts[$sk] = 0;
		}
		foreach ($statusGroupRows ?: [] as $sRow) {
			$st = (int)$sRow['status'];
			$cnt = (int)$sRow['c_total'];
			$statusCounts[$st] = $cnt;
			$statusCounts[-1] += $cnt;
		}

		if ($status > -1) {
			$where['status'] = $status;
		}

		$total = $spu->getCountData($where);
		$list = [];
		if ($total > 0) {
			$list = $spu->getList($where, '*', $page, $size, ['spu_id' => 'desc']);
			$spuIds = array_column($list, 'spu_id');

			// 批量查询扩展数据 (货源链接、渠道、店铺ID)
			$spuDataRows = service('product/SpuData')->getListData(['spu_id' => ['in', $spuIds]]);
			$spuDataMap = array_column($spuDataRows ?: [], null, 'spu_id');

			// 批量查询店铺名称
			$shopIds = array_values(array_unique(array_filter(array_column($spuDataRows ?: [], 'shop_id'))));
			$shopMap = [];
			if (!empty($shopIds)) {
				$shopRows = purchase()->shop()->getListData(['purchase_shop_id' => ['in', $shopIds]], 'purchase_shop_id,name,url');
				$shopMap = array_column($shopRows ?: [], null, 'purchase_shop_id');
			}

			// 批量聚合统计各SPU下的SKU数量与总库存
			$skuStatRows = service('product/Sku')->getListData(
				['spu_id' => ['in', $spuIds]],
				'spu_id, count(*) as sku_count, sum(case when status=1 then 1 else 0 end) as active_sku_count, sum(stock) as total_stock',
				0,
				0,
				[],
				'spu_id'
			);
			$skuStatMap = array_column($skuStatRows ?: [], null, 'spu_id');

			foreach ($list as $key => $value) {
				$sid = $value['spu_id'];
				$sData = $spuDataMap[$sid] ?? [];
				$sStat = $skuStatMap[$sid] ?? [];
				$sShopId = (int)($sData['shop_id'] ?? 0);
				$list[$key]['status_text'] = $statusList[$value['status']] ?? '--';
				$list[$key]['spu_data'] = $sData;
				$list[$key]['shop_info'] = $shopMap[$sShopId] ?? [];
				$list[$key]['sku_count'] = (int)($sStat['sku_count'] ?? 0);
				$list[$key]['active_sku_count'] = (int)($sStat['active_sku_count'] ?? 0);
				$list[$key]['total_stock'] = (int)($sStat['total_stock'] ?? 0);
			}
		}

		$filterShop = $shopId > 0 ? (purchase()->shop()->loadData($shopId, 'purchase_shop_id,name') ?: []) : [];

		$this->view([
			'spuId'        => $spuId,
			'itemId'       => $itemId,
			'shopId'       => $shopId,
			'filterShop'   => $filterShop,
			'keyword'      => $keyword,
			'status'       => $status,
			'statusList'   => $statusList,
			'statusCounts' => $statusCounts,
			'cate'         => $cate,
			'cateList'     => $cateList,
			'cateArr'      => $cateArr,
			'stime'        => $stime,
			'etime'        => $etime,
			'total'        => $total,
			'size'         => $size,
			'list'         => $list,
		]);
	}

	public function purchaseList()
	{
		if (isAjax()) {
			$opn = ipost('opn');
			if (in_array($opn, ['edit'])) {
				$opn .= 'PurchaseList';
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();

		$id = iget('id/d', 0);
		$status = iget('status/d', -1);
		$channelId = iget('channel_id/d', 0);
		$shopId = iget('shop_id/d', 0);
		$itemId = trim(iget('item_id', ''));
		$keyword = trim(iget('keyword', ''));
		$stime = iget('stime/t', '');
		$etime = iget('etime/t', '');
		$page = iget('page/d', 1);
		$size = iget('size/d', 30);

		$where = [];
		if ($id > 0) {
			$where['purchase_spu_id'] = $id;
		}
		if ($status > -1) {
			$where['status'] = $status;
		}
		if ($channelId > 0) {
			$where['channel_id'] = $channelId;
		}
		if ($shopId > 0) {
			$where['purchase_shop_id'] = $shopId;
		}
		if ($itemId !== '') {
			$where['item_id'] = $itemId;
		}
		if ($keyword !== '') {
			$where['name'] = ['like', '%' . $keyword . '%'];
		}
		if ($stime && $etime) {
			$where['add_time'] = ['between', [date('Y-m-d', strtotime($stime)).' 00:00:00', date('Y-m-d', strtotime($etime)).' 23:59:59']];
		} elseif ($stime) {
			$where['add_time'] = ['>=', date('Y-m-d', strtotime($stime)).' 00:00:00'];
		} elseif ($etime) {
			$where['add_time'] = ['<=', date('Y-m-d', strtotime($etime)).' 23:59:59'];
		}
		$total = purchase()->spu()->count($where);
		$list = [];
		if ($total > 0) {
			$list = purchase()->spu()->getListData($where, '*', $page, $size, ['purchase_spu_id' => 'desc']);
			// 用户
			$userList = array_values(array_unique(array_filter(array_column($list, 'mem_id'))));
			if (!empty($userList)) {
				$userList = service('member/Member')->getListData(['mem_id' => ['in', $userList]], 'mem_id,nick_name,avatar,sex');
				$userList = array_column($userList, null, 'mem_id');
			}
			// 店铺
			$shopIds = array_values(array_unique(array_filter(array_column($list, 'purchase_shop_id'))));
			$shopList = [];
			if (!empty($shopIds)) {
				$shopList = purchase()->shop()->getListData(['purchase_shop_id' => ['in', $shopIds]]);
				$shopList = array_column($shopList, null, 'purchase_shop_id');
			}
			// 关联已入库SPU
			$itemIds = array_values(array_unique(array_filter(array_column($list, 'item_id'))));
			$spuMap = [];
			if (!empty($itemIds)) {
				$spuDataRows = service('product/SpuData')->getListData(['item_id' => ['in', $itemIds]], 'spu_id,item_id');
				$spuMap = array_column($spuDataRows, 'spu_id', 'item_id');
			}
			foreach ($list as $key => $value) {
				$list[$key]['user_info'] = $this->avatar($userList[$value['mem_id']] ?? '');
				$list[$key]['shop_info'] = $shopList[$value['purchase_shop_id']] ?? [];
				$list[$key]['url'] = purchase()->spu()->url((int)$value['channel_id'], (int)$value['item_id']);
				$list[$key]['spu_id'] = (int)($spuMap[$value['item_id']] ?? 0);
				// 标题补全
				if ($value['status'] == purchase()->spu()->getConst('STATUS_SET') && !$value['name']) {
					$list[$key]['name'] = purchase()->spu()->updateTitle($value['channel_id'], $value['item_id']);
				}
			}
		}

		$filterShop = $shopId > 0 ? (purchase()->shop()->loadData($shopId, 'purchase_shop_id,name') ?: []) : [];
		$channelList = purchase()->channel()->getListData();
		$channelList = array_column($channelList, 'name', 'channel_id');
		$statusList = purchase()->spu()->getStatusList();

		$this->view([
			'id'          => $id,
			'status'      => $status,
			'channel_id'  => $channelId,
			'shop_id'     => $shopId,
			'filterShop'  => $filterShop,
			'item_id'     => $itemId,
			'keyword'     => $keyword,
			'stime'       => $stime,
			'etime'       => $etime,
			'total'       => $total,
			'size'        => $size,
			'list'        => $list,
			'channelList' => $channelList,
			'statusList'  => $statusList,
		]);
	}

	protected function editPurchaseList()
	{
		$id = ipost('id/d', 0);
		$status = ipost('status/d', 0);
		if ($id <= 0) {
			$this->error('参数不正确');
		}
		$statusList = purchase()->spu()->getStatusList();
		if (!isset($statusList[$status])) {
			$this->error('状态不正确');
		}
		$rst = purchase()->spu()->updateData($id, ['status'=>$status]);
		if ($rst) {
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}

	public function operate()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['attrMap', 'addProduct'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();

		$id = iget('id/d', 0);
		if ($id <= 0) {
			\App::error('参数不正确');
		} else {
			$info = purchase()->spu()->getInfo($id);
			if (empty($info['sku'])) {
				\App::error('数据不存在, 请重新上传');
			} else {
				$shopInfo = !empty($info['purchase_shop_id']) ? (purchase()->shop()->loadData($info['purchase_shop_id']) ?: []) : [];
				// 映射属性名
				$attrNs = !empty($info['attr']) ? attr()->nameMap()->getMapList(array_column($info['attr'], 'name')) : [];
				$attrNs = array_column($attrNs ?: [], null, 'name');
				// 映射属性值（同时支持原词与去噪清洗后的词匹配字典）
				$rawValNames = [];
				foreach ($info['attr'] as $value) {
					if (!empty($value['value']) && is_array($value['value'])) {
						foreach ($value['value'] as $vv) {
							if (!empty($vv['name'])) {
								$rawValNames[] = $vv['name'];
							}
							if (!empty($vv['clean_name'])) {
								$rawValNames[] = $vv['clean_name'];
							}
						}
					}
				}
				$attrVs = !empty($rawValNames) ? attr()->valueMap()->getMapList($rawValNames) : [];
				$attrVs = array_column($attrVs ?: [], null, 'name');

				$cateList = category()->getListFormat(false);
				$channelList = array_column(purchase()->channel()->getListData(), 'name', 'channel_id');
				$nextId = purchase()->spu()->getNextPendingId($id);

				$this->assign('attrNs', $attrNs);
				$this->assign('attrVs', $attrVs);
				$this->assign('info', $info);
				$this->assign('shopInfo', $shopInfo);
				$this->assign('cateList', $cateList);
				$this->assign('channelList', $channelList);
				$this->assign('nextId', $nextId);
			}
		}
		$this->view();
	}

	protected function attrMap()
	{
		$name = strTrim(ipost('name', ''));
		$fromName = strTrim(ipost('from_name', ''));
		$type = ipost('type/d', 0);
		$attr = ipost('attr/a', []);
		if ($name === '' || $fromName === '' || !in_array($type, [1, 2])) {
			$this->error('参数不正确');
		}
		$upperName = strtoupper($name);
		if ($type == 1) {
			$rst = attr()->nameMap()->addMap($fromName, $upperName);
		} else {
			$rst = attr()->valueMap()->addMap($fromName, $upperName, $attr);
		}
		if ($rst) {
			$this->success('添加属性映射成功', ['to_name' => $upperName]);
		}
		$this->error('添加属性映射失败');
	}

	protected function addProduct()
	{
		$id = ipost('id/d', 0);
		$cate_id = ipost('cate_id/d', 0);
		$spu_name = strTrim(ipost('spu_name', ''));
		$submitMode = strTrim(ipost('submit_mode', 'save'));
		if ($id <= 0) {
			$this->error('ID参数不正确');
		}
		if ($cate_id <= 0) {
			$this->error('请选择产品分类');
		}
		if ($spu_name === '') {
			$this->error('SPU标题不能为空');
		}
		$postData = ipost();
		if (empty($postData['sku']) || !is_array($postData['sku'])) {
			$this->error('SKU数据不能为空');
		}
		// 过滤未勾选的SKU
		$checkedSkus = [];
		foreach ($postData['sku'] as $skuKey => $skuRow) {
			if (!empty($skuRow['checked'])) {
				$checkedSkus[$skuKey] = $skuRow;
			}
		}
		if (empty($checkedSkus)) {
			$this->error('请至少勾选一个有效SKU');
		}
		$postData['sku'] = $checkedSkus;
		$enabledGroups = isset($postData['attr_group_checked']) && is_array($postData['attr_group_checked']) ? array_values($postData['attr_group_checked']) : [];
		$postData['attr_group_checked'] = $enabledGroups;
		$enabledGroupFlip = array_flip($enabledGroups);

		// 静默批量沉淀属性名与属性值映射词典（越用越聪明，省去逐个弹窗AJAX）
		if (!empty($postData['attr_name_map']) && is_array($postData['attr_name_map'])) {
			$activeNameMap = [];
			foreach ($postData['attr_name_map'] as $fromN => $toN) {
				if (isset($enabledGroupFlip[$fromN]) && strTrim((string)$toN) !== '') {
					$activeNameMap[$fromN] = $toN;
				}
			}
			if (!empty($activeNameMap)) {
				attr()->nameMap()->batchSaveMap($activeNameMap);
			}
		}
		if (!empty($postData['attr_val_map']) && is_array($postData['attr_val_map'])) {
			attr()->valueMap()->batchSaveMap($postData['attr_val_map']);
		}

		$info = purchase()->spu()->loadData($id);
		if (empty($info)) {
			$this->error('数据不存在');
		}
		$rst = purchase()->spu()->saveResult($info['channel_id'], $info['item_id'], $postData, 'save_data');
		if ($rst !== false) {
			purchase()->spu()->updateData($id, [
				'status' => purchase()->spu()->getConst('STATUS_SPU'),
				'name'   => $spu_name,
			]);
			$nextId = purchase()->spu()->getNextPendingId($id);
			$nextUrl = $nextId > 0 ? adminUrl('product/operate', ['id' => $nextId]) : adminUrl('product/purchaseList');

			if ($submitMode === 'direct') {
				$spuId = purchase()->spu()->operateSpu($id);
				if ($spuId) {
					$this->success('直通入库成功 (SPU #' . $spuId . ')', [
						'spu_id'   => $spuId,
						'next_id'  => $nextId,
						'next_url' => $nextUrl,
					]);
				}
				$errReason = purchase()->spu()->getLastError() ?: '请检查SKU价格或图片网络';
				$this->error('配置已保存，但生成SPU失败：' . $errReason);
			}

			$this->success('保存成功', [
				'next_id'  => $nextId,
				'next_url' => $nextUrl,
			]);
		}
		$this->error('保存失败');
	}

	public function detail()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['editInfo', 'getSpuNameLanguage', 'transfer', 'editSpuLanguage', 'modifySpuImage', 'addSpuImage', 'deleteSpuImage', 'editSkuInfo', 'recalcSkuPrice', 'modifySkuAttrImage', 'modifySpuDesc', 'deleteSpuDesc', 'batchDeleteSpuDesc', 'getSpuDescInfo', 'modifySpuIntroduceImage', 'deleteSpuIntroduceImage', 'addSpuIntroduceImage', 'modifySpuData', 'editDescGroupInfo'])) {
				$this->$opn();
			}
			$this->error('未知请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();
		$spuService = service('product/Spu');
		$info = $spuService->getAdminInfo(iget('id/d', 0));
		if (!empty($info)) {
			// spu状态
			$statusList = $spuService->getStatusList();
			// 全部分类列表（允许跨根分类修改）
			$siteCate = category()->getListFormat(false);
			$info['category'] = array_reverse(category()->pCate($info['cate_id']));

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
			$groupList = service('desc/Group')->getListData();
			$this->assign('info', $info);
			$this->assign('statusList', $statusList);
			$this->assign('groupList', $groupList);
			$this->assign('siteCate', $siteCate);
		}
		$this->view();
	}

	protected function getSpuNameLanguage()
	{
		$id = ipost('id/d', 0);
		if ($id <= 0) {
			$this->error('参数不正确');
		}
		$list = service('product/Language')->getListData(['spu_id'=>$id]);
		$list = array_column($list, null, 'lan_id');
		$languageList = sys()->language()->getListData();
		$result = [];
		foreach ($languageList as $value) {
			$lanId = $value['lan_id'];
			$result[$lanId] = [
				'lan_id'        => $lanId,
				'tr_code'       => $value['tr_code'],
				'name'          => $list[$lanId]['name'] ?? '',
				'language_name' => $lanId == 0 ? '中文名称' : ($value['name2'] ?: $value['name']),
			];
		}
		$this->success('获取成功', $result);
	}

	protected function transfer()
	{
		$trCode = ipost('tr_code');
		$name = ipost('name');
		if (empty($name) || empty($trCode)) {
			$this->error('参数不正确');
		}
		$rst = service('tool/Translate')->getText($name, $trCode);
		if ($rst !== false && $rst !== null && $rst !== '') {
			$this->success('翻译成功', $rst);
		}
		$this->error('翻译失败');
	}

	protected function editSpuLanguage()
	{
		$spuId = ipost('spu_id/d', 0);
		if ($spuId <= 0) {
			$this->error('ID值不正确');
		}
		$language = ipost('language');
		if (!empty($language)) {
			$services = service('product/Language');
			foreach ($language as $key => $value) {
				$services->setNxLanguage($spuId, $key, strTrim($value));
			}
		}
		$this->addLog('修改产品语言-'.$spuId);
		$this->success('操作成功');
	}

	protected function modifySpuImage()
	{
		$spuId = ipost('spu_id/d', 0);
		$attachId = ipost('attach_id/d', 0);
		if ($spuId <= 0 || $attachId <= 0) {
			$this->error('参数不正确');
		}
		$sort = ipost('sort/d', 0);
		$rst = service('product/SpuImage')->updateData(['spu_id'=>$spuId, 'attach_id'=>$attachId], ['sort'=>$sort]);
		if ($rst !== false) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function deleteSpuImage()
	{
		$spuId = ipost('spu_id/d', 0);
		$itemId = ipost('item_id/d', 0);
		if ($spuId <= 0 || $itemId <= 0) {
			$this->error('参数不正确');
		}
		$rst = service('product/SpuImage')->deleteData(['spu_id'=>$spuId, 'item_id'=>$itemId]);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function addSpuImage()
	{
		$spuId = ipost('spu_id/d', 0);
		$attachId = ipost('attach_id/d', 0);
		if ($spuId <= 0 || $attachId <= 0) {
			$this->error('参数不正确');
		}
		$imageService = service('product/SpuImage');
		$data = ['spu_id'=>$spuId, 'attach_id'=>$attachId];
		if ($imageService->getCountData($data)) {
			$this->success('上传成功');
		}
		$maxRow = $imageService->loadData(['spu_id'=>$spuId], 'max(sort) as sort');
		$data['sort'] = (int)($maxRow['sort'] ?? 0) + 1;
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
		$id = (int)($param['spu_id'] ?? 0);
		if ($id <= 0) {
			$this->error('ID不能为空');
		}
		if (isset($param['status'])) {
			$data['status'] = (int)$param['status'];
		}
		if (isset($param['free_ship'])) {
			$data['free_ship'] = (int)$param['free_ship'];
		}
		if (isset($param['is_hot'])) {
			$data['is_hot'] = (int)$param['is_hot'];
		}
		if (isset($param['cate_id'])) {
			$data['cate_id'] = (int)$param['cate_id'];
		}
		if (isset($param['gender'])) {
			$data['gender'] = (int)$param['gender'];
		}
		if (isset($param['attach_id'])) {
			$data['attach_id'] = (int)$param['attach_id'];
		}
		if (empty($data)) {
			$this->error('参数不正确');
		}
		$rst = service('product/Spu')->updateData(['spu_id'=>$id], $data);
		if ($rst !== false) {
			if (isset($data['attach_id'])) {
				service('product/SpuImage')->updateData(['spu_id'=>$id, 'attach_id'=>$data['attach_id']], ['sort'=>0]);
			}
			$this->success('修改成功');
		}
		$this->error('修改失败');
	}

	protected function editSkuInfo()
	{
		$skuId = ipost('sku_id');
		$spuId = ipost('spu_id/d', 0);
		if (empty($skuId) && $spuId <= 0) {
			$this->error('参数不正确');
		}
		$skuService = service('product/Sku');
		if (!empty($skuId)) {
			$skuId = is_array($skuId) ? array_values(array_filter(array_map('intval', $skuId))) : [(int)$skuId];
		} elseif ($spuId > 0) {
			$skuRows = $skuService->getListData(['spu_id' => $spuId], 'sku_id');
			$skuId = array_column($skuRows ?: [], 'sku_id');
		}
		if (empty($skuId)) {
			$this->error('未找到目标SKU');
		}
		if ($spuId <= 0) {
			$firstSku = $skuService->loadData($skuId[0], 'spu_id');
			$spuId = (int)($firstSku['spu_id'] ?? 0);
		}
		$param = ipost();
		$data = [];
		if (isset($param['attach_id'])) {
			$data['attach_id'] = (int)$param['attach_id'];
		}
		if (isset($param['status'])) {
			$data['status'] = (int)$param['status'];
		}
		if (isset($param['price'])) {
			$data['price'] = round((float)$param['price'], 2);
		}
		if (isset($param['original_price'])) {
			$data['original_price'] = round((float)$param['original_price'], 2);
		}
		if (isset($param['stock'])) {
			$data['stock'] = (int)$param['stock'];
		}
		if (isset($param['cost_price'])) {
			$data['cost_price'] = round((float)$param['cost_price'], 2);
		}
		if (isset($param['volume'])) {
			$data['volume'] = trim((string)$param['volume']);
		}
		if (isset($param['weight'])) {
			$data['weight'] = (int)$param['weight'];
		}
		if (empty($data)) {
			$this->error('更新的参数为空');
		}
		if (isset($data['cost_price'])) {
			$service = service('product/SkuData');
		} else {
			$service = $skuService;
		}
		$rst = $service->updateData(['sku_id' => ['in', $skuId]], $data);
		if ($rst !== false) {
			$priceRange = [];
			if ($spuId > 0 && (isset($data['price']) || isset($data['status']))) {
				$priceRange = service('product/Spu')->syncSpuPriceRange($spuId);
			}
			$this->success('操作成功', $priceRange);
		}
		$this->error('操作失败');
	}

	/**
	 * 根据SKU成本价+SPU运费一键重算选中或全部SKU售价
	 */
	protected function recalcSkuPrice()
	{
		$spuId = ipost('spu_id/d', 0);
		$skuIds = ipost('sku_id/a', []);
		if ($spuId <= 0) {
			$this->error('SPU ID不正确');
		}
		$spuService = service('product/Spu');
		$skuService = service('product/Sku');
		$spuData = service('product/SpuData')->loadData($spuId, 'post_fee');
		$postFee = (float)($spuData['post_fee'] ?? 0);

		$where = ['spu_id' => $spuId];
		if (!empty($skuIds)) {
			$where['sku_id'] = ['in', array_values(array_filter(array_map('intval', $skuIds)))];
		}
		$skus = $skuService->getListData($where, 'sku_id');
		if (empty($skus)) {
			$this->error('没有找到需要重算的SKU');
		}
		$targetSkuIds = array_column($skus, 'sku_id');
		$skuDataRows = service('product/SkuData')->getListData(['sku_id' => ['in', $targetSkuIds]], 'sku_id,cost_price');
		$costMap = array_column($skuDataRows ?: [], 'cost_price', 'sku_id');

		$updatedCount = 0;
		foreach ($targetSkuIds as $sid) {
			$cost = (float)($costMap[$sid] ?? 0);
			if ($cost > 0) {
				$newPrice = $spuService->getPrice($cost + $postFee);
				$skuService->updateData($sid, ['price' => $newPrice]);
				$updatedCount++;
			}
		}
		$priceRange = $spuService->syncSpuPriceRange($spuId);
		$this->success('已按公式重算 ' . $updatedCount . ' 个SKU售价', $priceRange);
	}

	protected function modifySkuAttrImage()
	{
		$spuId = ipost('spu_id/d', 0);
		$attrId = ipost('attrn_id/d', 0);
		$attvId = ipost('attrv_id/d', 0);
		$attachId = ipost('attach_id/d', 0);
		if ($spuId <= 0 || $attrId <= 0 || $attvId <= 0) {
			$this->error('参数不正确');
		}
		$skuService = service('product/Sku');
		$skuId = $skuService->getListData(['spu_id'=>$spuId], 'sku_id');
		if (empty($skuId)) {
			$this->error('SKU ID不能为空');
		}
		$skuId = array_column($skuId, 'sku_id');
		$attrUsed = service('product/AttrUsed');
		$list = $attrUsed->getListData(['sku_id'=>['in', $skuId], 'attrn_id'=>$attrId, 'attrv_id'=>$attvId], 'item_id,sku_id');
		if (empty($list)) {
			$this->error('找不到sku属性');
		}
		$rst = $attrUsed->updateData(['item_id'=>['in', array_column($list, 'item_id')]], ['attach_id'=>$attachId]);
		$skuService->updateData(['sku_id'=>['in', array_unique(array_column($list, 'sku_id'))]], ['attach_id'=>$attachId]);
		if ($rst !== false) {
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}

	protected function modifySpuDesc()
	{
		$id = ipost('item_id/d', 0);
		$param = ipost();
		$data = [];
		if (isset($param['sort'])) {
			$data['sort'] = (int)$param['sort'];
		}
		if (empty($data) && empty($param['spu_id'])) {
			$this->error('参数不正确');
		}
		if (!empty($param['spu_id'])) {
			$name = trim($param['name'] ?? '');
			$value = trim($param['value'] ?? '');
			if ($name === '') {
				$this->error('描述名不能为空');
			}
			if ($value === '') {
				$this->error('描述值不能为空');
			}
			$tempArr = service('desc/Name')->addNotExist($name);
			$data['descn_id'] = $tempArr[$name];
			$tempArr = service('desc/Value')->addNotExist($value);
			$data['descv_id'] = $tempArr[$value];
			$data['spu_id'] = (int)$param['spu_id'];
		}
		if ($id <= 0) {
			$rst = service('product/DescUsed')->addDescUsed((int)$param['spu_id'], $data);
		} else {
			$rst = service('product/DescUsed')->updateData($id, $data);
		}
		if ($rst !== false) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function deleteSpuDesc()
	{
		$id = ipost('item_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}
		$rst = service('product/DescUsed')->deleteData($id);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function batchDeleteSpuDesc()
	{
		$ids = ipost('id/a', []);
		$ids = array_values(array_filter(array_map('intval', $ids)));
		if (empty($ids)) {
			$this->error('请先选择需要删除的描述项');
		}
		$rst = service('product/DescUsed')->deleteData(['item_id' => ['in', $ids]]);
		if ($rst) {
			$this->success('批量删除成功');
		}
		$this->error('批量删除失败');
	}

	protected function getSpuDescInfo()
	{
		$id = ipost('item_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}
		$info = service('product/DescUsed')->loadData($id);
		if ($info) {
			$tempArr = service('desc/Name')->loadData($info['descn_id'], 'name');
			$info['name'] = $tempArr['name'] ?? '';
			$tempArr = service('desc/Value')->loadData($info['descv_id'], 'name');
			$info['value'] = $tempArr['name'] ?? '';
			$this->success('获取成功', $info);
		}
		$this->error('获取失败');
	}

	protected function modifySpuIntroduceImage()
	{
		$id = ipost('item_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}
		$sort = ipost('sort/d', 0);
		$rst = service('product/IntroUsed')->updateData($id, ['sort'=>$sort]);
		if ($rst !== false) {
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}

	protected function deleteSpuIntroduceImage()
	{
		$id = ipost('item_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}
		$rst = service('product/IntroUsed')->deleteData($id);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function addSpuIntroduceImage()
	{
		$spuId = ipost('spu_id/d', 0);
		$attachId = ipost('attach_id/d', 0);
		if ($spuId <= 0 || $attachId <= 0) {
			$this->error('参数不正确');
		}
		$imageService = service('product/IntroUsed');
		$data = ['spu_id'=>$spuId, 'attach_id'=>$attachId];
		if ($imageService->getCountData($data)) {
			$this->success('上传成功');
		}
		$maxRow = $imageService->loadData(['spu_id'=>$spuId], 'max(sort) as sort');
		$data['sort'] = (int)($maxRow['sort'] ?? 0) + 1;
		$rst = $imageService->insert($data);
		if ($rst) {
			$this->success('上传成功');
		}
		$this->error('上传失败');
	}

	protected function modifySpuData()
	{
		$spuId = ipost('spu_id/d', 0);
		$name = trim(ipost('name', ''));
		$value = ipost('value', '');
		if ($spuId <= 0 || $name === '') {
			$this->error('参数不正确');
		}
		$spuDataService = service('product/SpuData');
		if ($spuDataService->getCountData(['spu_id' => $spuId]) > 0) {
			$rst = $spuDataService->updateData($spuId, [$name => $value]);
		} else {
			$rst = $spuDataService->insert(['spu_id' => $spuId, $name => $value]);
		}
		if ($rst !== false) {
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
		if ($groupId === null || $groupId === '') {
			$this->error('分组ID值不正确');
		}
		$rst = service('product/DescUsed')->updateData(['item_id'=>['in', $idArr]], ['descg_id'=>(int)$groupId]);
		if ($rst !== false) {
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}

	public function purchaseShop()
	{
		frame('Html')->addCss('product/purchaseList');
		frame('Html')->addCss();
		frame('Html')->addJs();

		$id = iget('id/d', 0);
		$status = iget('status/d', -1);
		$channelId = iget('channel_id/d', 0);
		$uniqueId = trim(iget('unique_id', ''));
		$name = trim(iget('name', ''));
		$page = iget('page/d', 1);
		$size = iget('size/d', 30);

		$where = [];
		if ($id > 0) {
			$where['purchase_shop_id'] = $id;
		}
		if ($channelId > 0) {
			$where['channel_id'] = $channelId;
		}
		if ($uniqueId !== '') {
			$where['unique_id'] = $uniqueId;
		}
		if ($name !== '') {
			$where['name'] = ['like', '%' . $name . '%'];
		}
		$total = purchase()->shop()->count($where);
		$list = [];
		if ($total > 0) {
			$list = purchase()->shop()->getListData($where, '*', $page, $size, ['purchase_shop_id' => 'desc']);
			$shopIds = array_unique(array_column($list, 'purchase_shop_id'));
			if (!empty($shopIds)) {
				$productCount = purchase()->spu()->getListData(['purchase_shop_id' => ['in', $shopIds]], 'count(*) as c_count,purchase_shop_id', 0, 0, [], 'purchase_shop_id');
				$productCount = array_column($productCount, 'c_count', 'purchase_shop_id');
			}
			foreach ($list as $key => $value) {
				$list[$key]['product_count'] = (int)($productCount[$value['purchase_shop_id']] ?? 0);
			}
		}

		$channelList = service('purchase/Channel')->getListData();
		$channelList = array_column($channelList, 'name', 'channel_id');

		$this->view([
			'id'          => $id,
			'status'      => $status,
			'channel_id'  => $channelId,
			'unique_id'   => $uniqueId,
			'name'        => $name,
			'total'       => $total,
			'size'        => $size,
			'list'        => $list,
			'channelList' => $channelList,
		]);
	}
}