<?php

namespace template\template1\controller\computer;
use app\controller\Base;

class Category extends Base
{
	public function index()
	{
		$siteName = \App::get('domain', 'name', 'Shop');
		$lanId = lanId();
		$spuService = service('product/Spu');
		$cateService = service('category/Category');
		$openStatus = $spuService->getConst('STATUS_OPEN');
		$enMap = $cateService->getEnMap();

		// 1. 解析请求参数
		$cid = max(0, (int)iget('cid', 0));
		$rawSort = strtolower(trim((string)iget('sort', 'featured')));
		$sortMap = [
			'0'          => 'featured',
			'1'          => 'featured',
			'featured'   => 'featured',
			'2'          => 'popular',
			'popular'    => 'popular',
			'3'          => 'price_asc',
			'price_asc'  => 'price_asc',
			'4'          => 'price_desc',
			'price_desc' => 'price_desc',
			'5'          => 'newest',
			'newest'     => 'newest',
		];
		$sort = $sortMap[$rawSort] ?? 'featured';

		$rawPrice = strtolower(trim((string)iget('price', 'all')));
		$validPrices = ['all', 'under_120', '120_160', 'over_160'];
		$priceRange = in_array($rawPrice, $validPrices, true) ? $rawPrice : 'all';

		$page = max(1, (int)iget('page', 1));
		$size = 20;

		// 2. 单次聚合统计各分类在售商品数
		$cateCountRows = $spuService->getListData(
			['status' => $openStatus],
			'cate_id, count(*) as cnt',
			0,
			0,
			[],
			'cate_id'
		);
		$directCounts = [];
		$catalogTotal = 0;
		foreach ($cateCountRows as $row) {
			$c = (int)$row['cate_id'];
			$n = (int)$row['cnt'];
			$directCounts[$c] = $n;
			$catalogTotal += $n;
		}

		// 3. 解析当前分类及父级面包屑链路
		$cateInfo = $cid > 0 ? $cateService->getInfo($cid) : false;
		if ($cid > 0 && !$cateInfo) {
			$cid = 0;
		}

		$parentChain = $cid > 0 ? array_reverse($cateService->pCate($cid)) : [];
		$rootCateId = !empty($parentChain) ? (int)$parentChain[0]['cate_id'] : 0;
		$activeCateName = $cid > 0 ? ($enMap[$cid] ?? $cateInfo['name']) : distT('all_categories_heading');
		$rootCateName = $rootCateId > 0 ? ($enMap[$rootCateId] ?? $activeCateName) : '';

		// 4. 构建一级核心分类胶囊导航 (.filter-pills-wrap 固定伪静态链接)
		$departmentRoots = [
			112 => 'Trending Picks',
			101 => 'Furniture',
			109 => 'Outdoor & Garden',
			105 => 'Window & Textiles',
			102 => 'Lighting',
			113 => 'Personal Care',
			118 => 'Beauty & Salon',
		];

		$categoryPills = [
			[
				'cate_id' => 0,
				'name'    => distT('all_categories_pill'),
				'count'   => $catalogTotal,
				'active'  => ($cid === 0),
				'url'     => url('category'),
			],
		];

		$rootTreeMap = [];
		foreach ($departmentRoots as $rId => $rLabel) {
			$subRows = $cateService->sCate($rId);
			$tIds = array_merge([$rId], array_column($subRows, 'cate_id'));
			$rootTreeMap[$rId] = $tIds;
			$tCount = 0;
			foreach ($tIds as $tid) {
				$tCount += ($directCounts[(int)$tid] ?? 0);
			}
			if ($tCount <= 0) {
				continue;
			}
			$categoryPills[] = [
				'cate_id' => $rId,
				'name'    => $rLabel,
				'count'   => $tCount,
				'active'  => ($rootCateId === $rId),
				'url'     => url('category', ['cid' => $rId, 'name' => $rLabel]),
			];
		}

		// 5. 构建当前一级分类下的二级子分类导航条 (Subcategory Quick-Filter Bar)
		$subCategories = [];
		if ($rootCateId > 0) {
			$children = $cateService->getChildren($rootCateId, true);
			if (!empty($children)) {
				$subCategories[] = [
					'cate_id' => $rootCateId,
					'name'    => 'All ' . $rootCateName,
					'count'   => 0,
					'active'  => ($cid === $rootCateId),
					'url'     => url('category', ['cid' => $rootCateId, 'name' => $rootCateName]),
				];
				foreach ($children as $ch) {
					if (empty($ch['is_show'])) {
						continue;
					}
					$chId = (int)$ch['cate_id'];
					$chName = $enMap[$chId] ?? $ch['name'];
					$chSub = $cateService->sCate($chId);
					$chTreeIds = array_merge([$chId], array_column($chSub, 'cate_id'));
					$chCount = 0;
					foreach ($chTreeIds as $tid) {
						$chCount += ($directCounts[(int)$tid] ?? 0);
					}
					$isChActive = ($cid === $chId || in_array($cid, $chTreeIds, true));
					$subCategories[] = [
						'cate_id' => $chId,
						'name'    => $chName,
						'count'   => $chCount,
						'active'  => $isChActive,
						'url'     => url('category', ['cid' => $chId, 'name' => $chName]),
					];
				}
			}
		}

		// 6. 解析当前分类的商品 ID 集合（若子分类暂无独立挂载商品，则平滑回退至所属主分类商品池）
		$where = ['status' => $openStatus];
		if ($cid > 0) {
			$subList = $cateService->sCate($cid);
			$selectedIds = array_merge([$cid], array_column($subList, 'cate_id'));
			$selectedCount = 0;
			foreach ($selectedIds as $sid) {
				$selectedCount += ($directCounts[(int)$sid] ?? 0);
			}
			if ($selectedCount === 0 && $rootCateId > 0) {
				$rootIds = $rootTreeMap[$rootCateId] ?? array_merge([$rootCateId], array_column($cateService->sCate($rootCateId), 'cate_id'));
				$rootCount = 0;
				foreach ($rootIds as $rid) {
					$rootCount += ($directCounts[(int)$rid] ?? 0);
				}
				if ($rootCount > 0) {
					$selectedIds = $rootIds;
				}
			}
			$where['cate_id'] = count($selectedIds) === 1 ? $selectedIds[0] : ['in', $selectedIds];
		}

		if ($priceRange === 'under_120') {
			$where['min_price'] = ['<=', 120];
		} elseif ($priceRange === '120_160') {
			$where['min_price'] = ['between', [120, 160]];
		} elseif ($priceRange === 'over_160') {
			$where['min_price'] = ['>', 160];
		}

		// 7. 查询商品总数与分页列表
		$total = ($cid === 0 && $priceRange === 'all') ? $catalogTotal : (int)$spuService->getCountData($where);
		$totalPages = $total > 0 ? (int)ceil($total / $size) : 1;
		if ($page > $totalPages) {
			$page = $totalPages;
		}

		switch ($sort) {
			case 'popular':
				$orderBy = ['visit_total' => 'desc', 'spu_id' => 'desc'];
				break;
			case 'price_asc':
				$orderBy = ['min_price' => 'asc', 'spu_id' => 'desc'];
				break;
			case 'price_desc':
				$orderBy = ['min_price' => 'desc', 'spu_id' => 'desc'];
				break;
			case 'newest':
				$orderBy = ['spu_id' => 'desc'];
				break;
			default:
				$orderBy = ['is_hot' => 'desc', 'visit_total' => 'desc', 'spu_id' => 'desc'];
				break;
		}

		$productList = [];
		if ($total > 0) {
			$rawList = $spuService->getList(
				$where,
				'spu_id,cate_id,attach_id,min_price,max_price,visit_total,free_ship,is_hot',
				$page,
				$size,
				$orderBy,
				$lanId,
				true,
				true
			);
			foreach ($rawList as $idx => $item) {
				$productList[] = $this->formatProductCard($item, $enMap, $idx);
			}
		}

		// 若当前分类为空，提供精选热销推荐商品兜底
		$recommendList = [];
		if (empty($productList)) {
			$recRaw = $spuService->getList(
				['status' => $openStatus],
				'spu_id,cate_id,attach_id,min_price,max_price,visit_total,free_ship,is_hot',
				1,
				8,
				['visit_total' => 'desc', 'spu_id' => 'desc'],
				$lanId,
				true,
				true
			);
			foreach ($recRaw as $idx => $item) {
				$recommendList[] = $this->formatProductCard($item, $enMap, $idx);
			}
		}

		// 8. 组装价格筛选与排序按钮组
		$priceLabels = [
			'all'       => distT('price_all'),
			'under_120' => distT('price_under_120'),
			'120_160'   => distT('price_120_160'),
			'over_160'  => distT('price_over_160'),
		];
		$priceFilters = [];
		foreach ($priceLabels as $pKey => $pLabel) {
			$priceFilters[] = [
				'key'    => $pKey,
				'label'  => $pLabel,
				'active' => ($priceRange === $pKey),
				'url'    => $this->buildCategoryUrl($cid, $activeCateName, $sort, $pKey, 1),
			];
		}

		$sortLabels = [
			'featured'   => distT('sort_featured'),
			'popular'    => distT('sort_popular'),
			'newest'     => distT('sort_newest'),
			'price_asc'  => distT('sort_price_asc'),
			'price_desc' => distT('sort_price_desc'),
		];
		$sortOptions = [];
		foreach ($sortLabels as $sKey => $sLabel) {
			$sortOptions[] = [
				'key'    => $sKey,
				'label'  => $sLabel,
				'active' => ($sort === $sKey),
				'url'    => $this->buildCategoryUrl($cid, $activeCateName, $sKey, $priceRange, 1),
			];
		}

		// 9. 构建面包屑与分页结构
		$crumbs = [
			['name' => 'Home', 'url' => url()],
			['name' => distT('all_categories_pill'), 'url' => url('category')],
		];
		foreach ($parentChain as $node) {
			$nodeId = (int)$node['cate_id'];
			$nodeName = $enMap[$nodeId] ?? $node['name'];
			$crumbs[] = [
				'name' => $nodeName,
				'url'  => url('category', ['cid' => $nodeId, 'name' => $nodeName]),
			];
		}

		$pagination = $this->buildPagination($total, $page, $size, $cid, $activeCateName, $sort, $priceRange);
		$cateMeta = $this->getCategoryBannerMeta($rootCateId ?: $cid, $activeCateName);

		// 10. 赋值视图与 SEO 元数据
		$seoTitle = $activeCateName . ($page > 1 ? ' - Page ' . $page : '') . ' | ' . distT('seo_title_suffix') . ' - ' . $siteName;
		$seoDesc = $cateMeta['desc'];
		$seoKeyword = strtolower($activeCateName) . ', ' . distT('seo_keyword');
		$canonicalUrl = $this->buildCategoryUrl($cid, $activeCateName, 'featured', 'all', $page);
		$baseCateUrl = $cid > 0 ? url('category', ['cid' => $cid, 'name' => $activeCateName]) : url('category');

		$this->assign('cid', $cid);
		$this->assign('rootCateId', $rootCateId);
		$this->assign('activeCateName', $activeCateName);
		$this->assign('rootCateName', $rootCateName);
		$this->assign('cateMeta', $cateMeta);
		$this->assign('catalogTotal', $catalogTotal);
		$this->assign('total', $total);
		$this->assign('sort', $sort);
		$this->assign('priceRange', $priceRange);
		$this->assign('hasActiveFilter', ($priceRange !== 'all' || $sort !== 'featured'));
		$this->assign('resetUrl', $baseCateUrl);
		$this->assign('baseCateUrl', $baseCateUrl);
		$this->assign('crumbs', $crumbs);
		$this->assign('categoryPills', $categoryPills);
		$this->assign('subCategories', $subCategories);
		$this->assign('priceFilters', $priceFilters);
		$this->assign('sortOptions', $sortOptions);
		$this->assign('productList', $productList);
		$this->assign('recommendList', $recommendList);
		$this->assign('pagination', $pagination);

		$this->assign('_title', $seoTitle);
		$this->assign('_keyword', $seoKeyword);
		$this->assign('_desc', $seoDesc);
		$this->assign('_canonical', $canonicalUrl);
		if (!empty($pagination['prev_url'])) {
			$this->assign('_prevUrl', $pagination['prev_url']);
		}
		if (!empty($pagination['next_url'])) {
			$this->assign('_nextUrl', $pagination['next_url']);
		}
	}

