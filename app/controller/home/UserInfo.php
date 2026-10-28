<?php

namespace app\controller\home;
use app\controller\HomeBase;

class UserInfo extends HomeBase
{
	public function index()
	{
		frame('Html')->addCss('common/member');
		frame('Html')->addCss();
		frame('Html')->addJs();

		$order = service('order/Order');
		if ($this->isLogin()) {
			$info = session()->get(type().'_info');
			$info['name'] = trim(($info['first_name'] ?? '').' '.($info['last_name'] ?? ''));
			$temp = explode(' ', (string)($info['mobile'] ?? ''), 2);
			$info['dialing_code'] = $temp[0] ?: '+1';
			$info['phone'] = $temp[1] ?? '';

			$where = [
				'mem_id' => $info['mem_id'],
			];
			// 收藏统计
			$collectionTotal = service('member/Collect')->getCountData($where);
			// 足迹统计
			$historyTotal = service('member/History')->getCountData($where);
			// 地址统计
			$addressTotal = service('member/Address')->getCountData($where);
			// 订单统计
			$orderWhere = $where;
			$orderWhere['status'] = ['in', [
				$order->getConst('STATUS_WAIT_PAY'),
				$order->getConst('STATUS_PAIED'),
				$order->getConst('STATUS_SHIPPED'),
				$order->getConst('STATUS_FINISHED'),
				$order->getConst('STATUS_PART_REFUND'),
				$order->getConst('STATUS_FULL_REFUND'),
			]];
			$orderWhere['is_review'] = 0;
			$orderWhere['is_delete'] = 0;
			$orderTotal = $order->where($orderWhere)->field('count(*) as count, status')->groupBy('status')->get();
			$orderTotal = array_column($orderTotal, 'count', 'status');

			$recentOrders = $order->getList(['mem_id' => $info['mem_id'], 'is_delete' => 0], 1, 3);
			$this->assign('info', $info);
			$this->assign('recentOrders', $recentOrders);
		}
		$countryList = service('system/Country')->getListData(['status' => 1], 'code2,name_en,dialing_code', 0, 0, ['sort' => 'asc']);
		$this->assign('countryList', $countryList);
		$this->assign('collectionTotal', $collectionTotal ?? 0);
		$this->assign('historyTotal', $historyTotal ?? 0);
		$this->assign('addressTotal', $addressTotal ?? 0);
		$this->assign('orderTotal', $orderTotal ?? []);
		$this->assign('_title', distT('my_info'));
		$this->view();
	}

