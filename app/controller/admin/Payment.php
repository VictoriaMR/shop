<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Payment extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '支付账号管理',
			'paymentUsed' => '支付账号使用',
			'log' => '支付日志',
		];
		$this->_default = '支付管理';
		parent::_init();
	}

	public function index()
	{	
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'editInfo', 'deleteInfo', 'modifyInfo'])) {
				$this->$opn();
			}
			$this->error('未知请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();
		$type = iget('type');
		$isSandbox = iget('is_sandbox', -1);
		$name = iget('name');
		$page = max(1, (int)iget('page', 1));
		$size = max(1, min(100, (int)iget('size', 20)));

		$paymentService = service('payment/Payment');
		$where = [];
		if (!empty($type)) {
			$where['type'] = (int) $type;
		}
		if ($isSandbox >= 0) {
			$where['is_sandbox'] = (int) $isSandbox;
		}
		if (!empty($name)) {
			$where['name'] = ['like', '%'.trim($name).'%'];
		}

		$total = $paymentService->getCountData($where);

		if ($total > 0) {
			$list = $paymentService->getList($where, $page, $size);
		}

		$typeList = $paymentService->getTypeList();
		$sandBoxList = $paymentService->getSandBoxList();

		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->assign('type', $type);
		$this->assign('isSandbox', $isSandbox);
		$this->assign('name', $name);
		$this->assign('typeList', $typeList);
		$this->assign('sandBoxList', $sandBoxList);
		$this->view();
	}

	protected function getInfo()
	{
		$id = ipost('id/d', 0);
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$info = service('payment/Payment')->loadData($id);
		if (empty($info)) {
			$this->error('找不到数据');
		}
		$this->addLog('查看支付账户配置-'.$id);
		$snapshot = service('log/Payment')->snapshotPayment($info);
		service('log/Payment')->addLog(0, $id, '查看支付账户配置', $snapshot);
		$this->success('获取成功', $info);
	}

	protected function editInfo()
	{
		$id = (int) ipost('payment_id');
		$type = (int) ipost('type');
		$status = (int)ipost('status', 0);
		$isSandbox = (int)ipost('is_sandbox', 0);
		$name = trim(ipost('name'));
		$appKey = trim(ipost('app_key'));
		$secretKey = trim(ipost('secret_key'));
		$webhookKey = trim(ipost('webhook_key'));
		$remark = trim(ipost('remark'));
		if (empty($name)) {
			$this->error('账户名称不能为空');
		}
		if (empty($appKey)) {
			$this->error('App Key 不能为空');
		}
		if (empty($secretKey)) {
			$this->error('Secret Key 不能为空');
		}
		$paymentService = service('payment/Payment');
		$typeList = $paymentService->getTypeList();
		if (empty($typeList[$type])) {
			$this->error('账号类型不正确');
		}
		$data = [
			'name' => $name,
			'type' => $type,
			'app_key' => $appKey,
			'secret_key' => $secretKey,
			'webhook_key' => $webhookKey,
			'remark' => $remark,
			'status' => $status == 1 ? 1 : 0,
			'is_sandbox' => $isSandbox == 0 ? 0 : 1,
		];
		$logService = service('log/Payment');
		if (empty($id)) {
			$result = $paymentService->insertGetId($data);
			$msg = '新增支付账户-'.$result;
			$detail = $logService->snapshotPayment($data);
			$logItemId = $result;
			$logTitle = '新增支付账户';
		} else {
			$oldInfo = $paymentService->loadData($id);
			$result = $paymentService->updateData($id, $data);
			$msg = '修改支付账户-'.$id;
			$detail = $logService->diffPayment($oldInfo ?: [], $data);
			$logItemId = $id;
			$logTitle = '修改支付账户';
		}
		if ($result) {
			$this->addLog($msg);
			$logService->addLog(0, $logItemId, $logTitle, $detail);
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function deleteInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$paymentService = service('payment/Payment');
		$oldInfo = $paymentService->loadData($id);
		$result = $paymentService->deleteData($id);
		if ($result) {
			service('payment/Used')->deleteData(['payment_id'=>$id]);
			$this->addLog('删除支付账户配置-'.$id);
			$snapshot = service('log/Payment')->snapshotPayment($oldInfo ?: ['name' => '账户-'.$id]);
			service('log/Payment')->addLog(0, $id, '删除支付账户配置', $snapshot);
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function modifyInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$status = ipost('status');
		$isSandbox = ipost('is_sandbox');
		if (is_null($status) && is_null($isSandbox)) {
			$this->error('参数不正确');
		}
		$data = [];
		$type = '';
		if (!is_null($status)) {
			$data['status'] = $status == 1 ? 1 : 0;
			$type = '状态';
		}
		if (!is_null($isSandbox)) {
			$data['is_sandbox'] = $isSandbox == 1 ? 1 : 0;
			$type = '沙盒';
		}
		$oldInfo = service('payment/Payment')->loadData($id);
		$result = service('payment/Payment')->updateData($id, $data);
		if ($result) {
			$this->addLog('更改支付账户'.$type.'-'.$id);
			$diff = service('log/Payment')->diffPayment($oldInfo ?: [], $data);
			service('log/Payment')->addLog(0, $id, '更改支付账户' . $type, $diff);
			$this->success('设置成功');
		}
		$this->error('设置失败');
	}

	public function paymentUsed()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getUsedInfo', 'editUsedInfo', 'deleteUsedInfo'])) {
				$this->$opn();
			}
			$this->error('未知请求');
		}
		
		frame('Html')->addCss();
		frame('Html')->addJs();
		$type = iget('type', -1);
		$siteId = iget('site_id', -1);
		$paymentId = iget('payment_id', -1);
		$page = max(1, (int)iget('page', 1));
		$size = max(1, min(100, (int)iget('size', 20)));
		$where = [];
		if ($type > 0) {
			$where['type'] = (int)$type;
		}
		if ($siteId > 0) {
			$where['site_id'] = (int)$siteId;
		}
		if ($paymentId > 0) {
			$where['payment_id'] = (int)$paymentId;
		}

		//type 类型列表
		$typeList = service('payment/Payment')->methodList();
		$typeList = array_column($typeList, 'name', 'type');
		//站点列表
		$siteList = service('site/Site')->getListData(['site_id'=>['<>', '0']], 'site_id, name');
		$siteList = array_column($siteList, 'name', 'site_id');
		//账户列表
		$paymentList = service('payment/Payment')->getListData([], 'payment_id, name');
		$paymentList = array_column($paymentList, 'name', 'payment_id');

		$usedService = service('payment/Used');
		$total = $usedService->getCountData($where);
		if ($total > 0) {
			$list = $usedService->getListData($where, '*', $page, $size);
		}

		$this->assign('total', $total);
		$this->assign('list', $list ?? []);
		$this->assign('size', $size);
		$this->assign('typeList', $typeList);
		$this->assign('siteList', $siteList);
		$this->assign('paymentList', $paymentList);
		$this->view();
	}

	protected function getUsedInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$info = service('payment/Used')->loadData($id);
		if (empty($info)) {
			$this->error('获取数据为空');
		}
		$this->addLog('查看支付账户使用-'.$id);
		$siteList = service('site/Site')->getListData(['site_id'=>['<>', '0']], 'site_id, name');
		$siteList = array_column($siteList, 'name', 'site_id');
		$paymentList = service('payment/Payment')->getListData([], 'payment_id, name');
		$paymentMap = array_column($paymentList, 'name', 'payment_id');
		$snapshot = service('log/Payment')->snapshotUsed($info, $siteList, $paymentMap);
		service('log/Payment')->addLog(1, $id, '查看支付账户使用', $snapshot);
		$this->success('获取成功', $info);
	}

	protected function editUsedInfo()
	{
		$id = ipost('item_id');
		$type = (int)ipost('type');
		$siteId = (int)ipost('site_id');
		$paymentId = (int)ipost('payment_id');
		if (empty($type) || empty($siteId) || empty($paymentId)) {
			$this->error('参数不正确');
		}
		$data = [
			'site_id' => $siteId,
			'type' => $type,
			'payment_id' => $paymentId,
		];
		$usedService = service('payment/Used');
		$where = $data;
		if (!empty($id)) {
			$where['item_id'] = ['<>', $id];
		}
		if ($usedService->getCountData($where)) {
			$this->error('配置已存在');
		}

		$siteList = service('site/Site')->getListData(['site_id'=>['<>', '0']], 'site_id, name');
		$siteList = array_column($siteList, 'name', 'site_id');
		$paymentList = service('payment/Payment')->getListData([], 'payment_id, name');
		$paymentMap = array_column($paymentList, 'name', 'payment_id');
		$logService = service('log/Payment');

		if (empty($id)) {
			$result = $usedService->insertGetId($data);
			$msg = '新增支付账户使用-'.$result;
			$detail = $logService->snapshotUsed($data, $siteList, $paymentMap);
			$logItemId = $result;
			$logTitle = '新增支付账户使用';
		} else {
			$oldInfo = $usedService->loadData($id);
			$result = $usedService->updateData($id, $data);
			$msg = '更改支付账户使用-'.$id;
			$detail = $logService->diffUsed($oldInfo ?: [], $data, $siteList, $paymentMap);
			$logItemId = $id;
			$logTitle = '更改支付账户使用';
		}
		if ($result) {
			$this->addLog($msg);
			$logService->addLog(1, $logItemId, $logTitle, $detail);
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	protected function deleteUsedInfo()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$usedService = service('payment/Used');
		$oldInfo = $usedService->loadData($id);
		$result = $usedService->deleteData($id);
		if ($result) {
			$this->addLog('删除支付账户使用-'.$id);
			$siteList = service('site/Site')->getListData(['site_id'=>['<>', '0']], 'site_id, name');
			$siteList = array_column($siteList, 'name', 'site_id');
			$paymentList = service('payment/Payment')->getListData([], 'payment_id, name');
			$paymentMap = array_column($paymentList, 'name', 'payment_id');
			$snapshot = service('log/Payment')->snapshotUsed($oldInfo ?: [], $siteList, $paymentMap);
			service('log/Payment')->addLog(1, $id, '删除支付账户使用', $snapshot);
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	public function log()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();

		$type = iget('type', -1);
		$itemId = (int)iget('item_id', 0);
		$keyword = trim(iget('keyword', ''));
		$stime = trim(iget('stime', ''));
		$etime = trim(iget('etime', ''));
		$page = max(1, (int)iget('page', 1));
		$size = max(1, min(100, (int)iget('size', 20)));

		$where = [];
		if ($type !== '' && (int)$type >= 0) {
			$where['type'] = (int)$type;
		}
		if ($itemId > 0) {
			$where['item_id'] = $itemId;
		}
		if ($keyword !== '') {
			$where['info'] = ['like', '%' . $keyword . '%'];
		}
		$stimeFull = $stime !== '' ? $stime . (strlen($stime) === 10 ? ' 00:00:00' : '') : '';
		$etimeFull = $etime !== '' ? $etime . (strlen($etime) === 10 ? ' 23:59:59' : '') : '';
		if ($stimeFull !== '' && $etimeFull !== '') {
			$where['add_time'] = ['between', [$stimeFull, $etimeFull]];
		} elseif ($stimeFull !== '') {
			$where['add_time'] = ['>=', $stimeFull];
		} elseif ($etimeFull !== '') {
			$where['add_time'] = ['<=', $etimeFull];
		}

		$logService = service('log/Payment');
		$total = $logService->getCountData($where);
		$list = [];
		if ($total > 0) {
			$list = $logService->getListData($where, '*', $page, $size, ['log_id' => 'DESC']);

			// 批量关联操作人信息
			$memIds = array_unique(array_filter(array_column($list, 'mem_id')));
			$memberMap = [];
			if (!empty($memIds)) {
				$memberService = service('member/Member');
				$members = $memberService->getListData(['mem_id' => count($memIds) === 1 ? reset($memIds) : ['in', array_values($memIds)]], 'mem_id,avatar,sex,first_name,last_name,nick_name,mobile', 0, 0);
				foreach ($members as $m) {
					$firstName = $m['first_name'] ?? '';
					$lastName = $m['last_name'] ?? '';
					$m['fullName'] = ($firstName === $lastName) ? $firstName : trim($firstName . ' ' . $lastName);
					$m['avatar'] = $memberService->getAvatar($m['avatar'], $m['sex']);
					$memberMap[$m['mem_id']] = $m;
				}
			}
			foreach ($list as &$item) {
				$mid = (int)$item['mem_id'];
				$item['member'] = $memberMap[$mid] ?? [
					'mem_id'    => $mid,
					'avatar'    => isMobile() ? '' : siteUrl('/image/common/male.jpg'),
					'nick_name' => $mid > 0 ? '用户-' . $mid : '系统/未登录',
					'fullName'  => '',
					'mobile'    => '-',
				];
			}
			unset($item);
		}

		// 字典数据
		$paymentService = service('payment/Payment');
		$typeList = $paymentService->getTypeList();
		$siteList = service('site/Site')->getListData(['site_id' => ['<>', '0']], 'site_id, name');
		$siteList = array_column($siteList, 'name', 'site_id');
		$paymentList = $paymentService->getListData([], 'payment_id, name');
		$paymentMap = array_column($paymentList, 'name', 'payment_id');

		$this->assign('total', $total);
		$this->assign('list', $list);
		$this->assign('typeList', $typeList);
		$this->assign('siteList', $siteList);
		$this->assign('paymentMap', $paymentMap);
		$this->assign('search', [
			'type'    => $type,
			'item_id' => $itemId > 0 ? $itemId : '',
			'keyword' => $keyword,
			'stime'   => $stime,
			'etime'   => $etime,
			'page'    => $page,
			'size'    => $size,
		]);
		$this->view();
	}
}