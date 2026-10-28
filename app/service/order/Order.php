<?php

namespace app\service\order;
use app\service\Base;

class Order extends Base
{
	public function createOrder($skuIdArr, $shippingAddressId, $billingAddressId, $insurance = false, $couponId = 0)
	{
		if (empty($skuIdArr) || !is_array($skuIdArr)) {
			return false;
		}
		$memId = $this->userId();
		$addressService = service('member/Address');
		$shippingAddress = $addressService->loadData([
			'address_id' => (int)$shippingAddressId,
			'mem_id'     => $memId,
		]);
		if (empty($shippingAddress)) {
			return false;
		}
		if ((int)$shippingAddressId === (int)$billingAddressId || empty($billingAddressId)) {
			$billingAddress = $shippingAddress;
		} else {
			$billingAddress = $addressService->loadData([
				'address_id' => (int)$billingAddressId,
				'mem_id'     => $memId,
			]) ?: $shippingAddress;
		}

		$skuService = service('product/Sku');
		$skuRows = $skuService->getListData([
			'sku_id' => ['in', array_keys($skuIdArr)],
			'status' => $skuService->getConst('STATUS_OPEN'),
		], 'sku_id,spu_id,attach_id,stock,price');
		$skuMap = array_column($skuRows, null, 'sku_id');

		$productTotal = 0;
		$orderProducts = [];
		$orderProductAttrs = [];
		$lanId = $this->lanId();

		foreach ($skuIdArr as $skuId => $qty) {
			$skuId = (int)$skuId;
			$qty = max(1, (int)$qty);
			if (empty($skuMap[$skuId]) || (int)$skuMap[$skuId]['stock'] < $qty) {
				return false;
			}
			$info = $skuService->getInfoCache($skuId, $lanId);
			if (empty($info)) {
				return false;
			}
			$unitPrice = (float)$info['price'];
			$origPrice = (float)($info['original_price'] ?? $unitPrice);
			$productTotal += $unitPrice * $qty;

			$attachId = (int)($skuMap[$skuId]['attach_id'] ?? 0);
			if ($attachId <= 0 && !empty($skuMap[$skuId]['spu_id'])) {
				$attachId = (int)(service('product/Spu')->loadData($skuMap[$skuId]['spu_id'], 'attach_id')['attach_id'] ?? 0);
			}

			$orderProducts[] = [
				'sku_id'         => $skuId,
				'attach_id'      => $attachId,
				'name'           => substr((string)($info['name'] ?? 'Product #' . $skuId), 0, 250),
				'quantity'       => $qty,
				'price'          => $unitPrice,
				'original_price' => $origPrice,
			];

			$orderProductAttrs[$skuId] = [];
			if (!empty($info['attr']) && is_array($info['attr'])) {
				foreach ($info['attr'] as $aRow) {
					$orderProductAttrs[$skuId][] = [
						'attrn_id'   => (int)($aRow['attrn_id'] ?? 0),
						'attr_name'  => substr((string)($aRow['attrn_name'] ?? 'Option'), 0, 120),
						'attrv_id'   => (int)($aRow['attrv_id'] ?? 0),
						'attr_value' => substr((string)($aRow['attrv_name'] ?? ''), 0, 120),
						'attach_id'  => (int)($aRow['attach_id'] ?? 0),
					];
				}
			}
		}

		$couponId = (int)$couponId;
		$couponFree = 0;
		$currency = currencyId('code') ?: 'USD';
		$insuranceFree = $insurance ? $this->getInsurance($productTotal) : 0;
		$shippingFee = $this->getShippingFee($productTotal);

		$orderInsert = [
			'site_id'        => $this->siteId(),
			'mem_id'         => $memId,
			'status'         => $this->getConst('STATUS_WAIT_PAY'),
			'coupon_id'      => $couponId,
			'payment_id'     => 0,
			'lan_id'         => (string)$lanId,
			'is_mobile'      => isMobile() ? 1 : 0,
			'currency'       => substr((string)$currency, 0, 3),
			'insurance_free' => $insuranceFree,
			'coupon_free'    => $couponFree,
			'shipping_fee'   => $shippingFee,
			'product_total'  => $productTotal,
			'order_total'    => $productTotal - $couponFree + $insuranceFree + $shippingFee,
		];

		$orderProductService = service('order/Product');
		$orderProductAttrService = service('order/ProductAttribute');

		$this->start();
		$orderId = $this->insertGetId($orderInsert);
		if (!$orderId) {
			$this->rollback();
			return false;
		}

		$attrBatch = [];
		foreach ($orderProducts as $pRow) {
			$pRow['order_id'] = $orderId;
			$orderProductId = $orderProductService->insertGetId($pRow);
			$skuService->where('sku_id', $pRow['sku_id'])->decrement('stock', $pRow['quantity']);
			if (!empty($orderProductAttrs[$pRow['sku_id']])) {
				foreach ($orderProductAttrs[$pRow['sku_id']] as $attrItem) {
					$attrItem['order_product_id'] = $orderProductId;
					$attrBatch[] = $attrItem;
				}
			}
		}
		if (!empty($attrBatch)) {
			$orderProductAttrService->insert($attrBatch);
		}

		$addressBatch = [
			[
				'order_id'      => $orderId,
				'type'          => 0,
				'first_name'    => substr((string)($shippingAddress['first_name'] ?? ''), 0, 32),
				'last_name'     => substr((string)($shippingAddress['last_name'] ?? ''), 0, 32),
				'country_code2' => substr((string)($shippingAddress['country_code2'] ?? 'US'), 0, 2),
				'zone_id'       => (int)($shippingAddress['zone_id'] ?? 0),
				'state'         => substr((string)($shippingAddress['state'] ?? ''), 0, 32),
				'city'          => substr((string)($shippingAddress['city'] ?? ''), 0, 32),
				'address1'      => substr((string)($shippingAddress['address1'] ?? ''), 0, 64),
				'address2'      => substr((string)($shippingAddress['address2'] ?? ''), 0, 64),
				'phone'         => substr((string)($shippingAddress['phone'] ?? ''), 0, 20),
				'postcode'      => substr((string)($shippingAddress['postcode'] ?? ''), 0, 10),
				'tax_number'    => substr((string)($shippingAddress['tax_number'] ?? ''), 0, 32),
			],
			[
				'order_id'      => $orderId,
				'type'          => 1,
				'first_name'    => substr((string)($billingAddress['first_name'] ?? ''), 0, 32),
				'last_name'     => substr((string)($billingAddress['last_name'] ?? ''), 0, 32),
				'country_code2' => substr((string)($billingAddress['country_code2'] ?? 'US'), 0, 2),
				'zone_id'       => (int)($billingAddress['zone_id'] ?? 0),
				'state'         => substr((string)($billingAddress['state'] ?? ''), 0, 32),
				'city'          => substr((string)($billingAddress['city'] ?? ''), 0, 32),
				'address1'      => substr((string)($billingAddress['address1'] ?? ''), 0, 64),
				'address2'      => substr((string)($billingAddress['address2'] ?? ''), 0, 64),
				'phone'         => substr((string)($billingAddress['phone'] ?? ''), 0, 20),
				'postcode'      => substr((string)($billingAddress['postcode'] ?? ''), 0, 10),
				'tax_number'    => substr((string)($billingAddress['tax_number'] ?? ''), 0, 32),
			],
		];
		service('order/Address')->insert($addressBatch);
		$this->commit();

		service('order/StatusHistory')->addLog($orderId, $this->getConst('STATUS_WAIT_PAY'), $lanId);
		return $orderId;
	}

