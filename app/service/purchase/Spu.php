<?php 

namespace app\service\purchase;
use app\service\Base;

class Spu extends Base
{
	public function addUrl($url)
	{
		$channelId = purchase()->channel()->getChannelId($url);
		if (!$channelId) {
			return false;
		}
		$itemId = purchase()->channel()->getItemId($url);
		if (!$itemId) {
			return false;
		}
		$data = [
			'channel_id' => $channelId,
			'item_id' => $itemId,
		];
		if ($this->getCountData($data)) return true;
		return $this->insert($data);
	}

	public function getStatusList()
	{
		return [
			$this->getConst('STATUS_NORMAL') => '未使用',
			$this->getConst('STATUS_SET') => '已上传',
			$this->getConst('STATUS_USED') => '已使用',
			$this->getConst('STATUS_SPU') => '转化中',
			$this->getConst('STATUS_FAIL') => '已废弃',
		];
	}

	public function url(int $channelId, int $itemId)
	{
		switch ($channelId) {
			case 6051:
				return 'https://item.taobao.com/item.htm?id='.$itemId;
			case 6052:
				return 'https://detail.tmall.com/item.htm?id='.$itemId;
			case 6053:
				return 'https://detail.1688.com/offer/'.$itemId.'.html';
		}
	}

	/**
	 * 1688/淘宝常见无用批发/跨境描述属性黑名单关键词
	 */
	const JUNK_DESC_KEYWORDS = [
		'货号', '品牌', '主要下游平台', '主要销售地区', '有可授权的自有品牌',
		'是否跨境出口专供货源', '是否专利货源', '专利类型', '专利号', '是否进口',
		'跨境包裹重量', '单位重量', '产品体积', '是否一般纳税人', '是否有3D模型',
		'加工定制', '加印LOGO', '贸易属性', '外贸类型', '质检报告编号', '订货号',
		'上市年份', '货源类别', '是否库存', '库存类型', '最快出货时间', '是否支持代发',
		'一件代发', '售后服务', '发票', '销售序列号', '生产编号', '阿里旺旺',
	];

	/**
	 * 引流/无效SKU规格关键词
	 */
	const JUNK_SKU_KEYWORDS = [
		'定金', '专拍', '补差价', '勿拍', '邮费链接', '运费链接', '样品专拍', '定制专拍',
	];

	/**
	 * 单值规格常见无意义占位值
	 */
	const GENERIC_SINGLE_VALUES = [
		'默认', '均码', '通用', '标准', '常规', '普通', '无', '单品', '通码', '统一规格',
	];

	/**
	 * 清洗属性值中的促销后缀噪音
	 */
	public function cleanAttrText(string $text): string
	{
		$text = trim($text);
		if ($text === '') {
			return '';
		}
		$cleaned = preg_replace('/(【[^】]*(现货|速发|收藏|送|拍|发|只换不修|亏本|秒杀|特价|包邮|加购|立减|保价|质保|优先)[^】]*】|\([^)]*(现货|速发|收藏|送|包邮|加购|优先)[^)]*\)|（[^）]*(现货|速发|收藏|送|包邮|加购|优先)[^）]*）|\[[^\]]*(现货|速发|收藏|送|包邮|加购|优先)[^\]]*\])/u', '', $text);
		$cleaned = trim((string)$cleaned);
		return $cleaned !== '' ? $cleaned : $text;
	}

	/**
	 * 判断描述属性名是否属于垃圾/冗余属性
	 */
	public function isJunkDescName(string $name): bool
	{
		$name = trim($name);
		if ($name === '') {
			return true;
		}
		foreach (self::JUNK_DESC_KEYWORDS as $kw) {
			if (mb_strpos($name, $kw) !== false) {
				return true;
			}
		}
		return false;
	}

