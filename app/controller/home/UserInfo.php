<?php

namespace app\controller\home;
use app\controller\HomeBase;

class UserInfo extends HomeBase
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();

		if ($this->isLogin()) {
			$info = session()->get(APP_TEMPLATE_TYPE.'_info');
			$info['name'] = trim($info['first_name'].' '.$info['last_name']);
			$temp = explode(' ', $info['mobile']);
			$info['dialing_code'] = $temp[0];
			$info['phone'] = $temp[1] ?? '';

			$where = [
				'mem_id' => $info['mem_id'],
			];
			// 收藏统计
			$collectionTotal = make('app/service/member/Collect')->getCountData($where);
			// 足迹统计
			$historyTotal = make('app/service/member/History')->getCountData($where);
			// 地址统计
			$addressTotal = make('app/service/member/Address')->getCountData($where);
			// 订单统计
			$order = make('app/service/order/Order');
			$where['status'] = ['in', [
				$order->getConst('STATUS_WAIT_PAY'),
				$order->getConst('STATUS_PAIED'),
				$order->getConst('STATUS_SHIPPED'),
				$order->getConst('STATUS_FINISHED'),
				$order->getConst('STATUS_PART_REFUND'),
				$order->getConst('STATUS_FULL_REFUND'),
			]];
			$where['is_review'] = 0;
			$where['is_delete'] = 0;
			$orderTotal = $order->where($where)->field('count(*) as count, status')->groupBy('status')->get();
			$orderTotal = array_column($orderTotal, 'count', 'status');
			$this->assign('info', $info);
		}
		$this->assign('collectionTotal', $collectionTotal ?? 0);
		$this->assign('historyTotal', $historyTotal ?? 0);
		$this->assign('addressTotal', $addressTotal ?? 0);
		$this->assign('orderTotal', $orderTotal ?? 0);
		$this->assign('_title', distT('my_info'));
		$this->view();
	}

	public function getInfo()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$list = make('app/service/address/Country')->getListData(['status'=>1], 'dialing_code', 0, 0, ['sort'=>'asc']);
		$list = array_values(array_unique(array_column($list, 'dialing_code')));
		$this->success($list);
	}

	public function editInfo()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$first_name = ipost('first_name');
		$last_name = ipost('last_name');
		$dialing_code = ipost('dialing_code');
		$phone = ipost('phone');
		if (empty($first_name)) {
			$this->error('First Name is required');
		}
		if (empty($last_name)) {
			$this->error('Last Name is required');
		}
		if (empty($dialing_code) || empty($phone)) {
			$this->error('Phone Number is required');
		}
		$data = [
			'first_name' => substr($first_name, 0, 32),
			'last_name' => substr($last_name, 0, 32),
			'mobile' => substr($dialing_code.' '.$phone, 0, 20),
		];
		$rst = make('app/service/Member')->updateData(userId(), $data);
		if ($rst) {
			$info = session()->get(APP_TEMPLATE_TYPE.'_info');
			session()->set(APP_TEMPLATE_TYPE.'_info', array_merge($info, $data));
			$this->success('Update your info success.');
		} else {
			$this->error('Update your info failed.');
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
		$info = make('app/service/attachment/Attachment')->getAttachmentById($attach_id);
		if (empty($info)) {
			$this->error('File was not exist.');
		}
		$avatar = $info['cate'].DS.$info['name'].'.'.$info['type'];
		$rst = make('app/service/Member')->updateData(userId(), ['avatar'=>$avatar]);
		if ($rst) {
			session()->set(APP_TEMPLATE_TYPE.'_info', $info['url'], 'avatar');
		}
		$this->success('Update your avatar success.');
	}

	public function wish()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$spuId = ipost('spu_id', 0);
		if (empty($spuId)) {
			$this->error('param error');
		}
		$rst = make('app/service/member/Collect')->collectProduct($spuId);
		if ($rst) {
			$this->success($rst, $rst == 1 ? distT('add_wish') : distT('remove_wish'));
		} else {
			$this->error(distT('wish_error'));
		}
	}

	public function address()
	{
		html()->addCss();
		html()->addCss('common/address');
		html()->addCss('common/productList');
		html()->addJs();
		html()->addJs('common/address');
		if ($this->isLogin()) {
			$page = iget('page', 1);
			$size = iget('size', 10);
			$list = $this->getAddressList($page, $size);
			$this->assign('list', $list);
			$this->assign('page', $page);
			$this->assign('size', $size);
		}
		$this->assign('_title', distT('my_address'));
		$this->view();
	}

	protected function getAddressList($page=1, $size=10)
	{
		$memId = userId();
		if (!$memId) return [];
		$list = make('app/service/member/Address')->getListData(['mem_id'=>$memId], '*', $page, $size, ['is_default'=>'desc', 'is_bill'=>'desc','address_id'=>'desc']);
		if (!empty($list)) {
			$countryList = array_unique(array_column($list, 'country_code2'));
			$countryList = make('app/service/address/Country')->getListData(['code2'=>['in', $countryList]], 'code2,name_en');
			$countryList = array_column($countryList, 'name_en', 'code2');
			foreach ($list as $key => $value) {
				$value['country'] = $countryList[$value['country_code2']] ?? '';
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
		$id = ipost('id');
		$memId = userId();
		$where = [
			'address_id' => $id,
			'mem_id' => $memId,
		];
		$service = make('app/service/member/Address');
		if (!$service->getCountData($where)) {
			$this->error('That address was not exist!');
		}
		$service->updateData(['mem_id'=>$memId], ['is_default'=>0]);
		$service->updateData($id, ['is_default'=>1]);
		$this->success();
	}

	public function setAddressBillDefault()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$id = ipost('id');
		$memId = userId();
		$where = [
			'address_id' => $id,
			'mem_id' => $memId,
		];
		$service = make('app/service/member/Address');
		if (!$service->getCountData($where)) {
			$this->error('That address was not exist!');
		}
		$service->updateData(['mem_id'=>$memId], ['is_bill'=>0]);
		$service->updateData($id, ['is_bill'=>1]);
		$this->success();
	}

	public function deleteAddress()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$id = ipost('id');
		$memId = userId();
		$where = [
			'address_id' => $id,
			'mem_id' => $memId,
		];
		$service = make('app/service/member/Address');
		if (!$service->getCountData($where)) {
			$this->error('That address was not exist!');
		}
		$service->deleteData($id);
		$this->success();
	}

	public function getAddress()
	{
		if ($this->isLogin()) {
			$page = ipost('page', 1);
			$size = ipost('size', 10);
			$list = $this->getAddressList($page, $size);
		} else {
			$list = session()->get(APP_TEMPLATE_TYPE.'_info.address') ?? [];
		}
		$this->success($list);
	}

	public function getAddressInfo()
	{
		if ($this->isLogin()) {
			$id = ipost('id');
			$info = make('app/service/member/Address')->getInfo($id);
		} else {
			$list = session()->get(APP_TEMPLATE_TYPE.'_info.address') ?? [];
			foreach ($list as $value) {
				if ($value['address_id'] ?? 0 == $id) {
					$info = $value;
					break;
				}
			}
		}
		if (empty($info)) {
			$this->error('Sorry, That address was not exist!');
		}
		$this->success($info);
	}

	public function editAddress()
	{
		if (!$this->isLogin()) {
			$this->error(appT('need_login'), 10001);
		}
		$address_id = ipost('address_id');
		$country_code2 = ipost('country_code2');
		$tax_number = ipost('tax_number');
		$first_name = ipost('first_name');
		$last_name = ipost('last_name');
		$phone = ipost('phone');
		$postcode = ipost('postcode');
		$city = ipost('city');
		$zone_id = ipost('zone_id');
		$state = ipost('state');
		$address1 = ipost('address1');
		$address2 = ipost('address2');
		$is_default = ipost('is_default');
		$is_bill = ipost('is_bill');
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
			$this->error('ZIP Code is required');
		}
		if (empty($city)) {
			$this->error('City is required');
		}
		if (empty($state)) {
			$this->error('State / Region is required');
		}
		if (empty($address1)) {
			$this->error('Address is required');
		}
		$data = [
			'country_code2' => substr($country_code2, 0, 2),
			'tax_number' => substr($tax_number, 0, 32),
			'first_name' => substr($first_name, 0, 32),
			'last_name' => substr($last_name, 0, 32),
			'postcode' => substr($postcode, 0, 10),
			'city' => substr($city, 0, 32),
			'zone_id' => $zone_id,
			'state' => substr($state, 0, 32),
			'address1' => substr($address1, 0, 64),
			'address2' => substr($address2, 0, 64),
			'is_default' => $is_default == 1 ? 1 : 0,
			'is_bill' => $is_bill == 1 ? 1 : 0,
		];
		$countryInfo = make('app/service/address/Country')->loadData($country_code2, 'dialing_code');
		$data['phone'] = '+'.$countryInfo['dialing_code'].' '.$phone;
		if (empty(userId())) {
			$addressData = session()->get(APP_TEMPLATE_TYPE.'_info.address') ?? [];
			$data['address_id'] = randString(10, false, false, true);
			$addressData[] = $data;
			$rst = session()->set(APP_TEMPLATE_TYPE.'_info.address', $addressData);
		} else {
			if (empty($address_id)) {
				$data['mem_id'] = userId();
				$rst = make('app/service/member/Address')->insert($data);
			} else {
				$rst = make('app/service/member/Address')->updateData(['address_id'=>$address_id, 'mem_id'=>userId()], $data);
			}
		}
		if ($rst) {
			$this->success($address_id ? 'Edit address success' : 'Add address success');
		} else {
			$this->error($address_id ? 'Edit address failed' : 'Add address failed');
		}
	}

	public function wishList()
	{
		html()->addCss();
		html()->addCss('common/productList');
		html()->addJs();
		if ($this->isLogin()) {
			$page = iget('page', 1);
			$size = iget('size', 10);
			$list = $this->getWishList($page, $size);
			$this->assign('list', $list);
		}
		$this->assign('_title', distT('my_wish'));
		$this->view();
	}

	protected function getWishList($page=1, $size=10)
	{
		$memId = userId();
		$list = make('app/service/member/Collect')->getListData(['mem_id'=>$memId], '*', $page, $size, ['coll_id'=>'desc']);
		if (!empty($list)) {
			$spuIdArr = array_unique(array_column($list, 'spu_id'));
			$spuArr = make('app/service/product/Spu')->getListById($spuIdArr);
			$spuArr = array_column($spuArr, null, 'spu_id');
			foreach ($list as $key => $value) {
				$list[$key]  = array_merge($value, $spuArr[$value['spu_id']]);
			}
		}
		return $list;
	}

	public function history()
	{
		html()->addCss();
		html()->addCss('common/productList');
		html()->addJs();

		$page = iget('page', 1);
		$size = iget('size', 10);

		$list = $this->getHistoryList($page, $size);

		$this->assign('list', $list);
		$this->assign('_title', appT('my_history'));
		$this->view();
	}

	protected function getHistoryList($page=1, $size=10)
	{
		$memId = userId();
		if (!$memId) return [];
		$list = make('app/service/member/History')->getListData(['mem_id'=>$memId], '*', $page, $size, ['his_id'=>'desc']);
		if (!empty($list)) {
			$spuIdArr = array_unique(array_column($list, 'spu_id'));
			//获取收藏ID
			$where = [
				'mem_id' => $memId,
				'spu_id' => ['in', $spuIdArr],
			];
			$collSpuList = make('app/service/member/Collect')->getListData($where, 'spu_id');
			$collSpuList = array_column($collSpuList, 'spu_id');

			$spuArr = make('app/service/product/Spu')->getListById($spuIdArr);
			$spuArr = array_column($spuArr, null, 'spu_id');
			$tempArr = [];
			//按日期分组
			foreach ($list as $key => $value) {
				$value['is_liked'] = in_array($value['spu_id'], $collSpuList) ? 1 : 0;
				$tempArr[$value['add_date']][] = array_merge($value, $spuArr[$value['spu_id']]);
			}
			$list = $tempArr;
		}
		return $list;
	}

	public function deleteHistory()
	{
		$id = ipost('id');
		if (empty($id)) {
			$this->error('Sorry, That history product id was Empty.');
		}
		$where = [
			'mem_id' => userId(),
			'his_id' => $id,
		];
		$rst = make('app/service/member/History')->deleteData($where);
		if ($rst) {
			$this->success('That history product was removed failed.');
		} else {
			$this->error('Sorry, That history product was removed failed.');
		}
	}

	public function coupon()
	{
		html()->addCss();
		html()->addCss('common/productList');
		html()->addJs();
		$this->assign('_title', distT('my_coupon'));
		$this->view();
	}
}