	public function getShippingFee($total)
	{
		$total = (float)$total;
		if ($total >= 59) {
			return 0.00;
		} elseif ($total < 20) {
			return 4.99;
		} elseif ($total < 40) {
			return 3.99;
		}
		return 2.99;
	}

	public function getInsurance($total)
	{
		if ((float)$total > 49) {
			return 2.99;
		}
		return 1.99;
	}

	public function getOrderNo($orderId, $addTime = ''): string
	{
		$datePart = $addTime ? date('Ymd', strtotime($addTime)) : date('Ymd');
		return 'ORD-' . $datePart . '-' . str_pad((string)$orderId, 6, '0', STR_PAD_LEFT);
	}

	public function getInfo($orderId, $isAdmin = false)
	{
		$where = [
			'order_id' => (int)$orderId,
		];
		if (!$isAdmin) {
			$where['mem_id'] = userId();
		}
		$temp = $this->loadData($where);
		if (empty($temp)) {
			return false;
		}
		$temp['order_no'] = $this->getOrderNo($temp['order_id'], $temp['add_time'] ?? '');
		$temp['add_time_format'] = !empty($temp['add_time']) ? date('M j, Y H:i', strtotime($temp['add_time'])) : '';
		$temp['status_text'] = $this->getStatusLabel($temp['status']);
		$data = ['base' => $temp];

		// 递送地址 & 账单地址
		$addrRows = service('order/Address')->getListData(['order_id' => $orderId]);
		$addrByType = array_column($addrRows, null, 'type');
		$countryService = service('system/Country');
		$shipAddr = $addrByType[0] ?? [];
		$billAddr = $addrByType[1] ?? $shipAddr;
		if (!empty($shipAddr['country_code2'])) {
			$shipAddr['country'] = $countryService->getName($shipAddr['country_code2']);
		}
		if (!empty($billAddr['country_code2'])) {
			$billAddr['country'] = $countryService->getName($billAddr['country_code2']);
		}
		$data['shipping_address'] = $shipAddr;
		$data['billing_address'] = $billAddr;

		// 订单商品
		$products = service('order/Product')->getListData(['order_id' => $orderId]);
		$opIds = array_column($products, 'order_product_id');
		$attrs = !empty($opIds) ? service('order/ProductAttribute')->getListData(['order_product_id' => ['in', $opIds]]) : [];

		$attachIds = array_values(array_unique(array_filter(array_merge(
			array_column($products, 'attach_id'),
			array_column($attrs, 'attach_id')
		))));
		$imageMap = [];
		if (!empty($attachIds)) {
			$rawImgs = service('attachment/Attachment')->getList(['attach_id' => ['in', $attachIds]]);
			$imageMap = array_column($rawImgs, 'url', 'attach_id');
		}

		$skuIds = array_values(array_unique(array_filter(array_column($products, 'sku_id'))));
		$skuSpuMap = [];
		if (!empty($skuIds)) {
			$skuRows = service('product/Sku')->getListData(['sku_id' => ['in', $skuIds]], 'sku_id,spu_id');
			$skuSpuMap = array_column($skuRows, 'spu_id', 'sku_id');
		}

		$symbol = service('system/Currency')->getSymbolByCode($data['base']['currency']) ?: '$';
		$orderProductOriginPrice = 0;
		$noImg = siteUrl('image/common/noimg.svg');

		foreach ($products as $key => $value) {
			$value['attr'] = [];
			foreach ($attrs as $av) {
				if ((int)$value['order_product_id'] === (int)$av['order_product_id']) {
					$av['attv_name'] = $av['attr_value'] ?? '';
					$av['image'] = !empty($av['attach_id']) ? ($imageMap[$av['attach_id']] ?? '') : '';
					$value['attr'][] = $av;
				}
			}
			$unitPrice = (float)$value['price'];
			$origPrice = (float)$value['original_price'];
			$qty = max(1, (int)$value['quantity']);
			$value['price'] = sprintf('%.2f', $unitPrice);
			$value['original_price'] = sprintf('%.2f', $origPrice);
			$value['price_format'] = $symbol . $value['price'];
			$value['original_price_format'] = $symbol . $value['original_price'];
			$value['subtotal_format'] = $symbol . sprintf('%.2f', $unitPrice * $qty);
			$value['image'] = !empty($value['attach_id']) ? ($imageMap[$value['attach_id']] ?? $noImg) : $noImg;
			$spuId = $skuSpuMap[$value['sku_id']] ?? 0;
			$value['url'] = $spuId ? url('product', ['pid' => $spuId, 'sid' => $value['sku_id']]) : 'javascript:;';
			$products[$key] = $value;
			$orderProductOriginPrice += $qty * $origPrice;
		}
		$data['product'] = $products;

		$orderProductOriginPrice = sprintf('%.2f', $orderProductOriginPrice);
		$prodTotalFmt = sprintf('%.2f', (float)$data['base']['product_total']);
		$shipFeeFmt = sprintf('%.2f', (float)$data['base']['shipping_fee']);
		$insFeeFmt = sprintf('%.2f', (float)$data['base']['insurance_free']);
		$ordTotalFmt = sprintf('%.2f', (float)$data['base']['order_total']);

		$data['base']['product_total'] = $prodTotalFmt;
		$data['base']['shipping_fee'] = $shipFeeFmt;
		$data['base']['insurance_free'] = $insFeeFmt;
		$data['base']['order_total'] = $ordTotalFmt;
		$data['base']['order_total_format'] = $symbol . $ordTotalFmt;
		$data['base']['currency_symbol'] = $symbol;

		$feeList = [
			[
				'type'         => 0,
				'name'         => 'Original Retail Total',
				'value'        => $orderProductOriginPrice,
				'value_format' => $symbol . $orderProductOriginPrice,
			],
			[
				'type'         => 1,
				'name'         => 'Items Subtotal',
				'value'        => $prodTotalFmt,
				'value_format' => $symbol . $prodTotalFmt,
			],
			[
				'type'         => 2,
				'name'         => 'Shipping & Handling',
				'value'        => $shipFeeFmt,
				'value_format' => (float)$shipFeeFmt > 0 ? ($symbol . $shipFeeFmt) : 'FREE',
			],
		];
		if ((float)$data['base']['insurance_free'] > 0) {
			$feeList[] = [
				'type'         => 3,
				'name'         => 'Shipping Protection Guarantee',
				'value'        => $insFeeFmt,
				'value_format' => $symbol . $insFeeFmt,
			];
		}
		$data['fee_list'] = $feeList;
		$data['status_history'] = service('order/StatusHistory')->getListData(['order_id' => $orderId], '*', 0, 0, ['item_id' => 'desc']);
		return $data;
	}

