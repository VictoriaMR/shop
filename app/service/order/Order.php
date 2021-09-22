<?php 

namespace app\service\order;
use app\service\Base;

class Order extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/order/Order');
	}

	public function createOrder($skuIdArr, $shippingAddressId, $billingAddressId, $couponId=0, $insurance=false)
	{
		//递送地址 账单地址
		$where = [
			'address_id' => $shippingAddressId,
			'mem_id' => $this->userId(),
		];
		$address = make('app/service/member/Address');
		$shippingAddress = $address->loadData($where);
		if (empty($shippingAddress)) {
			return false;
		}
		if ($shippingAddressId == $billingAddressId) {
			$billingAddress = $shippingAddress;
		} else {
			$where['address_id'] = $billingAddressId;
			$billingAddress = $address->loadData($billingAddressId);
			if (empty($billingAddress)) {
				return false;
			}
		}
		//获取sku信息
		$sku = make('app/service/product/Sku');
		$where = [
			'site_id' => $this->siteId(),
			'sku_id' => ['in', array_keys($skuIdArr)],
			'status' => $sku->getConst('STATUS_OPEN'),
		];
		$skuList = $sku->getListData($where, 'sku_id,stock');
		$skuList = array_column($skuList, 'stock', 'sku_id');

		$list = [];
		$productTotal = 0;
		$originalPriceTotal = 0;
		$orderProduct = [];
		$orderProductAttr = [];
		foreach ($skuIdArr as $key => $value) {
			if (empty($skuList[$key])) {
				return false;
			}
			if ($skuList[$key] < $value) {
				return false;
			}
			$info = $sku->getInfoCache($key, $this->lanId());
			$productTotal = $info['price'] * $value;
			$originalPriceTotal = $info['original_price'] * $value;
			$orderProduct[] = [
				'sku_id' => $key,
				'name' => $info['name'],
				'quantity' => $value,
				'price' => $info['price'],
				'original_price' => $info['original_price'],
			];
			foreach ($info['attrMap'] as $k => $v) {
				foreach ($v as $kk => $vv) {
					$orderProductAttr[$key][] = [
						'attr_id' => $k,
						'attr_name' => $info['attr'][$k],
						'attv_id' => $vv,
						'attv_name' => $info['attv'][$vv],
						'attach_id' => empty($info['attvImage'][$vv]) ? 0 : $info['attvImage'][$vv]['attach_id'],
					];
				}
			}
		}
		$couponId = (int)$couponId;
		$couponFree = 0;
		if (!empty($couponId)) {
			//todo 优惠券使用
		}
		//当前货币
		$currency = make('app/service/Language')->currency();
		//保险
		$insuranceFree = $insurance ? $this->getInsurance($productTotal) : 0;
		//运费
		$shippingFee = $this->getShippingFee($productTotal);
		$insert = [
			'order_no' => substr(getUniqueName(), 2, -2),
			'site_id' => $this->siteId(),
			'mem_id' => $this->userId(),
			'coupon_id' => $couponId,
			'lan_id' => $this->lanId(),
			'is_moblie' => IS_MOBILE ? 1 : 0,
			'currency' => $currency,
			'insurance_free' => $insuranceFree,
			'coupon_free' => $couponFree,
			'shipping_fee' => $shippingFee,
			'product_total' => $productTotal,
			'order_total' => $productTotal + $couponFree + $insuranceFree + $shippingFee,
		];
		$orderProduct = make('app/service/order/Product');
		$orderProductAttr = make('app/service/order/ProductAttribute');
		$this->start();
		$orderId = $this->insertGetId($insert);
		$insert = [];
		foreach ($orderProduct as $value) {
			$value['order_id'] = $orderId;
			$orderProductId = $orderProduct->insertGetId($value);
			//同步减少库存
			$sku->where('sku_id', $value['sku_id'])->decrement('stock', $value['quantity']);
			$insert = array_merge($insert, array_map(function($value) use ($orderProductId){
				$value['order_product_id'] = $orderProductId;
				return $value;
			}, $orderProductAttr[$value['sku_id']]));
		}
		make('app/service/order/ProductAttribute')->insert($insert);

		$insert = [];
		$insert[] = [
			'order_id' => $orderId,
			'type' => 0,
			'first_name' => $shippingAddress['first_name'],
			'last_name' => $shippingAddress['last_name'],
			'country_code2' => $shippingAddress['country_code2'],
			'zone_id' => $shippingAddress['zone_id'],
			'state' => $shippingAddress['state'],
			'city' => $shippingAddress['city'],
			'address1' => $shippingAddress['address1'],
			'address2' => $shippingAddress['address2'],
			'postcode' => $shippingAddress['postcode'],
			'tax_number' => $shippingAddress['tax_number'],
		];
		$insert[] = [
			'order_id' => $orderId,
			'type' => 1,
			'first_name' => $billingAddress['first_name'],
			'last_name' => $billingAddress['last_name'],
			'country_code2' => $billingAddress['country_code2'],
			'zone_id' => $billingAddress['zone_id'],
			'state' => $billingAddress['state'],
			'city' => $billingAddress['city'],
			'address1' => $billingAddress['address1'],
			'address2' => $billingAddress['address2'],
			'postcode' => $billingAddress['postcode'],
			'tax_number' => $billingAddress['tax_number'],
		];
		make('app/service/order/Address')->insert($insert);

		$this->commit();
		return $orderId;
	}

	public function getShippingFee($total)
	{
		if ($total < 20) {
			return 1.99;
		} elseif ($total < 40) {
			return 2.99;
		} elseif ($total < 100) {
			return 3.99;
		} else {
			return 4.99;
		}
	}

	public function getInsurance($total)
	{
		if ($total > 49) {
			return 2.99;
		}
		return 1.99;
	}

	public function getInfo($orderId, $isAdmin=false)
	{
		$where = [
			'order_id' => $orderId
		];
		if (!$isAdmin) {
			$where['mem_id'] = userId();
		}
		$data = [];
		$temp = $this->loadData($where);
		if (empty($temp)) {
			return false;
		}
		$data['base'] = $temp;
		//递送地址 账单地址
		$temp = make('app/service/order/Address')->getListData(['order_id'=>$orderId]);
		$temp = array_column($temp, null, 'type');
		$data['shipping_address'] = $temp[0];
		$data['billing_address'] = $temp[1];
		//产品
		$temp = make('app/service/order/Product')->getListData(['order_id'=>$orderId]);
		$data['product'] = $temp;
		$ids = array_column($temp, 'order_product_id');
		//产品属性
		$temp = make('app/service/order/ProductAttribute')->getListData(['order_product_id'=>['in', $ids]]);
		//sku图片
		$ids = array_column($data['product'], 'attach_id');
		$ids = array_unique(array_merge($ids, array_column($temp, 'attach_id')));
		$imageArr = make('app/service/Attachment')->getList(['attach_id'=>['in', $ids]]);
		$imageArr = array_column($imageArr, null, 'attach_id');
		$orderProductOriginPrice = 0;
		$symbol = make('app/service/Currency')->getSymbolByCode($data['base']['currency']);
		foreach ($data['product'] as $key => $value) {
			$value['attr'] = [];
			foreach ($temp as $ak => $av) {
				if ($value['order_product_id'] == $av['order_product_id']) {
					$av['image'] = empty($imageArr[$av['attach_id']]) ? '' : $imageArr[$av['attach_id']]['url'];
					$value['attr'][] = $av;
				}
			}
			$value['price_format'] = $symbol.$value['price'];
			$value['original_price_format'] = $symbol.$value['original_price'];
			$value['image'] = empty($imageArr[$value['attach_id']]) ? '' : $imageArr[$value['attach_id']]['url'];
			$data['product'][$key] = $value;
			$orderProductOriginPrice += $value['quantity']*$value['original_price'];
		}
		$temp = [];
		$orderProductOriginPrice = sprintf('%.2f', $orderProductOriginPrice);
		$data['base']['order_total_format'] = $symbol.$data['base']['order_total'];
		$temp[] = [
			'type' => 0,
			'name' => 'Product Original Total',
			'value' => $orderProductOriginPrice,
			'value_format' => $symbol.$orderProductOriginPrice,
		];
		$temp[] = [
			'type' => 1,
			'name' => 'Product Total',
			'value' => $data['base']['product_total'],
			'value_format' => $symbol.$data['base']['product_total'],
		];
		$temp[] = [
			'type' => 2,
			'name' => 'Shipping Fee',
			'value' => $data['base']['shipping_fee'],
			'value_format' => $symbol.$data['base']['shipping_fee'],
		];
		if ($data['base']['insurance_free'] > 0) {
			$temp[] = [
				'type' => 3,
				'name' => 'Shipping Guarantee',
				'value' => $data['base']['insurance_free'],
				'value_format' => $symbol.$data['base']['insurance_free'],
			];	
		}
		$data['fee_list'] = $temp;
		return $data;
	}

	public function getList(array $where=[], $page=1, $size=10)
	{
		$fields = 'order_id,order_no,status,currency,product_total,order_total,is_review,add_time,is_delete';
		$list = $this->getListData($where, $fields, $page, $size, ['order_id'=>'desc']);
		if (!empty($list)) {
			//订单产品
			$orderProductArr = make('app/service/order/Product')->getListData(['order_id'=>['in', array_column($list, 'order_id')]]);
			//属性
			$attrArr = make('app/service/order/ProductAttribute')->getListData(['order_product_id'=>['in', array_column($orderProductArr, 'order_product_id')]], 'order_product_id,attr_name,attv_name,attach_id', 0, 0, ['item_id'=>'asc']);
			//属性图片
			$attachIdArr = array_filter(array_column($attrArr, 'attach_id'));
			$attachIdArr = array_merge($attachIdArr, array_column($orderProductArr, 'attach_id'));
			//文件
			$attachArr = make('app/service/Attachment')->getList(['attach_id'=>['in', array_unique($attachIdArr)]]);
			$attachArr = array_column($attachArr, 'url', 'attach_id');
			//产品属性归类
			$tempArr = [];
			foreach ($attrArr as $value) {
				if (!isset($tempArr[$value['order_product_id']])) {
					$tempArr[$value['order_product_id']] = [];
				}
				if (!empty($value['attach_id'])) {
					$value['image'] = $attachArr[$value['attach_id']] ?? '';
				}
				$tempArr[$value['order_product_id']][] = $value;
			}
			$attrArr = $tempArr;
			//产品图片
			$tempArr = [];
			foreach ($orderProductArr as $value) {
				if (!isset($tempArr[$value['order_id']])) {
					$tempArr[$value['order_id']] = [];
				}
				$value['image'] = $attachArr[$value['attach_id']] ?? '';
				$value['attr'] = $attrArr[$value['order_product_id']];
				$tempArr[$value['order_id']][] = $value;
			}
			$orderProductArr = $tempArr;
			//订单产品归类
			$currencyService = make('app/service/Currency');
			$currencyArr = array_unique(array_column($list, 'currency'));
			$tempArr = [];
			foreach ($currencyArr as $value) {
				$tempArr[$value] = $currencyService->getSymbolByCode($value);
			}
			$currencyArr = $tempArr;

			foreach ($list as $key => $value) {
				$value['product'] = $orderProductArr[$value['order_id']];
				$value['status_text'] = $this->getappTStatus($value['status']);
				$value['currency_symbol'] = $currencyArr[$value['currency']] ?? '';
				$value['url'] = url('order/detail', ['id'=>$value['order_id']]);
				$value['add_time_format'] = date('j M, Y', strtotime($value['add_time']));
				$list[$key] = $value;
			}
		}
		return $list;
	}

	protected function getappTStatus($status)
	{
		$arr = [
			$this->getConst('STATUS_CANCEL') => 'cancel',
			$this->getConst('STATUS_WAIT_PAY') => 'wait_pay',
			$this->getConst('STATUS_PAIED') => 'paid',
			$this->getConst('STATUS_SHIPPED') => 'shipped',
			$this->getConst('STATUS_FINISHED') => 'completed',
			$this->getConst('STATUS_PART_REFUND') =>'part_refund',
			$this->getConst('STATUS_FULL_REFUND') => 'full_refund',
			$this->getConst('STATUS_REFUNDING') => 'refunding',
		];
		return appT($arr[$status]);
	}

	public function getListByKeyword($where, $keyword, $page=1, $size=10)
	{
		if (empty($keyword)) {
			return [];
		}
		$idArr = [];
		$tempWhere = $where;
		$tempWhere['order_no'] = ['like', $keyword];
		$list = $this->getListData($tempWhere, 'order_id');
		if (!empty($list)) {
			$idArr = array_column($list, 'order_id');
		}
		//订单产品关键字搜索
		$tempWhere = [];
		foreach ($where as $key => $value) {
			$tempWhere['a.'.$key] = $value;
		}
		$tempWhere['b.`name`'] = ['like', '%'.$keyword.'%'];
		$list = $this->table('`order` as a')->leftJoin('order_product as b', 'a.order_id', 'b.order_id')->where($tempWhere, 'a.order_id')->get();
		if (!empty($list)) {
			$idArr = array_merge($idArr, array_column($list, 'order_id'));
		}
		if (empty($idArr)) {
			$where = ['order_id'=>0];
		} else {
			$where['order_id'] = ['in', array_unique($idArr)];
		}
		return $this->getList($where, $page, $size);
	}
}