	public function getInfo()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$list = service('system/Country')->getListData(['status'=>1], 'dialing_code', 0, 0, ['sort'=>'asc']);
		$list = array_values(array_unique(array_column($list, 'dialing_code')));
		$this->success($list);
	}

	public function editInfo()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$first_name = trim((string)ipost('first_name'));
		$last_name = trim((string)ipost('last_name'));
		$dialing_code = trim((string)ipost('dialing_code', '+1'));
		$phone = trim((string)ipost('phone'));
		if (empty($first_name)) {
			$this->error('First Name is required');
		}
		if (empty($last_name)) {
			$this->error('Last Name is required');
		}
		if (empty($dialing_code) || empty($phone)) {
			$this->error('Phone Number is required');
		}
		if (strpos($dialing_code, '+') !== 0) {
			$dialing_code = '+' . $dialing_code;
		}
		$data = [
			'first_name' => substr($first_name, 0, 32),
			'last_name'  => substr($last_name, 0, 32),
			'mobile'     => substr($dialing_code.' '.$phone, 0, 20),
		];
		$rst = service('member/Member')->updateData(userId(), $data);
		if ($rst !== false) {
			$info = session()->get(type().'_info') ?: [];
			session()->set(type().'_info', array_merge($info, $data));
			$this->success('Profile updated successfully.');
		} else {
			$this->error('Failed to update profile.');
		}
	}

	public function updateAvatar()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$attach_id = ipost('attach_id');
		if (empty($attach_id)) {
			$this->error('Param error');
		}
		$info = service('attachment/Attachment')->getAttachmentById($attach_id);
		if (empty($info)) {
			$this->error('File does not exist.');
		}
		$avatar = $info['cate'].DS.$info['name'].'.'.$info['type'];
		$rst = service('member/Member')->updateData(userId(), ['avatar'=>$avatar]);
		if ($rst !== false) {
			session()->set(type().'_info', $info['url'], 'avatar');
		}
		$this->success('Avatar updated successfully.');
	}

	public function wish()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$spuId = (int)ipost('spu_id', 0);
		if (empty($spuId)) {
			$this->error('Invalid product parameter.');
		}
		$rst = service('member/Collect')->collectProduct($spuId);
		if ($rst) {
			$this->success($rst, $rst == 1 ? distT('add_wish') : distT('remove_wish'));
		} else {
			$this->error(distT('wish_error'));
		}
	}

	public function address()
	{
		frame('Html')->addCss('common/member');
		frame('Html')->addCss();
		frame('Html')->addCss('common/address');
		frame('Html')->addJs();
		frame('Html')->addJs('common/address');
		$page = (int)iget('page', 1);
		$size = (int)iget('size', 10);
		$list = [];
		if ($this->isLogin()) {
			$list = $this->getAddressList($page, $size);
		}
		$country = service('system/Country')->getListData(['status' => 1], 'code2,name_en,dialing_code', 0, 0, ['sort' => 'asc']);
		$this->assign('country', $country);
		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->assign('size', $size);
		$this->assign('_title', distT('my_address'));
		$this->view();
	}

	protected function getAddressList($page=1, $size=10)
	{
		$memId = userId();
		if (!$memId) return [];
		$list = service('member/Address')->getListData(['mem_id'=>$memId], '*', $page, $size, ['is_default'=>'desc', 'is_bill'=>'desc', 'address_id'=>'desc']);
		if (!empty($list)) {
			$countryCodes = array_unique(array_column($list, 'country_code2'));
			$countryList = service('system/Country')->getListData(['code2'=>['in', $countryCodes]], 'code2,name_en');
			$countryList = array_column($countryList, 'name_en', 'code2');
			foreach ($list as $key => $value) {
				$value['country'] = $countryList[$value['country_code2']] ?? $value['country_code2'];
				$list[$key] = $value;
			}
		}
		return $list;
	}

	public function setAddressDefault()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$id = (int)ipost('id');
		$memId = userId();
		$where = [
			'address_id' => $id,
			'mem_id' => $memId,
		];
		$service = service('member/Address');
		if (!$service->getCountData($where)) {
			$this->error('That address does not exist!');
		}
		$service->updateData(['mem_id'=>$memId], ['is_default'=>0]);
		$service->updateData($id, ['is_default'=>1]);
		$this->success('Default shipping address updated.');
	}

	public function setAddressBillDefault()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$id = (int)ipost('id');
		$memId = userId();
		$where = [
			'address_id' => $id,
			'mem_id' => $memId,
		];
		$service = service('member/Address');
		if (!$service->getCountData($where)) {
			$this->error('That address does not exist!');
		}
		$service->updateData(['mem_id'=>$memId], ['is_bill'=>0]);
		$service->updateData($id, ['is_bill'=>1]);
		$this->success('Default billing address updated.');
	}

	public function deleteAddress()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$id = (int)ipost('id');
		$memId = userId();
		$where = [
			'address_id' => $id,
			'mem_id' => $memId,
		];
		$service = service('member/Address');
		if (!$service->getCountData($where)) {
			$this->error('That address does not exist!');
		}
		$service->deleteData($id);
		$this->success('Address deleted.');
	}

	public function getAddress()
	{
		if ($this->isLogin()) {
			$page = (int)ipost('page', 1);
			$size = (int)ipost('size', 10);
			$list = $this->getAddressList($page, $size);
		} else {
			$list = session()->get(type().'_info.address') ?? [];
		}
		$this->success($list);
	}

	public function getAddressInfo()
	{
		$id = ipost('id');
		$info = [];
		if ($this->isLogin()) {
			$info = service('member/Address')->getInfo($id);
		} else {
			$list = session()->get(type().'_info.address') ?? [];
			foreach ($list as $value) {
				if (($value['address_id'] ?? 0) == $id) {
					$info = $value;
					break;
				}
			}
		}
		if (empty($info)) {
			$this->error('Sorry, that address does not exist!');
		}
		$this->success($info);
	}

	public function editAddress()
	{
		$address_id = ipost('address_id');
		$country_code2 = trim((string)ipost('country_code2', 'US'));
		$company_name = trim((string)ipost('company_name', ''));
		$tax_number = trim((string)ipost('tax_number', ''));
		$first_name = trim((string)ipost('first_name', ''));
		$last_name = trim((string)ipost('last_name', ''));
		$phone = trim((string)ipost('phone', ''));
		$postcode = trim((string)ipost('postcode', ''));
		$city = trim((string)ipost('city', ''));
		$state = trim((string)ipost('state', ''));
		$address1 = trim((string)ipost('address1', ''));
		$address2 = trim((string)ipost('address2', ''));
		$is_default = (int)ipost('is_default', 0);
		$is_bill = (int)ipost('is_bill', 0);
		if (empty($country_code2)) {
			$this->error('Country is required');
		}
		if (empty($first_name)) {
			$this->error('First Name is required');
		}
		if (empty($phone)) {
			$this->error('Phone Number is required');
		}
		if (empty($postcode)) {
			$this->error('ZIP / Postal Code is required');
		}
		if (empty($city)) {
			$this->error('City is required');
		}
		if (empty($state)) {
			$this->error('State / Province is required');
		}
		if (empty($address1)) {
			$this->error('Address Line 1 is required');
		}
		$data = [
			'country_code2' => substr($country_code2, 0, 2),
			'company_name'  => substr($company_name, 0, 32),
			'tax_number'    => substr($tax_number, 0, 32),
			'first_name'    => substr($first_name, 0, 32),
			'last_name'     => substr($last_name, 0, 32),
			'postcode'      => substr($postcode, 0, 10),
			'city'          => substr($city, 0, 32),
			'state'         => substr($state, 0, 32),
			'address1'      => substr($address1, 0, 64),
			'address2'      => substr($address2, 0, 64),
			'is_default'    => $is_default == 1 ? 1 : 0,
			'is_bill'       => $is_bill == 1 ? 1 : 0,
		];
		$countryInfo = service('system/Country')->loadData(['code2' => $country_code2], 'dialing_code,name_en');
		$dialingCode = $countryInfo['dialing_code'] ?? '1';
		$cleanPhone = preg_replace('/^\+\d+\s*/', '', $phone);
		$data['phone'] = substr('+' . $dialingCode . ' ' . $cleanPhone, 0, 20);

		if (empty(userId())) {
			$addressData = session()->get(type().'_info.address') ?? [];
			$data['country'] = $countryInfo['name_en'] ?? $country_code2;
			if (!empty($address_id)) {
				foreach ($addressData as $k => $v) {
					if (($v['address_id'] ?? '') == $address_id) {
						$data['address_id'] = $address_id;
						$addressData[$k] = $data;
						break;
					}
				}
			} else {
				$data['address_id'] = randString(8, false, false, true);
				$addressData[] = $data;
			}
			$rst = session()->set(type().'_info.address', $addressData);
		} else {
			$addrService = service('member/Address');
			if ($data['is_default'] == 1) {
				$addrService->updateData(['mem_id' => userId()], ['is_default' => 0]);
			}
			if ($data['is_bill'] == 1) {
				$addrService->updateData(['mem_id' => userId()], ['is_bill' => 0]);
			}
			if (empty($address_id)) {
				$data['mem_id'] = userId();
				$data['add_time'] = now();
				$rst = $addrService->insert($data);
			} else {
				$data['update_time'] = now();
				$rst = $addrService->updateData(['address_id'=>$address_id, 'mem_id'=>userId()], $data);
			}
		}
		if ($rst !== false) {
			$this->success($address_id ? 'Address updated successfully.' : 'Address added successfully.');
		} else {
			$this->error($address_id ? 'Failed to update address.' : 'Failed to add address.');
		}
	}

	public function wishList()
	{
		frame('Html')->addCss('common/member');
		frame('Html')->addCss();
		frame('Html')->addCss('common/productList');
		frame('Html')->addJs();
		$list = [];
		if ($this->isLogin()) {
			$page = (int)iget('page', 1);
			$size = (int)iget('size', 12);
			$list = $this->getWishList($page, $size);
		}
		$this->assign('list', $list);
		$this->assign('_title', distT('my_wish'));
		$this->view();
	}

	protected function getWishList($page=1, $size=12)
	{
		$memId = userId();
		if (!$memId) return [];
		$list = service('member/Collect')->getListData(['mem_id'=>$memId], '*', $page, $size, ['coll_id'=>'desc']);
		if (!empty($list)) {
			$spuIdArr = array_unique(array_column($list, 'spu_id'));
			$spuArr = service('product/Spu')->getListById($spuIdArr);
			$spuArr = array_column($spuArr, null, 'spu_id');
			$merged = [];
			foreach ($list as $value) {
				if (!empty($spuArr[$value['spu_id']])) {
					$merged[] = array_merge($value, $spuArr[$value['spu_id']]);
				}
			}
			$list = $merged;
		}
		return $list;
	}

	public function history()
	{
		frame('Html')->addCss('common/member');
		frame('Html')->addCss();
		frame('Html')->addCss('common/productList');
		frame('Html')->addJs();

		$page = (int)iget('page', 1);
		$size = (int)iget('size', 12);

		$list = $this->getHistoryList($page, $size);

		$this->assign('list', $list);
		$this->assign('_title', appT('my_history'));
		$this->view();
	}

	protected function getHistoryList($page=1, $size=12)
	{
		$memId = userId();
		if (!$memId) return [];
		$list = service('member/History')->getListData(['mem_id'=>$memId], '*', $page, $size, ['his_id'=>'desc']);
		if (!empty($list)) {
			$spuIdArr = array_unique(array_column($list, 'spu_id'));
			//获取收藏ID
			$where = [
				'mem_id' => $memId,
				'spu_id' => ['in', $spuIdArr],
			];
			$collSpuList = service('member/Collect')->getListData($where, 'spu_id');
			$collSpuList = array_column($collSpuList, 'spu_id');

			$spuArr = service('product/Spu')->getListById($spuIdArr);
			$spuArr = array_column($spuArr, null, 'spu_id');
			$tempArr = [];
			//按日期分组
			foreach ($list as $value) {
				if (empty($spuArr[$value['spu_id']])) {
					continue;
				}
				$value['is_liked'] = in_array($value['spu_id'], $collSpuList) ? 1 : 0;
				$tempArr[$value['add_date']][] = array_merge($value, $spuArr[$value['spu_id']]);
			}
			$list = $tempArr;
		}
		return $list;
	}

	public function deleteHistory()
	{
		$id = (int)ipost('id');
		if (empty($id)) {
			$this->error('History item ID is required.');
		}
		$where = [
			'mem_id' => userId(),
			'his_id' => $id,
		];
		$rst = service('member/History')->deleteData($where);
		if ($rst) {
			$this->success('History item removed.');
		} else {
			$this->error('Failed to remove history item.');
		}
	}

	public function coupon()
	{
		frame('Html')->addCss('common/member');
		frame('Html')->addCss();
		frame('Html')->addJs();
		$this->assign('coupons', []);
		$this->assign('_title', distT('my_coupon'));
		$this->view();
	}
}