	public function getList(array $where = [], $page = 1, $size = 10)
	{
		$fields = 'order_id,status,lan_id,currency,product_total,shipping_fee,insurance_free,order_total,is_review,add_time,is_delete';
		$list = $this->getListData($where, $fields, $page, $size, ['order_id' => 'desc']);
		if (empty($list)) {
			return [];
		}

		$orderIds = array_column($list, 'order_id');
		$orderProductArr = service('order/Product')->getListData(['order_id' => ['in', $orderIds]]);
		$opIds = array_column($orderProductArr, 'order_product_id');
		$attrArr = !empty($opIds) ? service('order/ProductAttribute')->getListData(['order_product_id' => ['in', $opIds]], 'order_product_id,attr_name,attr_value,attach_id', 0, 0, ['item_id' => 'asc']) : [];

		$attachIdArr = array_values(array_unique(array_filter(array_merge(
			array_column($attrArr, 'attach_id'),
			array_column($orderProductArr, 'attach_id')
		))));
		$attachMap = [];
		if (!empty($attachIdArr)) {
			$rawAttaches = service('attachment/Attachment')->getList(['attach_id' => ['in', $attachIdArr]]);
			$attachMap = array_column($rawAttaches, 'url', 'attach_id');
		}

		$skuIds = array_values(array_unique(array_filter(array_column($orderProductArr, 'sku_id'))));
		$skuSpuMap = [];
		if (!empty($skuIds)) {
			$skuRows = service('product/Sku')->getListData(['sku_id' => ['in', $skuIds]], 'sku_id,spu_id');
			$skuSpuMap = array_column($skuRows, 'spu_id', 'sku_id');
		}

		$attrByOp = [];
		foreach ($attrArr as $value) {
			$opId = (int)$value['order_product_id'];
			$value['attv_name'] = $value['attr_value'] ?? '';
			if (!empty($value['attach_id'])) {
				$value['image'] = $attachMap[$value['attach_id']] ?? '';
			}
			$attrByOp[$opId][] = $value;
		}

		$noImg = siteUrl('image/common/noimg.svg');
		$productsByOrder = [];
		foreach ($orderProductArr as $value) {
			$oid = (int)$value['order_id'];
			$opId = (int)$value['order_product_id'];
			$value['image'] = !empty($value['attach_id']) ? ($attachMap[$value['attach_id']] ?? $noImg) : $noImg;
			$value['attr'] = $attrByOp[$opId] ?? [];
			$value['price'] = sprintf('%.2f', (float)$value['price']);
			$spuId = $skuSpuMap[$value['sku_id']] ?? 0;
			$value['url'] = $spuId ? url('product', ['pid' => $spuId, 'sid' => $value['sku_id']]) : 'javascript:;';
			$productsByOrder[$oid][] = $value;
		}

		$currencyService = service('system/Currency');
		$currencyCodes = array_unique(array_column($list, 'currency'));
		$symbolMap = [];
		foreach ($currencyCodes as $code) {
			$symbolMap[$code] = $currencyService->getSymbolByCode($code) ?: '$';
		}

		foreach ($list as $key => $value) {
			$oid = (int)$value['order_id'];
			$symbol = $symbolMap[$value['currency']] ?? '$';
			$value['order_no'] = $this->getOrderNo($oid, $value['add_time'] ?? '');
			$value['product'] = $productsByOrder[$oid] ?? [];
			$value['status_text'] = $this->getStatusLabel($value['status']);
			$value['currency_symbol'] = $symbol;
			$value['order_total'] = sprintf('%.2f', (float)$value['order_total']);
			$value['order_total_format'] = $symbol . $value['order_total'];
			$value['url'] = url('order/detail', ['id' => $oid]);
			$value['add_time_format'] = !empty($value['add_time']) ? date('M j, Y', strtotime($value['add_time'])) : '';
			$list[$key] = $value;
		}
		return $list;
	}