	protected function buildCategoryUrl(int $cid, string $cateName, string $sort, string $price, int $page): string
	{
		$params = [];
		if ($cid > 0) {
			$params['cid'] = $cid;
			if ($cateName !== '') {
				$params['name'] = $cateName;
			}
		}
		if ($sort !== '' && $sort !== 'featured') {
			$params['sort'] = $sort;
		}
		if ($price !== '' && $price !== 'all') {
			$params['price'] = $price;
		}
		if ($page > 1) {
			$params['page'] = $page;
		}
		return url('category', $params);
	}

	protected function buildPagination(int $total, int $current, int $size, int $cid, string $cateName, string $sort, string $price): array
	{
		$totalPages = $total > 0 ? (int)ceil($total / $size) : 0;
		$showingStart = $total > 0 ? (($current - 1) * $size + 1) : 0;
		$showingEnd = min($total, $current * $size);

		$pages = [];
		if ($totalPages > 1) {
			$radius = 2;
			$left = max(2, $current - $radius);
			$right = min($totalPages - 1, $current + $radius);

			$pages[] = [
				'type'    => 'page',
				'num'     => 1,
				'current' => ($current === 1),
				'url'     => $this->buildCategoryUrl($cid, $cateName, $sort, $price, 1),
			];

			if ($left > 2) {
				$pages[] = ['type' => 'ellipsis'];
			}

			for ($i = $left; $i <= $right; $i++) {
				$pages[] = [
					'type'    => 'page',
					'num'     => $i,
					'current' => ($current === $i),
					'url'     => $this->buildCategoryUrl($cid, $cateName, $sort, $price, $i),
				];
			}

			if ($right < $totalPages - 1) {
				$pages[] = ['type' => 'ellipsis'];
			}

			$pages[] = [
				'type'    => 'page',
				'num'     => $totalPages,
				'current' => ($current === $totalPages),
				'url'     => $this->buildCategoryUrl($cid, $cateName, $sort, $price, $totalPages),
			];
		}

		return [
			'total'         => $total,
			'size'          => $size,
			'current'       => $current,
			'total_pages'   => $totalPages,
			'showing_start' => $showingStart,
			'showing_end'   => $showingEnd,
			'prev_url'      => $current > 1 ? $this->buildCategoryUrl($cid, $cateName, $sort, $price, $current - 1) : '',
			'next_url'      => $current < $totalPages ? $this->buildCategoryUrl($cid, $cateName, $sort, $price, $current + 1) : '',
			'pages'         => $pages,
		];
	}

