<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Order extends HomeBase
{
	public function index()
	{
		$status = iget('status', '');
		$page = (int)iget('page', 1);
		$size = (int)iget('size', 10);

		frame('Html')->addCss('common/member');
		frame('Html')->addCss();
		frame('Html')->addJs();

		if (userId()) {
			$where = ['mem_id'=>userId(), 'is_delete'=>0];
			if ($status !== '' && $status !== null) {
				$where['status'] = (int)$status;
			}
			$list = service('order/Order')->getList($where, $page, $size);
		}

		$this->assign('list', $list ?? []);
		$this->assign('status', $status);
		$this->assign('page', $page);
		$this->assign('size', $size);
		$this->assign('_title', appT('my_order'));
		$this->view();
	}

	public function getOrderListAjax()
	{
		$status = ipost('status', '');
		$page = (int)ipost('page', 1);
		$size = (int)ipost('size', 10);
		$isDelete = (int)ipost('is_delete', 0);
		if (userId()) {
			$where = ['mem_id'=>userId(), 'is_delete'=>$isDelete];
			if ($status !== '' && $status !== null) {
				$where['status'] = (int)$status;
			}
			$list = service('order/Order')->getList($where, $page, $size);
		}
		$this->success($list ?? [], '');
	}

	public function repurchase()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('order_error'));
		}
		if (!service('order/Order')->getCountData(['mem_id'=>userId(), 'order_id'=>$id])) {
			$this->error(appT('order_error'));
		}
		$list = service('order/Product')->getListData(['order_id'=>$id], 'sku_id,quantity');
		$cartService = service('cart/Cart');
		foreach ($list as $value) {
			$cartService->addToCart($value['sku_id'], $value['quantity']);
		}
		$this->success('Items added to your shopping bag.', ['url'=>url('cart')]);
	}

	public function delete()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('order_error'));
		}
		$orderService = service('order/Order');
		if (!$orderService->getCountData(['mem_id'=>userId(), 'order_id'=>$id])) {
			$this->error(appT('order_error'));
		}
		$rst = $orderService->updateData($id, ['is_delete'=>1]);
		if ($rst !== false) {
			$this->success('Order moved to trash.');
		} else {
			$this->error('Failed to delete order.');
		}
	}

	public function restore()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('order_error'));
		}
		$orderService = service('order/Order');
		if (!$orderService->getCountData(['mem_id'=>userId(), 'order_id'=>$id])) {
			$this->error(appT('order_error'));
		}
		$rst = $orderService->updateData($id, ['is_delete'=>0]);
		if ($rst !== false) {
			$this->success('Order restored.');
		} else {
			$this->error('Failed to restore order.');
		}
	}

	public function refund()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('order_error'));
		}
		$orderService = service('order/Order');
		$info = $orderService->loadData(['mem_id'=>userId(), 'order_id'=>$id, 'status'=>$orderService->getConst('STATUS_PAIED')]);
		if (empty($info)) {
			$this->error(appT('order_error'));
		}
		$status = $orderService->getConst('STATUS_REFUNDING');
		$rst = $orderService->updateData($id, ['status'=>$status]);
		if ($rst !== false) {
			service('order/StatusHistory')->addLog($id, $status, $info['lan_id']);
			$this->success('Refund request submitted.');
		} else {
			$this->error('Failed to submit refund request.');
		}
	}

	public function complete()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('order_error'));
		}
		$orderService = service('order/Order');
		$info = $orderService->loadData(['mem_id'=>userId(), 'order_id'=>$id, 'status'=>$orderService->getConst('STATUS_SHIPPED')]);
		if (empty($info)) {
			$this->error(appT('order_error'));
		}
		$status = $orderService->getConst('STATUS_FINISHED');
		$rst = $orderService->updateData($id, ['status'=>$status]);
		if ($rst !== false) {
			service('order/StatusHistory')->addLog($id, $status, $info['lan_id']);
			$this->success('Order marked as received.');
		} else {
			$this->error('Failed to update order status.');
		}
	}

	public function cancel()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error(appT('order_error'));
		}
		$orderService = service('order/Order');
		$info = $orderService->loadData(['mem_id'=>userId(), 'order_id'=>$id, 'status'=>$orderService->getConst('STATUS_WAIT_PAY')]);
		if (empty($info)) {
			$this->error(appT('order_error'));
		}
		$status = $orderService->getConst('STATUS_CANCEL');
		$rst = $orderService->updateData($id, ['status'=>$status]);
		if ($rst !== false) {
			service('order/StatusHistory')->addLog($id, $status, $info['lan_id']);
			$this->success('Order cancelled.');
		} else {
			$this->error('Failed to cancel order.');
		}
	}

	public function getOrderAddress()
	{
		$orderId = (int)ipost('order_id');
		if (empty($orderId)) {
			$this->error('Order parameter is missing.');
		}
		if (!service('order/Order')->getCountData(['mem_id'=>userId(), 'order_id'=>$orderId])) {
			$this->error('Order not found.');
		}
		$info = service('order/Address')->loadData(['order_id'=>$orderId, 'type'=>1]);
		$this->success($info, 'success');
	}

	public function editOrderAddress()
	{
		$orderId = (int)ipost('order_id');
		$country_code2 = trim((string)ipost('country_code2'));
		$tax_number = trim((string)ipost('tax_number'));
		$first_name = trim((string)ipost('first_name'));
		$last_name = trim((string)ipost('last_name'));
		$phone = trim((string)ipost('phone'));
		$postcode = trim((string)ipost('postcode'));
		$city = trim((string)ipost('city'));
		$state = trim((string)ipost('state'));
		$address1 = trim((string)ipost('address1'));
		$address2 = trim((string)ipost('address2'));
		if (empty($orderId)) {
			$this->error('Order ID is required.');
		}
		if (empty($country_code2) || empty($first_name) || empty($phone) || empty($postcode) || empty($city) || empty($state) || empty($address1)) {
			$this->error('Please complete all required address fields.');
		}
		if (!service('order/Order')->getCountData(['mem_id'=>userId(), 'order_id'=>$orderId])) {
			$this->error('Order not found.');
		}
		$countryInfo = service('system/Country')->loadData(['code2' => $country_code2], 'dialing_code,name_en');
		$dialingCode = $countryInfo['dialing_code'] ?? '1';
		$cleanPhone = preg_replace('/^\+\d+\s*/', '', $phone);
		$data = [
			'country_code2' => substr($country_code2, 0, 2),
			'country'       => $countryInfo['name_en'] ?? $country_code2,
			'tax_number'    => substr($tax_number, 0, 32),
			'first_name'    => substr($first_name, 0, 32),
			'last_name'     => substr($last_name, 0, 32),
			'postcode'      => substr($postcode, 0, 10),
			'city'          => substr($city, 0, 32),
			'state'         => substr($state, 0, 32),
			'address1'      => substr($address1, 0, 64),
			'address2'      => substr($address2, 0, 64),
			'phone'         => substr('+'.$dialingCode.' '.$cleanPhone, 0, 20),
		];

		$rst = service('order/Address')->updateData(['order_id'=>$orderId, 'type'=>1], $data);
		if ($rst !== false) {
			$this->success('Billing address updated successfully.');
		} else {
			$this->error('Failed to update billing address.');
		}
	}

	public function search()
	{
		frame('Html')->addCss('common/member');
		frame('Html')->addCss('order/index');
		frame('Html')->addCss();
		frame('Html')->addJs('order/index');
		frame('Html')->addJs();

		$page = (int)iget('page', 1);
		$size = (int)iget('size', 10);
		$keyword = trim((string)iget('keyword'));

		if (userId()) {
			$where = ['mem_id'=>userId(), 'is_delete'=>0];
			$list = service('order/Order')->getListByKeyword($where, $keyword, $page, $size);
		}

		$this->assign('page', $page);
		$this->assign('size', $size);
		$this->assign('keyword', $keyword);
		$this->assign('list', $list ?? []);
		$this->assign('_title', appT('order_search'));
		$this->view();
	}

	public function getSearchOrderListAjax()
	{
		$page = (int)iget('page', 1);
		$size = (int)iget('size', 10);
		$keyword = trim((string)iget('keyword'));

		if (!empty($keyword) && userId()) {
			$where = ['mem_id'=>userId(), 'is_delete'=>0];
			$list = service('order/Order')->getListByKeyword($where, $keyword, $page, $size);
		}
		$this->success($list ?? [], '');
	}

	public function trash()
	{
		frame('Html')->addCss('common/member');
		frame('Html')->addCss('order/index');
		frame('Html')->addCss();
		frame('Html')->addJs('order/index');
		frame('Html')->addJs();

		$page = (int)iget('page', 1);
		$size = (int)iget('size', 10);

		if (userId()) {
			$where = ['mem_id'=>userId(), 'is_delete'=>1];
			$list = service('order/Order')->getList($where, $page, $size);
		}

		$this->assign('page', $page);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->assign('_title', appT('order_trash'));
		$this->view();
	}

	public function detail()
	{
		frame('Html')->addCss('common/member');
		frame('Html')->addCss();
		frame('Html')->addJs();

		$id = (int)iget('id');
		$orderInfo = [];
		$error = '';
		if (empty($id)) {
			$error = appT('order_error');
		} else {
			$orderService = service('order/Order');
			$orderInfo = $orderService->getInfo($id);
			if (empty($orderInfo)) {
				$error = appT('order_error');
			} else {
				//检查订单状态
				if ($orderInfo['base']['status'] == $orderService->getConst('STATUS_WAIT_PAY')) {
					if ($orderInfo['base']['add_time'] < now(time()-$orderService->getConst('ORDER_WAIT_PAY_TIME'))) {
						//取消订单
						$status = $orderService->getConst('STATUS_CANCEL');
						$rst = $orderService->updateData($id, ['status'=>$status]);
						if ($rst !== false) {
							service('order/StatusHistory')->addLog($id, $status, $orderInfo['base']['lan_id']);
						}
						$orderInfo = $orderService->getInfo($id);
					}
					$orderInfo['base']['last_pay_time'] = max(0, (int)((strtotime($orderInfo['base']['add_time']) + $orderService->getConst('ORDER_WAIT_PAY_TIME') - time()) / 86400));
				}
			}
		}
		$this->assign('error', $error);
		$this->assign('orderInfo', $orderInfo);
		$this->assign('_title', appT('order_detail'));
		$this->view();
	}
}
