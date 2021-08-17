<?php

namespace app\controller\bag;
use app\controller\Controller;

class UserInfoController extends Controller
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();

		$info = session()->get(APP_TEMPLATE_TYPE.'_info');
		$info['name'] = trim($info['first_name'].' '.$info['last_name']);
		$temp = explode(' ', $info['mobile']);
		$info['dialing_code'] = $temp[0];
		$info['phone'] = $temp[1] ?? '';

		$where = [
			'mem_id' => $info['mem_id'],
		];
		//收藏统计
		$collectionTotal = make('app/service/member/CollectService')->getCountData($where);
		//足迹统计
		$historyTotal = make('app/service/member/HistoryService')->getCountData($where);
		//地址统计
		$addressTotal = make('app/service/member/AddressService')->getCountData($where);

		$this->assign('collectionTotal', $collectionTotal);
		$this->assign('historyTotal', $historyTotal);
		$this->assign('addressTotal', $addressTotal);
		$this->assign('info', $info);
		$this->assign('_title', 'My Info - '.site()->getName());
		$this->view();
	}

	public function getInfo()
	{
		$list = make('app/service/address/CountryService')->getListData(['status'=>1], 'dialing_code', 0, 0, ['sort'=>'asc']);
		$list = array_values(array_unique(array_column($list, 'dialing_code')));
		$this->success($list);
	}

	public function editInfo()
	{
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
		$rst = make('app/service/MemberService')->updateData(userId(), $data);
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
		$attach_id = ipost('attach_id');
		if (empty($attach_id)) {
			$this->error('Param error');
		}
		$info = make('app/service/AttachmentService')->getAttachmentById($attach_id);
		if (empty($info)) {
			$this->error('File was not exist.');
		}
		$avatar = $info['cate'].DS.$info['name'].'.'.$info['type'];
		$rst = make('app/service/MemberService')->updateData(userId(), ['avatar'=>$avatar]);
		if ($rst) {
			session()->set(APP_TEMPLATE_TYPE.'_info.avatar', $info['url']);
		}
		$this->success('Update your avatar success.');
	}

	public function wish()
	{
		$spuId = ipost('spu_id', 0);
		if (empty($spuId)) {
			$this->error('param error');
		}
		$rst = make('app/service/member/CollectService')->collectProduct($spuId);
		if ($rst) {
			$this->success($rst, $rst == 1 ? 'That product was add to your wish list success' : 'That product was removed from your wish list success');
		} else {
			$this->error('collect failed');
		}
	}

	public function address()
	{
		html()->addCss();
		html()->addCss('common/address');
		html()->addJs();
		html()->addJs('common/address');

		$page = iget('page', 1);
		$size = iget('size', 10);
		
		$list = $this->getAddressList($page, $size);

		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->assign('size', $size);
		$this->assign('_title', 'My address - '.site()->getName());
		$this->view();
	}

	protected function getAddressList($page=1, $size=10)
	{
		$list = make('app/service/member/AddressService')->getListData(['mem_id'=>userId()], '*', $page, $size, ['is_default'=>'desc','address_id'=>'desc']);
		if (!empty($list)) {
			$countryList = array_unique(array_column($list, 'country_code2'));
			$countryList = make('app/service/address/CountryService')->getListData(['code2'=>['in', $countryList]], 'code2,name_en');
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
		$id = (int)ipost('id');
		$memId = userId();
		$where = [
			'address_id' => $id,
			'mem_id' => $memId,
		];
		$service = make('app/service/member/AddressService');
		if (!$service->getCountData($where)) {
			$this->error('That address was not exist!');
		}
		$service->updateData(['mem_id'=>$memId], ['is_default'=>0]);
		$service->updateData($id, ['is_default'=>1]);
		$this->success();
	}

	public function deleteAddress()
	{
		$id = (int)ipost('id');
		$memId = userId();
		$where = [
			'address_id' => $id,
			'mem_id' => $memId,
		];
		$service = make('app/service/member/AddressService');
		if (!$service->getCountData($where)) {
			$this->error('That address was not exist!');
		}
		$service->deleteData($id);
		$this->success();
	}

	public function getAddress()
	{
		$page = ipost('page', 1);
		$size = ipost('size', 10);
		$list = $this->getAddressList($page, $size);
		$this->success($list);
	}

	public function getAddressInfo()
	{
		$id = (int)ipost('id');
		$where = [
			'address_id' => $id,
			'mem_id' => userId(),
		];
		$info = make('app/service/member/AddressService')->loadData($id);
		if (empty($info)) {
			$this->error('Sorry, That address was not exist!');
		} else {
			$this->success($info);
		}
	}

	public function editAddress()
	{
		$address_id = ipost('address_id');
		$country_code2 = ipost('country_code2');
		$tax_number = ipost('tax_number');
		$first_name = ipost('first_name');
		$last_name = ipost('last_name');
		$phone = ipost('phone');
		$postcode = ipost('postcode');
		$city = ipost('city');
		$zone_id = (int)ipost('zone_id');
		$state = ipost('state');
		$address1 = ipost('address1');
		$address2 = ipost('address2');
		$is_default = (int)ipost('is_default');
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
		];
		$countryInfo = make('app/service/address/CountryService')->loadData($country_code2, 'dialing_code');
		$data['phone'] = '+'.$countryInfo['dialing_code'].' '.$phone;
		if (empty($address_id)) {
			$data['mem_id'] = userId();
			$rst = make('app/service/member/AddressService')->insert($data);
		} else {
			$rst = make('app/service/member/AddressService')->updateData(['address_id'=>$address_id, 'mem_id'=>userId()], $data);
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

		$page = iget('page', 1);
		$size = iget('size', 10);

		$list = $this->getWishList($page, $size);

		$this->assign('list', $list);

		$this->assign('_title', 'My Wish List - '.site()->getName());

		$this->view();
	}

	protected function getWishList($page=1, $size=10)
	{
		$memId = userId();
		$list = make('app/service/member/CollectService')->getListData(['mem_id'=>$memId], '*', $page, $size, ['coll_id'=>'desc']);
		if (!empty($list)) {
			$spuIdArr = array_unique(array_column($list, 'spu_id'));
			$spuArr = make('app/service/product/SpuService')->getListById($spuIdArr);
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
		$this->assign('_title', 'My History List - '.site()->getName());
		$this->view();
	}

	protected function getHistoryList($page=1, $size=10)
	{
		$memId = userId();
		$list = make('app/service/member/HistoryService')->getListData(['mem_id'=>$memId], '*', $page, $size, ['his_id'=>'desc']);
		if (!empty($list)) {
			$spuIdArr = array_unique(array_column($list, 'spu_id'));
			//获取收藏ID
			$where = [
				'mem_id' => $memId,
				'spu_id' => ['in', $spuIdArr],
			];
			$collSpuList = make('app/service/member/CollectService')->getListData($where, 'spu_id');
			$collSpuList = array_column($collSpuList, 'spu_id');

			$spuArr = make('app/service/product/SpuService')->getListById($spuIdArr);
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
		$rst = make('app/service/member/HistoryService')->deleteData($where);
		if ($rst) {
			$this->success('That history product was removed failed.');
		} else {
			$this->error('Sorry, That history product was removed failed.');
		}
	}
}