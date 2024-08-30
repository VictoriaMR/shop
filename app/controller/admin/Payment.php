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
		];
		$this->_default = '支付管理';
		parent::_init();
	}

	public function index()
	{	
		if (frame('Request')->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'editInfo', 'deleteInfo', 'modifyInfo'])) {
				$this->$opn();
			}
			$this->error('未知请求');
		}

		frame('Html')->addJs();
		$type = iget('type');
		$isSandbox = iget('is_sandbox', -1);
		$name = iget('name');
		$page = iget('page', 1);
		$size = iget('size', 20);

		$paymentService = service('payment/Payment');
		$where = [];
		if (!empty($type)) {
			$where['type'] = (int) $type;
		}
		if ($isSandbox >= 0) {
			$where['is_sandbox'] = (int) $isSandbox;
		}
		if (!empty($name)) {
			$where['is_sandbox'] = ['like', '%'.trim($name).'%'];
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
		$id = ipost('id');
		if (empty($id)) {
			$this->error('参数不正确');
		}
		$info = service('payment/Payment')->loadData($id);
		if (empty($info)) {
			$this->error('找不到数据');
		}
		$this->addLog('查看支付账户配置-'.$id);
		$this->success($info, '');
	}

	protected function editInfo()
	{
		$id = ipost('payment_id');
		$type = ipost('type');
		$status = (int)ipost('status', 0);
		$isSandbox = (int)ipost('is_sandbox', 0);
		$name = trim(ipost('name'));
		$appKey = trim(ipost('app_key'));
		$secretKey = trim(ipost('secret_key'));
		$webhookKey = trim(ipost('webhook_key'));
		$remark = trim(ipost('remark'));
		if (empty($name)) {
			$this->error('账户名称不能未空');
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
		if (empty($id)) {
			$result = $paymentService->insertGetId($data);
			$msg = '新增支付账户-'.$result;
		} else {
			$result = $paymentService->updateData($id, $data);
			$msg = '修改支付账户-'.$id;
		}
		if ($result) {
			$this->addLog($msg);
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
		$result = service('payment/Payment')->deleteData($id);
		if ($result) {
			service('payment/Used')->deleteData(['payment_id'=>$id]);
			$this->addLog('删除支付账户配置-'.$id);
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
		$result = service('payment/Payment')->updateData($id, $data);
		if ($result) {
			$this->addLog('更改支付账户'.$type.'-'.$id);
			$this->success('设置成功');
		}
		$this->error('设置失败');
	}

	public function paymentUsed()
	{
		if (frame('Request')->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getUsedInfo', 'editUsedInfo', 'deleteUsedInfo'])) {
				$this->$opn();
			}
			$this->error('未知请求');
		}
		
		frame('Html')->addJs();
		$type = iget('type', -1);
		$siteId = iget('site_id', -1);
		$paymentId = iget('payment_id', -1);
		$page = iget('page', 1);
		$size = iget('size', 20);
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
		$typeList = make('app/payment/PaymentMethod')::methodList();
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
		$this->_init();
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
		$this->success($info, '');
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
		if (empty($id)) {
			$result = $usedService->insertGetId($data);
			$msg = '新增支付账户使用-'.$result;
		} else {
			$result = $usedService->updateData($id, $data);
			$msg = '更改支付账户使用-'.$id;
		}
		if ($result) {
			$this->addLog($msg);
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
		$result = service('payment/Used')->deleteData($id);
		if ($result) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}
}