	/**
	 * 判断SKU规格是否为定金/补差价等引流规格
	 */
	public function isJunkSkuSpec(array $pvs): bool
	{
		foreach ($pvs as $pv) {
			$pvStr = (string)$pv;
			foreach (self::JUNK_SKU_KEYWORDS as $kw) {
				if (mb_strpos($pvStr, $kw) !== false) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * 判断单个属性值是否为无意义的通用占位词
	 */
	public function isGenericSingleValue(string $val): bool
	{
		$val = trim($val);
		return in_array($val, self::GENERIC_SINGLE_VALUES, true);
	}

	/**
	 * 根据同供应商店铺历史入库记录自动推荐分类ID
	 */
	public function recommendCateId(int $purchaseShopId): int
	{
		if ($purchaseShopId <= 0) {
			return 0;
		}
		$lastSpuData = service('product/SpuData')->getListData(
			['shop_id' => $purchaseShopId],
			'spu_id',
			1,
			1,
			['spu_id' => 'desc']
		);
		$lastSpuId = (int)($lastSpuData[0]['spu_id'] ?? 0);
		if ($lastSpuId <= 0) {
			return 0;
		}
		$spuRow = service('product/Spu')->loadData($lastSpuId, 'cate_id');
		return (int)($spuRow['cate_id'] ?? 0);
	}

	/**
	 * 获取下一个待编辑的采购产品ID（实现流水线连续作业）
	 */
	public function getNextPendingId(int $currentId): int
	{
		$statusSet = $this->getConst('STATUS_SET');
		$nextRows = $this->getListData(
			[
				'status'          => $statusSet,
				'purchase_spu_id' => ['<', $currentId],
			],
			'purchase_spu_id',
			1,
			1,
			['purchase_spu_id' => 'desc']
		);
		if (!empty($nextRows[0]['purchase_spu_id'])) {
			return (int)$nextRows[0]['purchase_spu_id'];
		}
		$fallbackRows = $this->getListData(
			[
				'status'          => $statusSet,
				'purchase_spu_id' => ['<>', $currentId],
			],
			'purchase_spu_id',
			1,
			1,
			['purchase_spu_id' => 'desc']
		);
		return (int)($fallbackRows[0]['purchase_spu_id'] ?? 0);
	}

	public function getInfo(int $id)
	{
		$info = $this->loadData($id);
		if (empty($info)) {
			return false;
		}
		$result = $this->getResult($info['channel_id'], $info['item_id']);
		if (empty($result) || !is_array($result)) {
			return false;
		}
		$merged = array_merge($info, $result);
		$merged['pdt_picture'] = (!empty($merged['pdt_picture']) && is_array($merged['pdt_picture'])) ? array_values($merged['pdt_picture']) : [];
		$merged['desc_picture'] = (!empty($merged['desc_picture']) && is_array($merged['desc_picture'])) ? array_values($merged['desc_picture']) : [];
		$merged['attr'] = (!empty($merged['attr']) && is_array($merged['attr'])) ? $merged['attr'] : [];
		$merged['detail'] = (!empty($merged['detail']) && is_array($merged['detail'])) ? $merged['detail'] : [];
		$merged['sku'] = (!empty($merged['sku']) && is_array($merged['sku'])) ? $merged['sku'] : [];

		// 预处理属性与提取属性图片映射
		$attrImgMap = [];
		foreach ($merged['attr'] as $ak => $av) {
			$vals = (!empty($av['value']) && is_array($av['value'])) ? $av['value'] : [];
			$valCount = count($vals);
			$isDefaultSingle = false;
			foreach ($vals as $vk => $vv) {
				$vName = (string)($vv['name'] ?? '');
				$merged['attr'][$ak]['value'][$vk]['clean_name'] = $this->cleanAttrText($vName);
				if (!empty($vv['img']) && $vName !== '') {
					$attrImgMap[$vName] = $vv['img'];
				}
				if ($valCount === 1 && $this->isGenericSingleValue($vName)) {
					$isDefaultSingle = true;
				}
			}
			$merged['attr'][$ak]['is_default_single'] = $isDefaultSingle;
		}

		// 预处理SKU：自动绑定属性图、识别引流/无库存SKU
		foreach ($merged['sku'] as $k => $skuItem) {
			$pvs = (!empty($skuItem['pvs']) && is_array($skuItem['pvs'])) ? $skuItem['pvs'] : [];
			$skuImg = trim((string)($skuItem['img'] ?? ''));
			if ($skuImg === '' && !empty($pvs)) {
				foreach ($pvs as $pv) {
					if (!empty($attrImgMap[$pv])) {
						$skuImg = $attrImgMap[$pv];
						break;
					}
				}
			}
			$price = (float)($skuItem['price'] ?? 0);
			$stock = (int)($skuItem['stock'] ?? 0);
			$merged['sku'][$k]['pvs'] = $pvs;
			$merged['sku'][$k]['img'] = $skuImg;
			$merged['sku'][$k]['price'] = $skuItem['price'] ?? 0;
			$merged['sku'][$k]['stock'] = $skuItem['stock'] ?? 0;
			$merged['sku'][$k]['is_junk'] = ($stock <= 0 || $price <= 0 || $this->isJunkSkuSpec($pvs));
		}

		// 预处理描述属性：标记黑名单垃圾属性
		foreach ($merged['detail'] as $dk => $dItem) {
			$merged['detail'][$dk]['is_junk'] = $this->isJunkDescName((string)($dItem['name'] ?? ''));
		}

		// 若无分类，自动根据同供应商店铺历史记录推荐分类
		if (empty($merged['cate_id']) && !empty($info['purchase_shop_id'])) {
			$recCateId = $this->recommendCateId((int)$info['purchase_shop_id']);
			if ($recCateId > 0) {
				$merged['cate_id'] = $recCateId;
				$merged['is_recommended_cate'] = true;
			}
		}

		$merged['url'] = $merged['url'] ?? $this->url((int)$info['channel_id'], (int)$info['item_id']);
		$merged['post_fee'] = $merged['post_fee'] ?? 0;
		$merged['save_data'] = $this->getResult($info['channel_id'], $info['item_id'], 'save_data') ?: [];
		return $merged;
	}

	public function saveResult(int $channelId, int $itemId, array $data, $type='source_data')
	{
		$data = isJson($data);
		if (!$data) {
			return false;
		}
		$where = [
			'channel_id' => $channelId,
			'item_id' => $itemId,
		];
		if (purchase()->result()->getCountData($where) > 0) {
			return purchase()->result()->updateData($where, $where+[$type=>$data]);
		} else {
			$where[$type] = $data;
			return purchase()->result()->insert($where);
		}
	}

	public function getResult(int $channelId, int $itemId, $type='source_data')
	{
		$info = purchase()->result()->loadData(['channel_id' => $channelId, 'item_id' => $itemId]);
		if (empty($info) || empty($info[$type])) {
			return false;
		}
		return isArray($info[$type]);
	}

	public function updateTitle($channelId, int $itemId)
	{
		$rst = $this->getResult($channelId, $itemId);
		if (empty($rst['name'])) {
			return false;
		}
		$this->updateData(['channel_id'=>$channelId, 'item_id'=>$itemId], ['name'=>$rst['name']]);
		return $rst['name'];
	}

	public function getVolume($str)
	{
		$arr = [
			'length' => 0,
			'width' => 0,
			'height' => 0,
		];
		if ($str) {
			$str = str_replace(['*','x','X','-','&',';',':'], ',', $str);
			$str = explode(',', $str);
			$arr['length'] = $str[0] ?? 0;
			$arr['width'] = $str[1] ?? 0;
			$arr['height'] = $str[2] ?? 0;
		}
		return $arr;
	}

	protected $lastError = '';

	public function getLastError(): string
	{
		return $this->lastError;
	}

	public function operateSpu(int $purchaseSpuId = 0)
	{
		@set_time_limit(0);
		$this->lastError = '';
		$where = $purchaseSpuId > 0
			? ['purchase_spu_id' => $purchaseSpuId]
			: ['status' => $this->getConst('STATUS_SPU')];
		$info = $this->loadData($where);
		if (empty($info)) {
			$this->lastError = '未找到待转化的采购产品记录';
			return false;
		}
		$data = $this->getResult($info['channel_id'], $info['item_id'], 'save_data');
		if (empty($data) || empty($data['sku']) || !is_array($data['sku'])) {
			$this->lastError = '未找到已保存的SKU配置数据';
			return false;
		}
		$sourceData = $this->getResult($info['channel_id'], $info['item_id'], 'source_data') ?: [];

		$attrName = service('attr/Name');
		$attrValue = service('attr/Value');
		$descName = service('desc/Name');
		$descValue = service('desc/Value');
		$file = service('tool/File');
		$productSpu = service('product/Spu');
		$productSpuData = service('product/SpuData');
		$productSku = service('product/Sku');
		$productSkuData = service('product/SkuData');

		$postFee = (float)($data['post_fee'] ?? 0);
		$cateId = (int)($data['cate_id'] ?? 0);

		$attrNameArr = [];
		$attrValueArr = [];
		$mainImgArr = (!empty($data['main_img']) && is_array($data['main_img'])) ? array_values(array_filter($data['main_img'])) : [];
		$spuCoverUrl = !empty($data['spu_image']) ? $data['spu_image'] : ($mainImgArr[0] ?? '');

		$productImages = $mainImgArr;
		if ($spuCoverUrl !== '') {
			$productImages[] = $spuCoverUrl;
		}

		$validSkus = [];
		$priceArr = [];
		foreach ($data['sku'] as $key => $value) {
			if (isset($value['checked']) && !$value['checked']) {
				continue;
			}
			$costPrice = (float)($value['price'] ?? 0);
			if ($costPrice <= 0) {
				continue;
			}
			$validSkus[$key] = $value;
			if (!empty($value['img'])) {
				$productImages[] = $value['img'];
			}
			if (!empty($value['attr']) && is_array($value['attr'])) {
				foreach ($value['attr'] as $ak => $av) {
					$akTrim = strtoupper(strTrim($ak));
					$avTrim = strtoupper(strTrim(is_array($av) ? ($av['text'] ?? '') : $av));
					if ($akTrim !== '' && $avTrim !== '') {
						$attrNameArr[] = $akTrim;
						$attrValueArr[] = $avTrim;
					}
				}
			}
			$priceArr[] = $productSpu->getPrice($costPrice + $postFee);
		}

		if (empty($validSkus) || empty($priceArr)) {
			$this->lastError = '没有可入库的有效SKU（进货价需大于0）';
			return false;
		}

		// 上传主图与SKU图片
		$productImages = array_values(array_unique(array_filter($productImages)));
		$uploadedProductImgs = !empty($productImages) ? ($file->uploadUrlImage($productImages, 'product') ?: []) : [];

		// 写入属性名和属性值字典
		$attrNameMap = !empty($attrNameArr) ? ($attrName->addNotExist($attrNameArr) ?: []) : [];
		$attrValueMap = !empty($attrValueArr) ? ($attrValue->addNotExist($attrValueArr) ?: []) : [];

		// 写入描述名和描述值字典
		$descNameMap = [];
		$descValueMap = [];
		$descPairs = [];
		if (!empty($data['desc']['name']) && is_array($data['desc']['name'])) {
			$dNames = [];
			$dValues = [];
			foreach ($data['desc']['name'] as $k => $n) {
				$nClean = strtoupper(strTrim($n));
				$vClean = strtoupper(strTrim($data['desc']['value'][$k] ?? ''));
				if ($nClean !== '' && $vClean !== '') {
					$dNames[] = $nClean;
					$dValues[] = $vClean;
					$descPairs[] = ['name' => $nClean, 'value' => $vClean];
				}
			}
			if (!empty($dNames)) {
				$descNameMap = $descName->addNotExist($dNames) ?: [];
				$descValueMap = $descValue->addNotExist($dValues) ?: [];
			}
		}

		$supplierName = $sourceData['domain'] ?? '';
		if ($supplierName === '') {
			$channelMap = [6051 => 'taobao', 6052 => 'tmall', 6053 => '1688'];
			$supplierName = $channelMap[$info['channel_id']] ?? '';
		}

		$existingSpuData = $productSpuData->loadData([
			'item_id'  => $info['item_id'],
			'supplier' => $supplierName,
		], 'spu_id');

		$spuAttachId = $uploadedProductImgs[$spuCoverUrl] ?? ( reset($uploadedProductImgs) ?: 0 );
		$spuFields = [
			'status'    => 0,
			'cate_id'   => $cateId,
			'attach_id' => (int)$spuAttachId,
			'min_price' => min($priceArr),
			'max_price' => max($priceArr),
		];

		$existingPurSkus = purchase()->sku()->getListData(
			['purchase_spu_id' => $info['purchase_spu_id']],
			'unique_id,sku_id'
		);
		$existingSkuMap = array_column($existingPurSkus ?: [], 'sku_id', 'unique_id');

		$this->start();
		try {
			if (empty($existingSpuData['spu_id'])) {
				$spuId = $productSpu->insertGetId($spuFields);
				$firstSku = reset($validSkus);
				$spuVol = trim(($firstSku['length'] ?? '') . ',' . ($firstSku['width'] ?? '') . ',' . ($firstSku['hight'] ?? ''), ',');
				$productSpuData->insert([
					'spu_id'    => $spuId,
					'supplier'  => $supplierName,
					'item_id'   => $info['item_id'],
					'item_url'  => $sourceData['url'] ?? $this->url((int)$info['channel_id'], (int)$info['item_id']),
					'shop_id'   => (int)$info['purchase_shop_id'],
					'post_fee'  => $postFee,
					'weight'    => (int)($firstSku['weight'] ?? ($sourceData['weight'] ?? 0)),
					'volume'    => $spuVol ?: ($sourceData['volume'] ?? ''),
				]);
				service('product/Language')->insert([
					'spu_id' => $spuId,
					'lan_id' => 0,
					'name'   => strTrim($data['spu_name'] ?? $info['name']),
				]);
			} else {
				$spuId = (int)$existingSpuData['spu_id'];
				$productSpu->updateData($spuId, $spuFields);
				service('product/Language')->updateData(['spu_id' => $spuId, 'lan_id' => 0], [
					'name' => strTrim($data['spu_name'] ?? $info['name']),
				]);
			}

			// SPU 主图集
			if (!empty($mainImgArr)) {
				$spuImgInsert = [];
				$sort = 1;
				foreach ($mainImgArr as $imgUrl) {
					if (isset($uploadedProductImgs[$imgUrl])) {
						$spuImgInsert[] = [
							'spu_id'    => $spuId,
							'attach_id' => (int)$uploadedProductImgs[$imgUrl],
							'sort'      => $sort++,
						];
					}
				}
				if (!empty($spuImgInsert)) {
					service('product/SpuImage')->addSpuImage($spuImgInsert);
				}
			}

			// SKU 列表写入
			foreach ($validSkus as $skuKey => $skuVal) {
				$costPrice = (float)$skuVal['price'];
				$salePrice = $productSpu->getPrice($costPrice + $postFee);
				$stock = (int)($skuVal['stock'] ?? 0);
				$skuAttachId = (!empty($skuVal['img']) && isset($uploadedProductImgs[$skuVal['img']])) ? (int)$uploadedProductImgs[$skuVal['img']] : 0;
				$skuVol = trim(($skuVal['length'] ?? '') . ',' . ($skuVal['width'] ?? '') . ',' . ($skuVal['hight'] ?? ''), ',');

				$skuFields = [
					'spu_id'    => $spuId,
					'status'    => $stock > 0 ? 1 : 0,
					'attach_id' => $skuAttachId,
					'stock'     => $stock,
					'price'     => $salePrice,
					'weight'    => (int)($skuVal['weight'] ?? 0),
					'volume'    => $skuVol,
				];
				$existSkuId = (int)($existingSkuMap[(string)$skuKey] ?? 0);
				if ($existSkuId > 0 && $productSku->getCountData(['sku_id' => $existSkuId])) {
					$skuId = $existSkuId;
					$productSku->updateData($skuId, $skuFields);
					$productSkuData->updateData($skuId, [
						'cost_price' => $costPrice,
					]);
				} else {
					$skuId = $productSku->insertGetId($skuFields);
					$productSkuData->insert([
						'sku_id'     => $skuId,
						'item_id'    => (int)$skuKey,
						'cost_price' => $costPrice,
					]);
					purchase()->sku()->updateData([
						'purchase_spu_id' => $info['purchase_spu_id'],
						'unique_id'       => (string)$skuKey,
					], ['sku_id' => $skuId]);
				}

				if (!empty($skuVal['attr']) && is_array($skuVal['attr'])) {
					$attrInsert = [];
					$aSort = 1;
					foreach ($skuVal['attr'] as $ak => $av) {
						$akUpper = strtoupper(strTrim($ak));
						$avUpper = strtoupper(strTrim(is_array($av) ? ($av['text'] ?? '') : $av));
						if (isset($attrNameMap[$akUpper]) && isset($attrValueMap[$avUpper])) {
							$attrInsert[] = [
								'sku_id'    => $skuId,
								'attrn_id'  => (int)$attrNameMap[$akUpper],
								'attrv_id'  => (int)$attrValueMap[$avUpper],
								'attach_id' => $skuAttachId,
								'sort'      => $aSort++,
							];
						}
					}
					if (!empty($attrInsert)) {
						service('product/AttrUsed')->addAttrUsed($skuId, $attrInsert);
					}
				}
			}

			// SPU 详情介绍图片
			if (!empty($data['desc_img']) && is_array($data['desc_img'])) {
				$descImgUrls = array_values(array_unique(array_filter($data['desc_img'])));
				$uploadedDescImgs = !empty($descImgUrls) ? ($file->uploadUrlImage($descImgUrls, 'introduce', false) ?: []) : [];
				if (!empty($uploadedDescImgs)) {
					$introInsert = [];
					$iSort = 1;
					foreach ($uploadedDescImgs as $attachId) {
						$introInsert[$spuId . '-' . $attachId] = [
							'spu_id'    => $spuId,
							'attach_id' => (int)$attachId,
							'sort'      => $iSort++,
						];
					}
					service('product/IntroUsed')->addIntroUsed($spuId, $introInsert);
				}
			}

			// SPU 描述文本属性
			if (!empty($descPairs)) {
				$descInsert = [];
				$dSort = 1;
				foreach ($descPairs as $pair) {
					$nameId = $descNameMap[$pair['name']] ?? 0;
					$valId = $descValueMap[$pair['value']] ?? 0;
					if ($nameId > 0 && $valId > 0) {
						$descInsert[$nameId . '-' . $valId] = [
							'spu_id'   => $spuId,
							'descn_id' => (int)$nameId,
							'descv_id' => (int)$valId,
							'sort'     => $dSort++,
						];
					}
				}
				if (!empty($descInsert)) {
					service('product/DescUsed')->addDescUsed($spuId, $descInsert);
				}
			}

			// 更新采购SPU状态为已使用
			$this->updateData($info['purchase_spu_id'], [
				'status' => $this->getConst('STATUS_USED'),
			]);
			$this->commit();
			return $spuId;
		} catch (\Throwable $e) {
			$this->rollback();
			$this->lastError = $e->getMessage();
			return false;
		}
	}
}