	protected function formatProductCard(array $item, array $enMap, int $index): array
	{
		$item['url'] = url('product', ['pid' => $item['spu_id']]);
		$minPrice = (float)($item['min_price'] ?? 0);
		$origPrice = (float)($item['original_price'] ?? 0);
		$discount = 0;
		if ($origPrice > $minPrice && $origPrice > 0) {
			$discount = (int)round((1 - $minPrice / $origPrice) * 100);
		}
		$item['discount_percent'] = $discount > 0 ? $discount : 20;
		$item['badge'] = !empty($item['is_hot']) ? 'HOT' : '';
		$c = (int)($item['cate_id'] ?? 0);
		$item['cate_name'] = $enMap[$c] ?? 'Collection';
		$item['is_eager'] = ($index < 4);
		return $item;
	}

	protected function getCategoryBannerMeta(int $rootId, string $cateName): array
	{
		$themes = [
			112 => [
				'eyebrow' => 'VIRAL & TRENDING COLLECTION',
				'desc'    => 'Shop our most-wanted streetwear, cotton tees, statement knitwear, and viral lifestyle favorites loved by thousands of customers.',
				'theme'   => 'theme-indigo',
				'icon'    => 'fashion',
			],
			101 => [
				'eyebrow' => 'MODERN LIVING & FURNITURE',
				'desc'    => 'Elevate your dining room, living space, and home office with artisan-crafted tables, ergonomic seating, and contemporary storage.',
				'theme'   => 'theme-amber',
				'icon'    => 'furniture',
			],
			102 => [
				'eyebrow' => 'ARCHITECTURAL & DESIGNER LIGHTING',
				'desc'    => 'Illuminate indoor and outdoor spaces with warm minimalist wall sconces, crystal chandeliers, and energy-saving solar fixtures.',
				'theme'   => 'theme-rose',
				'icon'    => 'lighting',
			],
			105 => [
				'eyebrow' => 'WINDOW TREATMENTS & TEXTILES',
				'desc'    => 'Tailored blackout drapes, sheer curtains, and modern window shades designed for privacy, comfort, and timeless elegance.',
				'theme'   => 'theme-purple',
				'icon'    => 'textile',
			],
			109 => [
				'eyebrow' => 'OUTDOOR, PATIO & GARDEN',
				'desc'    => 'Weather-resistant patio furniture, outdoor shades, and leisure essentials built to transform your backyard and garden.',
				'theme'   => 'theme-emerald',
				'icon'    => 'outdoor',
			],
			113 => [
				'eyebrow' => 'PERSONAL CARE & WELLNESS',
				'desc'    => 'Reliable daily wellness essentials, protective gear, and personal care supplies crafted for comfort and peace of mind.',
				'theme'   => 'theme-sky',
				'icon'    => 'care',
			],
			118 => [
				'eyebrow' => 'BEAUTY & SALON ESSENTIALS',
				'desc'    => 'Professional-grade beauty care, styling stations, and spa accessories tailored for effortless daily routines.',
				'theme'   => 'theme-rose',
				'icon'    => 'beauty',
			],
		];

		if (isset($themes[$rootId])) {
			return $themes[$rootId];
		}

		return [
			'eyebrow' => 'CURATED DEPARTMENT CATALOG',
			'desc'    => 'Explore our handpicked ' . $cateName . ' collection featuring direct studio pricing, free shipping over $59, and 30-day easy returns.',
			'theme'   => 'theme-indigo',
			'icon'    => 'default',
		];
	}
}
