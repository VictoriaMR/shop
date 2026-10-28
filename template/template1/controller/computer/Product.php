<?php

namespace template\template1\controller\computer;
use app\controller\Base;

class Product extends Base
{
	public function index()
	{
		$siteName = \App::get('domain', 'name', 'Shop');
		$lanId = lanId();
		$spuService = service('product/Spu');
		$cateService = service('category/Category');
		$enMap = $cateService->getEnMap();

		$spuId = (int)iget('pid', (int)iget('p', 0));
		$skuId = (int)iget('sid', (int)iget('s', 0));

		if ($spuId <= 0 && $skuId > 0) {
			$skuRow = service('product/Sku')->loadData(['sku_id' => $skuId], 'spu_id');
			$spuId = (int)($skuRow['spu_id'] ?? 0);
		}

		$info = $spuId > 0 ? $spuService->getInfoCache($spuId, $lanId) : [];

		// 构建面包屑导航
		$crumbs = [];
		$cateName = '';
		if (!empty($info['cate_id'])) {
			$cateChain = array_reverse($cateService->pCate((int)$info['cate_id']));
			foreach ($cateChain as $cRow) {
				if (!empty($cRow['status']) && !empty($cRow['is_show'])) {
					$cId = (int)$cRow['cate_id'];
					$cLabel = $enMap[$cId] ?? $cRow['name'];
					$cateName = $cLabel;
					$crumbs[] = [
						'name' => $cLabel,
						'url'  => url('category', ['cid' => $cId, 'name' => $cLabel]),
					];
				}
			}
		}

		$activeSku = [];
		$activeSkuId = 0;
		$selectedAttvByAttrn = [];
		$galleryImages = [];
		$totalStock = 0;
		$saleTotal = 0;
		$discountPercent = 20;

		if (!empty($info) && !empty($info['sku'])) {
			if ($skuId > 0 && isset($info['sku'][$skuId])) {
				$activeSku = $info['sku'][$skuId];
				$activeSkuId = $skuId;
			} else {
				foreach ($info['sku'] as $sKey => $sRow) {
					if ((int)$sRow['stock'] > 0) {
						$activeSku = $sRow;
						$activeSkuId = (int)$sKey;
						break;
					}
				}
				if (empty($activeSku)) {
					$activeSku = reset($info['sku']);
					$activeSkuId = (int)key($info['sku']);
				}
			}

			// 提取当前选中 SKU 的属性映射 (attrn_id => attrv_id)
			$activeAttvList = $info['skuAttv'][$activeSkuId] ?? [];
			foreach (($info['attrMap'] ?? []) as $nid => $vidList) {
				foreach ($vidList as $vid) {
					if (in_array($vid, $activeAttvList)) {
						$selectedAttvByAttrn[(int)$nid] = (int)$vid;
						break;
					}
				}
			}

			// 汇总图集
			$rawGallery = array_merge(
				[!empty($activeSku['image']) ? $activeSku['image'] : ($info['image'] ?? '')],
				$info['image_list'] ?? [],
				array_values($info['attvImage'] ?? [])
			);
			foreach ($rawGallery as $gImg) {
				if (!empty($gImg) && !in_array($gImg, $galleryImages, true)) {
					$galleryImages[] = $gImg;
				}
			}
			if (empty($galleryImages)) {
				$galleryImages[] = siteUrl('image/common/noimg.svg');
			}

			$stocks = array_column($info['sku'], 'stock');
			$totalStock = !empty($stocks) ? (int)max($stocks) : 0;
			$saleTotal = (int)array_sum(array_column($info['sku'], 'sale_total'));

			$curPrice = (float)($activeSku['price'] ?? $info['min_price'] ?? 0);
			$origPrice = (float)($activeSku['original_price'] ?? $info['original_price'] ?? 0);
			if ($origPrice > $curPrice && $origPrice > 0) {
				$discountPercent = (int)round((1 - $curPrice / $origPrice) * 100);
			}
		}

		$isLiked = ($spuId > 0 && userId() > 0) ? service('member/Collect')->isCollect($spuId) : false;

		// 同类相关推荐商品 (Related Products - 8 items)
		$relatedWhere = [
			'status' => $spuService->getConst('STATUS_OPEN'),
		];
		if ($spuId > 0) {
			$relatedWhere['spu_id'] = ['<>', $spuId];
		}
		if (!empty($info['cate_id'])) {
			$relatedWhere['cate_id'] = (int)$info['cate_id'];
		}
		$relatedRaw = $spuService->getList(
			$relatedWhere,
			'spu_id,cate_id,attach_id,min_price,max_price,visit_total,free_ship,is_hot',
			1,
			8,
			['visit_total' => 'desc', 'spu_id' => 'desc'],
			$lanId,
			true,
			true
		);
		if (count($relatedRaw) < 4 && !empty($info['cate_id'])) {
			unset($relatedWhere['cate_id']);
			$relatedRaw = $spuService->getList(
				$relatedWhere,
				'spu_id,cate_id,attach_id,min_price,max_price,visit_total,free_ship,is_hot',
				1,
				8,
				['visit_total' => 'desc', 'spu_id' => 'desc'],
				$lanId,
				true,
				true
			);
		}
		$relatedProducts = [];
		foreach ($relatedRaw as $rItem) {
			$rItem['url'] = url('product', ['pid' => $rItem['spu_id']]);
			$rCid = (int)$rItem['cate_id'];
			$rItem['cate_name'] = $enMap[$rCid] ?? '';
			$minP = (float)($rItem['min_price'] ?? 0);
			$origP = (float)($rItem['original_price'] ?? 0);
			$rItem['discount_percent'] = ($origP > $minP && $origP > 0) ? (int)round((1 - $minP / $origP) * 100) : 20;
			$relatedProducts[] = $rItem;
		}

		// 前端 SKU 快速查找映射表 (attrn_id:attrv_id;... => sku 对象)
		$skuFrontendMap = [];
		if (!empty($info['skuMap']) && !empty($info['sku'])) {
			foreach ($info['skuMap'] as $comboKey => $sId) {
				if (isset($info['sku'][$sId])) {
					$sObj = $info['sku'][$sId];
					$minP = (float)$sObj['price'];
					$origP = (float)$sObj['original_price'];
					$sObj['discount_percent'] = ($origP > $minP && $origP > 0) ? (int)round((1 - $minP / $origP) * 100) : 20;
					$skuFrontendMap[$comboKey] = $sObj;
				}
			}
		}

		$pageTitle = !empty($info['name']) ? $info['name'] : 'Product Details';
		$seoDesc = $pageTitle . (!empty($cateName) ? (' in ' . $cateName) : '') . ' - Shop online at ' . $siteName . ' with free express shipping over $59 and 30-day buyer protection.';

		$this->assign('spuId', $spuId);
		$this->assign('skuId', $activeSkuId);
		$this->assign('info', $info);
		$this->assign('activeSku', $activeSku);
		$this->assign('selectedAttvByAttrn', $selectedAttvByAttrn);
		$this->assign('galleryImages', $galleryImages);
		$this->assign('totalStock', $totalStock);
		$this->assign('saleTotal', $saleTotal);
		$this->assign('discountPercent', $discountPercent);
		$this->assign('isLiked', $isLiked);
		$this->assign('crumbs', $crumbs);
		$this->assign('cateName', $cateName);
		$this->assign('relatedProducts', $relatedProducts);
		$this->assign('skuFrontendMap', $skuFrontendMap);

		$this->assign('_title', $pageTitle . ' - ' . $siteName);
		$this->assign('_keyword', $pageTitle . ', ' . $cateName . ', ' . $siteName);
		$this->assign('_desc', $seoDesc);
		$this->assign('_canonical', $spuId > 0 ? url('product', ['pid' => $spuId]) : url('product'));
		$this->assign('_og_type', 'product');
		if (!empty($galleryImages[0])) {
			$this->assign('_og_image', $galleryImages[0]);
		}
	}
}
