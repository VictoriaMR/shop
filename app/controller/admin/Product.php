<?php

namespace app\controller\admin;
use app\controller\AdminBase;
use app\service\log\Product as ProductLog;

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
			'log' => '产品日志',
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
		$oldRow = purchase()->spu()->loadData($id, 'purchase_spu_id,status,name,item_id');
		$rst = purchase()->spu()->updateData($id, ['status'=>$status]);
		if ($rst) {
			$oldSt = (int)($oldRow['status'] ?? 0);
			$logTitle = '修改采购商品状态 #' . $id;
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_PURCHASE, 0, $logTitle, [
				[
					'field' => 'status',
					'label' => '采购状态 (ID:' . $id . ')',
					'old'   => ($statusList[$oldSt] ?? $oldSt) . ' (' . $oldSt . ')',
					'new'   => ($statusList[$status] ?? $status) . ' (' . $status . ')',
				],
			]);
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
					$logTitle = '采购直通入库生成 SPU #' . $spuId;
					$this->addLog($logTitle);
					service('log/Product')->addLog(ProductLog::TYPE_PURCHASE, (int)$spuId, $logTitle, [
						['field' => 'purchase_spu_id', 'label' => '采购记录ID', 'val' => (string)$id],
						['field' => 'spu_name', 'label' => '商品标题', 'val' => $spu_name],
						['field' => 'cate_id', 'label' => '分类ID', 'val' => (string)$cate_id],
						['field' => 'sku_count', 'label' => '入库SKU数', 'val' => (string)count($checkedSkus)],
					]);
					$this->success('直通入库成功 (SPU #' . $spuId . ')', [
						'spu_id'   => $spuId,
						'next_id'  => $nextId,
						'next_url' => $nextUrl,
					]);
				}
				$errReason = purchase()->spu()->getLastError() ?: '请检查SKU价格或图片网络';
				$this->error('配置已保存，但生成SPU失败：' . $errReason);
			}

			$logTitle = '保存采购商品入库配置 #' . $id;
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_PURCHASE, 0, $logTitle, [
				['field' => 'purchase_spu_id', 'label' => '采购记录ID', 'val' => (string)$id],
				['field' => 'spu_name', 'label' => '商品标题', 'val' => $spu_name],
				['field' => 'cate_id', 'label' => '分类ID', 'val' => (string)$cate_id],
				['field' => 'sku_count', 'label' => '勾选SKU数', 'val' => (string)count($checkedSkus)],
			]);
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
		$diff = [];
		if (!empty($language) && is_array($language)) {
			$services = service('product/Language');
			$oldRows = $services->getListData(['spu_id' => $spuId], 'lan_id,name');
			$oldMap = array_column($oldRows ?: [], 'name', 'lan_id');
			$langRows = sys()->language()->getListData();
			$langMap = [];
			foreach ($langRows ?: [] as $lr) {
				$langMap[$lr['lan_id']] = $lr['name2'] ?: $lr['name'];
			}
			foreach ($language as $key => $value) {
				$services->setNxLanguage($spuId, $key, strTrim($value));
			}
			$diff = service('log/Product')->diffLanguage($language, $oldMap, $langMap);
		}
		$logTitle = '修改产品多语言标题 #' . $spuId;
		$this->addLog($logTitle);
		service('log/Product')->addLog(ProductLog::TYPE_LANGUAGE, $spuId, $logTitle, $diff);
		$this->success('操作成功');
	}

	protected function modifySpuImage()
	{
		$spuId = ipost('spu_id/d', 0);
		if ($spuId <= 0) {
			$this->error('参数不正确');
		}
		$attachIds = ipost('attach_ids/a', []);
		if (!empty($attachIds)) {
			$rst = service('product/SpuImage')->updateSort($spuId, $attachIds);
			if ($rst !== false) {
				$logTitle = '拖动调整SPU主图排序 #' . $spuId;
				$this->addLog($logTitle);
				service('log/Product')->addLog(ProductLog::TYPE_IMAGE, $spuId, $logTitle, [
					['field' => 'attach_ids', 'label' => '主图排序序列(attach_id)', 'val' => implode(' -> ', array_map('intval', $attachIds))],
				]);
				$this->success('排序已保存');
			}
			$this->error('操作失败');
		}
		$attachId = ipost('attach_id/d', 0);
		if ($attachId <= 0) {
			$this->error('参数不正确');
		}
		$sort = ipost('sort/d', 0);
		$rst = service('product/SpuImage')->updateData(['spu_id'=>$spuId, 'attach_id'=>$attachId], ['sort'=>$sort]);
		if ($rst !== false) {
			$logTitle = '修改SPU主图排序 #' . $spuId;
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_IMAGE, $spuId, $logTitle, [
				['field' => 'sort', 'label' => '主图(attach_id:' . $attachId . ')排序', 'val' => (string)$sort],
			]);
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
		$oldRow = service('product/SpuImage')->loadData($itemId, 'item_id,spu_id,attach_id,sort');
		$rst = service('product/SpuImage')->deleteData(['spu_id'=>$spuId, 'item_id'=>$itemId]);
		if ($rst) {
			$logTitle = '删除SPU主图 #' . $spuId;
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_IMAGE, $spuId, $logTitle, [
				['field' => 'item_id', 'label' => '记录ID', 'val' => (string)$itemId],
				['field' => 'attach_id', 'label' => '图片附件ID', 'val' => (string)($oldRow['attach_id'] ?? '-')],
			]);
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
			$logTitle = '上传SPU主图 #' . $spuId;
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_IMAGE, $spuId, $logTitle, [
				['field' => 'attach_id', 'label' => '图片附件ID', 'val' => (string)$attachId],
				['field' => 'sort', 'label' => '排序权重', 'val' => (string)$data['sort']],
			]);
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
		$spuService = service('product/Spu');
		$oldInfo = $spuService->loadData($id);
		$rst = $spuService->updateData(['spu_id'=>$id], $data);
		if ($rst !== false) {
			if (isset($data['attach_id'])) {
				service('product/SpuImage')->updateData(['spu_id'=>$id, 'attach_id'=>$data['attach_id']], ['sort'=>0]);
			}
			$diff = service('log/Product')->diffSpuInfo($oldInfo ?: [], $data);
			$logTitle = isset($data['attach_id']) ? ('设置SPU主封面图 #' . $id) : ('修改SPU基础信息 #' . $id);
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_SPU, $id, $logTitle, $diff);
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
		$oldRow = count($skuId) === 1 ? ($service->loadData($skuId[0]) ?: []) : [];
		$rst = $service->updateData(['sku_id' => ['in', $skuId]], $data);
		if ($rst !== false) {
			$priceRange = [];
			if ($spuId > 0 && (isset($data['price']) || isset($data['status']))) {
				$priceRange = service('product/Spu')->syncSpuPriceRange($spuId);
			}
			$fieldLabels = [
				'attach_id'      => '绑定图片ID',
				'status'         => 'SKU状态',
				'price'          => '售价(￥)',
				'original_price' => '原价(￥)',
				'cost_price'     => '成本价(￥)',
				'stock'          => '库存',
				'volume'         => '体积(mm)',
				'weight'         => '重量(g)',
			];
			$detail = [];
			$skuScope = count($skuId) === 1 ? ('SKU #' . $skuId[0]) : ('共 ' . count($skuId) . ' 个SKU (' . implode(',', array_slice($skuId, 0, 6)) . (count($skuId) > 6 ? '...' : '') . ')');
			foreach ($data as $fk => $fv) {
				$fLabel = ($fieldLabels[$fk] ?? $fk) . ' [' . $skuScope . ']';
				$dispNew = $fk === 'status' ? ((int)$fv === 1 ? '在售(1)' : '下架(0)') : (string)$fv;
				if (!empty($oldRow) && array_key_exists($fk, $oldRow)) {
					$oldRaw = $oldRow[$fk];
					$dispOld = $fk === 'status' ? ((int)$oldRaw === 1 ? '在售(1)' : '下架(0)') : (string)$oldRaw;
					$detail[] = ['field' => $fk, 'label' => $fLabel, 'old' => $dispOld, 'new' => $dispNew];
				} else {
					$detail[] = ['field' => $fk, 'label' => $fLabel, 'val' => $dispNew];
				}
			}
			$logTitle = (count($skuId) > 1 ? '批量修改SKU信息' : ('修改SKU #' . $skuId[0])) . ' (SPU #' . $spuId . ')';
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_SKU, $spuId, $logTitle, $detail);
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
		$skus = $skuService->getListData($where, 'sku_id,price');
		if (empty($skus)) {
			$this->error('没有找到需要重算的SKU');
		}
		$targetSkuIds = array_column($skus, 'sku_id');
		$oldPriceMap = array_column($skus, 'price', 'sku_id');
		$skuDataRows = service('product/SkuData')->getListData(['sku_id' => ['in', $targetSkuIds]], 'sku_id,cost_price');
		$costMap = array_column($skuDataRows ?: [], 'cost_price', 'sku_id');

		$updatedCount = 0;
		$diff = [];
		foreach ($targetSkuIds as $sid) {
			$cost = (float)($costMap[$sid] ?? 0);
			if ($cost > 0) {
				$newPrice = $spuService->getPrice($cost + $postFee);
				$skuService->updateData($sid, ['price' => $newPrice]);
				$updatedCount++;
				$diff[] = [
					'field' => 'sku_' . $sid,
					'label' => 'SKU #' . $sid . ' 售价',
					'old'   => '￥' . ($oldPriceMap[$sid] ?? '0.00'),
					'new'   => '￥' . $newPrice . ' (成本￥' . $cost . '+邮费￥' . $postFee . ')',
				];
			}
		}
		$priceRange = $spuService->syncSpuPriceRange($spuId);
		$logTitle = '按公式重算SKU售价 #' . $spuId . ' (共' . $updatedCount . '个)';
		$this->addLog($logTitle);
		service('log/Product')->addLog(ProductLog::TYPE_SKU, $spuId, $logTitle, $diff);
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
		$list = $attrUsed->getListData(['sku_id'=>['in', $skuId], 'attrn_id'=>$attrId, 'attrv_id'=>$attvId], 'item_id,sku_id,attach_id');
		if (empty($list)) {
			$this->error('找不到sku属性');
		}
		$oldAttachId = (int)($list[0]['attach_id'] ?? 0);
		$rst = $attrUsed->updateData(['item_id'=>['in', array_column($list, 'item_id')]], ['attach_id'=>$attachId]);
		$skuService->updateData(['sku_id'=>['in', array_unique(array_column($list, 'sku_id'))]], ['attach_id'=>$attachId]);
		if ($rst !== false) {
			$logTitle = ($attachId > 0 ? '更换规格属性图片' : '移除规格属性图片') . ' #' . $spuId;
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_SKU, $spuId, $logTitle, [
				[
					'field' => 'attach_id',
					'label' => '规格图 (attrn:' . $attrId . '/attrv:' . $attvId . ')',
					'old'   => (string)$oldAttachId,
					'new'   => (string)$attachId,
				],
			]);
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}

	protected function modifySpuDesc()
	{
		$id = ipost('item_id/d', 0);
		$param = ipost();
		if (!empty($param['type'])) {
			if ($id <= 0) {
				$this->error('参数不正确');
			}
			$info = service('product/DescUsed')->loadData($id, 'item_id,spu_id,descg_id,descn_id');
			if (empty($info)) {
				$this->error('描述项不存在');
			}
			$sortType = trim($param['type']);
			$rst = service('tool/Sort')->sort('product_desc_used', $id, $sortType, 'item_id', 'sort', [
				'spu_id'   => (int)$info['spu_id'],
				'descg_id' => (int)$info['descg_id'],
			]);
			if ($rst) {
				$typeNames = ['top' => '置顶', 'up' => '上移', 'down' => '下移', 'bottom' => '置底'];
				$logTitle = '调整描述属性排序 #' . $info['spu_id'];
				$this->addLog($logTitle);
				service('log/Product')->addLog(ProductLog::TYPE_DESC, (int)$info['spu_id'], $logTitle, [
					['field' => 'item_id', 'label' => '描述记录ID', 'val' => (string)$id],
					['field' => 'type', 'label' => '排序动作', 'val' => $typeNames[$sortType] ?? $sortType],
				]);
				$this->success('排序成功');
			}
			$this->error('排序失败');
		}
		$data = [];
		if (isset($param['sort'])) {
			$data['sort'] = (int)$param['sort'];
		}
		if (empty($data) && empty($param['spu_id'])) {
			$this->error('参数不正确');
		}
		$name = trim($param['name'] ?? '');
		$value = trim($param['value'] ?? '');
		if (!empty($param['spu_id'])) {
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
		$spuId = (int)($param['spu_id'] ?? 0);
		if ($id <= 0) {
			if (empty($data['sort'])) {
				$maxRow = service('product/DescUsed')->loadData(['spu_id' => $spuId], 'max(sort) as sort');
				$data['sort'] = (int)($maxRow['sort'] ?? 0) + 1;
			}
			$rst = service('product/DescUsed')->addDescUsed($spuId, $data);
			if ($rst !== false) {
				$logTitle = '新增描述属性 #' . $spuId;
				$this->addLog($logTitle);
				service('log/Product')->addLog(ProductLog::TYPE_DESC, $spuId, $logTitle, [
					['field' => 'name', 'label' => '描述名', 'val' => $name],
					['field' => 'value', 'label' => '描述值', 'val' => $value],
					['field' => 'sort', 'label' => '排序权重', 'val' => (string)$data['sort']],
				]);
				$this->success('操作成功');
			}
		} else {
			$oldRow = service('product/DescUsed')->loadData($id);
			if ($spuId <= 0) {
				$spuId = (int)($oldRow['spu_id'] ?? 0);
			}
			$rst = service('product/DescUsed')->updateData($id, $data);
			if ($rst !== false) {
				$logTitle = '编辑描述属性 #' . $spuId;
				$this->addLog($logTitle);
				service('log/Product')->addLog(ProductLog::TYPE_DESC, $spuId, $logTitle, [
					['field' => 'item_id', 'label' => '描述记录ID', 'val' => (string)$id],
					['field' => 'name', 'label' => '描述名', 'val' => $name ?: '-'],
					['field' => 'value', 'label' => '描述值', 'val' => $value ?: '-'],
					['field' => 'sort', 'label' => '排序权重', 'val' => (string)($data['sort'] ?? ($oldRow['sort'] ?? 0))],
				]);
				$this->success('操作成功');
			}
		}
		$this->error('操作失败');
	}

	protected function deleteSpuDesc()
	{
		$id = ipost('item_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}
		$oldRow = service('product/DescUsed')->loadData($id);
		$rst = service('product/DescUsed')->deleteData($id);
		if ($rst) {
			$spuId = (int)($oldRow['spu_id'] ?? 0);
			$logTitle = '删除描述属性 #' . $spuId;
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_DESC, $spuId, $logTitle, [
				['field' => 'item_id', 'label' => '被删描述ID', 'val' => (string)$id],
			]);
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
		$firstRow = service('product/DescUsed')->loadData($ids[0], 'spu_id');
		$spuId = (int)($firstRow['spu_id'] ?? 0);
		$rst = service('product/DescUsed')->deleteData(['item_id' => ['in', $ids]]);
		if ($rst) {
			$logTitle = '批量删除描述属性 #' . $spuId . ' (共' . count($ids) . '项)';
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_DESC, $spuId, $logTitle, [
				['field' => 'item_ids', 'label' => '被删记录ID列表', 'val' => implode(', ', $ids)],
			]);
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
		$spuId = ipost('spu_id/d', 0);
		$itemIds = ipost('item_ids/a', []);
		if ($spuId > 0 && !empty($itemIds)) {
			$rst = service('product/IntroUsed')->updateSort($spuId, $itemIds);
			if ($rst !== false) {
				$logTitle = '拖动调整详情介绍图排序 #' . $spuId;
				$this->addLog($logTitle);
				service('log/Product')->addLog(ProductLog::TYPE_INTRO, $spuId, $logTitle, [
					['field' => 'item_ids', 'label' => '详情图排序序列(item_id)', 'val' => implode(' -> ', array_map('intval', $itemIds))],
				]);
				$this->success('排序已保存');
			}
			$this->error('排序失败');
		}
		$id = ipost('item_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}
		$sort = ipost('sort/d', 0);
		$oldRow = service('product/IntroUsed')->loadData($id, 'item_id,spu_id,sort');
		$rst = service('product/IntroUsed')->updateData($id, ['sort'=>$sort]);
		if ($rst !== false) {
			$spuId = (int)($oldRow['spu_id'] ?? 0);
			$logTitle = '修改详情介绍图排序 #' . $spuId;
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_INTRO, $spuId, $logTitle, [
				['field' => 'sort', 'label' => '详情图(item_id:' . $id . ')排序', 'old' => (string)($oldRow['sort'] ?? 0), 'new' => (string)$sort],
			]);
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
		$oldRow = service('product/IntroUsed')->loadData($id, 'item_id,spu_id,attach_id');
		$rst = service('product/IntroUsed')->deleteData($id);
		if ($rst) {
			$spuId = (int)($oldRow['spu_id'] ?? 0);
			$logTitle = '删除详情介绍图 #' . $spuId;
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_INTRO, $spuId, $logTitle, [
				['field' => 'item_id', 'label' => '记录ID', 'val' => (string)$id],
				['field' => 'attach_id', 'label' => '图片附件ID', 'val' => (string)($oldRow['attach_id'] ?? '-')],
			]);
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
			$logTitle = '上传详情介绍图 #' . $spuId;
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_INTRO, $spuId, $logTitle, [
				['field' => 'attach_id', 'label' => '图片附件ID', 'val' => (string)$attachId],
				['field' => 'sort', 'label' => '排序权重', 'val' => (string)$data['sort']],
			]);
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
		$oldRow = $spuDataService->loadData($spuId);
		if (!empty($oldRow)) {
			$rst = $spuDataService->updateData($spuId, [$name => $value]);
		} else {
			$rst = $spuDataService->insert(['spu_id' => $spuId, $name => $value]);
		}
		if ($rst !== false) {
			$labelMap = ['post_fee' => '基础邮费(￥)', 'weight' => '默认重量(g)', 'volume' => '默认体积(mm)'];
			$logTitle = '修改SPU扩展参数 #' . $spuId;
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_SPU, $spuId, $logTitle, [
				[
					'field' => $name,
					'label' => $labelMap[$name] ?? $name,
					'old'   => (string)($oldRow[$name] ?? '-'),
					'new'   => (string)$value,
				],
			]);
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
		$idList = is_array($idArr) ? array_values(array_filter(array_map('intval', $idArr))) : [(int)$idArr];
		$firstRow = !empty($idList) ? service('product/DescUsed')->loadData($idList[0], 'spu_id') : [];
		$spuId = (int)($firstRow['spu_id'] ?? 0);
		$rst = service('product/DescUsed')->updateData(['item_id'=>['in', $idList]], ['descg_id'=>(int)$groupId]);
		if ($rst !== false) {
			$groupInfo = (int)$groupId > 0 ? service('desc/Group')->loadData((int)$groupId, 'name') : [];
			$groupName = !empty($groupInfo['name']) ? $groupInfo['name'] : '默认分组';
			$logTitle = '设置描述属性分组 #' . $spuId;
			$this->addLog($logTitle);
			service('log/Product')->addLog(ProductLog::TYPE_DESC, $spuId, $logTitle, [
				['field' => 'group_id', 'label' => '目标分组', 'val' => $groupName . ' (ID:' . (int)$groupId . ')'],
				['field' => 'item_ids', 'label' => '关联描述项ID', 'val' => implode(', ', $idList)],
			]);
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

	/**
	 * 产品管理独立操作日志页
	 */
	public function log()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();
		$type = iget('type', -1);
		$spuId = trim((string)iget('spu_id', ''));
		$keyword = trim((string)iget('keyword', ''));
		$stime = trim((string)iget('stime', ''));
		$etime = trim((string)iget('etime', ''));
		$page = max(1, (int)iget('page', 1));
		$size = max(1, (int)iget('size', 20));

		$where = [];
		if ($type !== '' && (int)$type >= 0) {
			$where['type'] = (int)$type;
		}
		if ($spuId !== '') {
			$where['spu_id'] = (int)$spuId;
		}
		if ($keyword !== '') {
			$where['info'] = ['like', '%' . $keyword . '%'];
		}
		$stimeFull = $stime !== '' ? (strlen($stime) <= 10 ? $stime . ' 00:00:00' : $stime) : '';
		$etimeFull = $etime !== '' ? (strlen($etime) <= 10 ? $etime . ' 23:59:59' : $etime) : '';
		if ($stimeFull !== '' && $etimeFull !== '') {
			$where['add_time'] = ['between', [$stimeFull, $etimeFull]];
		} elseif ($stimeFull !== '') {
			$where['add_time'] = ['>=', $stimeFull];
		} elseif ($etimeFull !== '') {
			$where['add_time'] = ['<=', $etimeFull];
		}

		$logService = service('log/Product');
		$total = $logService->getCountData($where);
		$list = [];
		if ($total > 0) {
			$list = $logService->getListData($where, '*', $page, $size, ['log_id' => 'desc']);
			$memIds = array_values(array_unique(array_filter(array_column($list, 'mem_id'))));
			$spuIds = array_values(array_unique(array_filter(array_column($list, 'spu_id'))));

			$memberMap = [];
			if (!empty($memIds)) {
				$memberService = service('member/Member');
				$members = $memberService->getListData(
					['mem_id' => count($memIds) === 1 ? $memIds[0] : ['in', $memIds]],
					'mem_id, avatar, sex, first_name, last_name, nick_name, mobile',
					0,
					0
				);
				foreach ($members ?: [] as $m) {
					$firstName = $m['first_name'] ?? '';
					$lastName = $m['last_name'] ?? '';
					$m['fullName'] = ($firstName === $lastName) ? $firstName : trim($firstName . ' ' . $lastName);
					$m['avatar'] = $memberService->getAvatar($m['avatar'], $m['sex']);
					$memberMap[$m['mem_id']] = $m;
				}
			}

			$spuNameMap = [];
			if (!empty($spuIds)) {
				$lanRows = service('product/Language')->getListData([
					'spu_id' => count($spuIds) === 1 ? $spuIds[0] : ['in', $spuIds],
					'lan_id' => 0,
				], 'spu_id,name');
				$spuNameMap = array_column($lanRows ?: [], 'name', 'spu_id');
			}

			foreach ($list as &$item) {
				$item['member'] = $memberMap[$item['mem_id']] ?? [];
				$item['spu_name'] = $spuNameMap[$item['spu_id']] ?? '';
			}
			unset($item);
		}

		$this->view([
			'total'    => $total,
			'list'     => $list,
			'size'     => $size,
			'typeList' => $logService->getTypeList(),
			'search'   => [
				'type'    => $type,
				'spu_id'  => $spuId,
				'keyword' => $keyword,
				'stime'   => $stime,
				'etime'   => $etime,
			],
		]);
	}
}