	public function getStatusLabel($status): string
	{
		$map = [
			$this->getConst('STATUS_CANCEL')      => 'Cancelled',
			$this->getConst('STATUS_WAIT_PAY')    => 'Awaiting Payment',
			$this->getConst('STATUS_PAIED')       => 'Processing',
			$this->getConst('STATUS_SHIPPED')     => 'Shipped',
			$this->getConst('STATUS_FINISHED')    => 'Completed',
			$this->getConst('STATUS_REFUNDING')   => 'Refund in Progress',
			$this->getConst('STATUS_PART_REFUND') => 'Partially Refunded',
			$this->getConst('STATUS_FULL_REFUND') => 'Refunded',
		];
		return $map[(int)$status] ?? 'Processing';
	}

	protected function getappTStatus($status, $lanId)
	{
		return $this->getStatusLabel($status);
	}

	public function getListByKeyword($where, $keyword, $page = 1, $size = 10)
	{
		$keyword = trim((string)$keyword);
		if ($keyword === '') {
			return $this->getList($where, $page, $size);
		}
		$idArr = [];
		$cleanId = preg_replace('/^ORD-\d{8}-0*/i', '', $keyword);
		if (ctype_digit($cleanId) && (int)$cleanId > 0) {
			$idArr[] = (int)$cleanId;
		}
		$prodMatches = service('order/Product')->getListData([
			'name' => ['like', '%' . $keyword . '%'],
		], 'order_id');
		if (!empty($prodMatches)) {
			$idArr = array_merge($idArr, array_column($prodMatches, 'order_id'));
		}
		$idArr = array_values(array_unique(array_map('intval', $idArr)));
		if (empty($idArr)) {
			return [];
		}
		$where['order_id'] = ['in', $idArr];
		return $this->getList($where, $page, $size);
	}
}
