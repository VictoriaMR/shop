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

	public function getInfo($orderId)
	{
		$where = [
			'order_id' => $orderId,
			'mem_id' => $this->userId(),
			'is_delete' => 0,
		];
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
}