<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Order extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '订单列表',
		];
		$this->_default = '订单管理';
		$this->_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getInfo', 'modifyStatus'], true)) {
				$this->$opn();
			}
			$this->error('非法请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();

		$page = max(1, (int)iget('page', 1));
		$size = max(1, min(100, (int)iget('size', 20)));
		$siteId = (int)iget('site_id', 0);
		$statusRaw = trim((string)iget('status', ''));
		$status = ($statusRaw === '' || $statusRaw === '-99') ? -99 : (int)$statusRaw;
		$paymentId = (int)iget('payment_id', 0);
		$orderId = trim((string)iget('order_id', ''));
		$email = trim((string)iget('email', ''));
		$stime = trim((string)iget('stime', ''));
		$etime = trim((string)iget('etime', ''));

		$where = [];
		if ($siteId > 0) {
			$where['site_id'] = $siteId;
		}
		if ($status >= -1) {
			$where['status'] = $status;
		}
		if ($paymentId > 0) {
			$where['payment_id'] = $paymentId;
		}
		if ($orderId !== '') {
			$where['order_id'] = (int)$orderId;
		}
		if ($email !== '') {
			$memList = service('member/Member')->getListData(['email' => ['like', '%' . $email . '%']], 'mem_id');
			if (empty($memList)) {
				$where = ['order_id' => 0];
			} else {
				$where['mem_id'] = ['in', array_column($memList, 'mem_id')];
			}
		}
		$stimeFull = $stime !== '' ? ($stime . (strlen($stime) === 10 ? ' 00:00:00' : '')) : '';
		$etimeFull = $etime !== '' ? ($etime . (strlen($etime) === 10 ? ' 23:59:59' : '')) : '';
		if ($stimeFull !== '' && $etimeFull !== '') {
			$where['add_time'] = ['between', [$stimeFull, $etimeFull]];
		} elseif ($stimeFull !== '') {
			$where['add_time'] = ['>=', $stimeFull];
		} elseif ($etimeFull !== '') {
			$where['add_time'] = ['<=', $etimeFull];
		}

		$siteList = service('site/Site')->getListData([], 'site_id,name');
		$siteList = array_column($siteList, 'name', 'site_id');

		// 状态列表
		$orderService = service('order/Order');
		$statusList = $orderService->orderStatus();

		// 支付账号
		$paymentAccount = service('payment/Payment')->getList([], 0);
		$paymentAccount = array_map(function($value){
			$value['name'] = $value['type_name'] . ' - ' . $value['name'];
			return $value;
		}, $paymentAccount);
		$paymentAccount = array_column($paymentAccount, 'name', 'payment_id');

		$total = $orderService->getCountData($where);
		$list = [];
		if ($total > 0) {
			$list = $orderService->getListData($where, '*', $page, $size, ['order_id' => 'desc']);
			$memIds = array_values(array_unique(array_filter(array_column($list, 'mem_id'))));
			$memMap = [];
			if (!empty($memIds)) {
				$members = service('member/Member')->getListData(['mem_id' => ['in', $memIds]], 'mem_id,first_name,last_name,nick_name,email,mobile');
				$memMap = array_column($members, null, 'mem_id');
			}
			foreach ($list as $k => $row) {
				$m = $memMap[$row['mem_id']] ?? [];
				$fullName = trim(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? ''));
				if ($fullName === '' && !empty($m['nick_name'])) {
					$fullName = $m['nick_name'];
				}
				$row['member_name'] = $fullName;
				$row['member_email'] = $m['email'] ?? '';
				$row['member_mobile'] = $m['mobile'] ?? '';
				$row['site_name'] = $siteList[$row['site_id']] ?? ('站点#' . $row['site_id']);
				$row['payment_name'] = $paymentAccount[$row['payment_id']] ?? ($row['payment_id'] ? ('支付#' . $row['payment_id']) : '未选择');
				$row['status_name'] = $statusList[$row['status']] ?? '未知';
				$list[$k] = $row;
			}
		}

		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list);
		$this->assign('site_id', $siteId);
		$this->assign('status', $status);
		$this->assign('payment_id', $paymentId);
		$this->assign('order_id', $orderId);
		$this->assign('email', $email);
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);
		$this->assign('siteList', $siteList);
		$this->assign('statusList', $statusList);
		$this->assign('paymentAccount', $paymentAccount);
		$this->view();
	}

	protected function getInfo()
	{
		$orderId = (int)ipost('order_id');
		if ($orderId <= 0) {
			$this->error('订单ID不正确');
		}
		$orderService = service('order/Order');
		$base = $orderService->loadData($orderId);
		if (empty($base)) {
			$this->error('未找到该订单信息');
		}
		$statusList = $orderService->orderStatus();
		$base['status_name'] = $statusList[$base['status']] ?? '未知';

		$addresses = service('order/Address')->getListData(['order_id' => $orderId]);
		$addresses = array_column($addresses, null, 'type');

		$products = service('order/Product')->getListData(['order_id' => $orderId]);
		if (!empty($products)) {
			$opIds = array_column($products, 'order_product_id');
			$attrs = service('order/ProductAttribute')->getListData(['order_product_id' => ['in', $opIds]]);
			$attrMap = [];
			foreach ($attrs as $at) {
				$attrMap[$at['order_product_id']][] = $at['attr_name'] . ': ' . $at['attv_name'];
			}
			foreach ($products as $k => $p) {
				$products[$k]['attr_text'] = isset($attrMap[$p['order_product_id']]) ? implode(' / ', $attrMap[$p['order_product_id']]) : '';
			}
		}

		$history = service('order/StatusHistory')->getListData(['order_id' => $orderId], '*', 0, 0, ['item_id' => 'desc']);

		$this->success('获取成功', [
			'base'             => $base,
			'shipping_address' => $addresses[0] ?? [],
			'billing_address'  => $addresses[1] ?? [],
			'products'         => $products,
			'history'          => $history,
		]);
	}

	protected function modifyStatus()
	{
		$orderId = (int)ipost('order_id');
		$status = (int)ipost('status', -99);
		$orderService = service('order/Order');
		$statusList = $orderService->orderStatus();
		if ($orderId <= 0 || !isset($statusList[$status])) {
			$this->error('参数不正确');
		}
		$info = $orderService->loadData($orderId);
		if (empty($info)) {
			$this->error('订单不存在');
		}
		if ((int)$info['status'] === $status) {
			$this->success('状态未变更');
		}
		$rst = $orderService->updateData($orderId, ['status' => $status]);
		if ($rst) {
			$this->addLog('修改订单状态 #' . $orderId . ' -> ' . $statusList[$status]);
			service('log/Order')->add('修改订单状态为: ' . $statusList[$status], $orderId);
			$this->success('订单状态更新成功');
		}
		$this->error('状态更新失败');
	}
}