<?php

namespace template\template1\controller\computer;
use app\controller\Base;

class NewIn extends Base
{
	public function index()
	{
		$siteName = \App::get('domain', 'name', 'Shop');
		$lanId = lanId();
		$spuService = service('product/Spu');
		$cateService = service('category/Category');
		$openStatus = $spuService->getConst('STATUS_OPEN');

		// 1. 解析过滤与排序参数
		$cid = max(0, (int)iget('cid', 0));
		$rawSort = strtolower(trim((string)iget('sort', 'newest')));
		$sortMap = [
			'0'          => 'newest',
			'1'          => 'newest',
			'newest'     => 'newest',
			'2'          => 'popular',
			'popular'    => 'popular',
			'3'          => 'price_asc',
			'price_asc'  => 'price_asc',
			'4'          => 'price_desc',
			'price_desc' => 'price_desc',
		];
		$sort = $sortMap[$rawSort] ?? 'newest';

		$rawPrice = strtolower(trim((string)iget('price', 'all')));
		$validPrices = ['all', 'under_120', '120_160', 'over_160'];
		$priceRange = in_array($rawPrice, $validPrices, true) ? $rawPrice : 'all';

		$page = max(1, (int)iget('page', 1));
		$size = 20;

		// 2. 一次性聚合各分类在售商品数量（单次轻量 GROUP BY 查询，避免 N+1）
		$cateCountRows = $spuService->getListData(
			['status' => $openStatus],
			'cate_id, count(*) as cnt',
			0,
			0,
			[],
			'cate_id'
		);
		$directCounts = [];
		$allNewTotal = 0;
		foreach ($cateCountRows as $row) {
			$c = (int)$row['cate_id'];
			$n = (int)$row['cnt'];
			$directCounts[$c] = $n;
			$allNewTotal += $n;
		}

		// 3. 构建顶部分类筛选胶囊 (Category Filter Pills)
		$enNames = $this->getCategoryEnMap();
		$rootCandidates = [
			101 => 'Furniture',
			112 => 'Trending Fashion',
			109 => 'Outdoor & Garden',
			105 => 'Window & Textiles',
			102 => 'Lighting & Lamps',
			113 => 'Personal Care',
			118 => 'Beauty & Care',
		];

		$categoryPills = [
			[
				'cate_id' => 0,
				'name'    => distT('all_new_in'),
				'count'   => $allNewTotal,
				'active'  => ($cid === 0),
				'url'     => url('newIn'),
			],
		];

		$activeCateName = '';
		$selectedCateIds = [];

		foreach ($rootCandidates as $rootId => $rootLabel) {
			$subList = $cateService->sCate($rootId);
			$treeIds = array_merge([$rootId], array_column($subList, 'cate_id'));
			$treeCount = 0;
			foreach ($treeIds as $tid) {
				$treeCount += ($directCounts[(int)$tid] ?? 0);
			}
			if ($treeCount <= 0) {
				continue;
			}
			$isActive = ($cid === $rootId);
			if ($isActive) {
				$activeCateName = $rootLabel;
				$selectedCateIds = $treeIds;
			}
			$categoryPills[] = [
				'cate_id' => $rootId,
				'name'    => $rootLabel,
				'count'   => $treeCount,
				'active'  => $isActive,
				'url'     => url('newIn', ['cid' => $rootId, 'name' => $rootLabel]),
			];
		}

		// 若传入了二级子分类 ID，同样兼容解析
		if ($cid > 0 && empty($selectedCateIds)) {
			$cateInfo = $cateService->getInfo($cid);
			if ($cateInfo) {
				$activeCateName = $enNames[$cid] ?? ($cateInfo['name_en'] ?: $cateInfo['name']);
				$subList = $cateService->sCate($cid);
				$selectedCateIds = array_merge([$cid], array_column($subList, 'cate_id'));
			} else {
				$cid = 0;
			}
		}

		// 4. 组装商品查询条件
		$where = ['status' => $openStatus];
		if (!empty($selectedCateIds)) {
			$where['cate_id'] = count($selectedCateIds) === 1 ? $selectedCateIds[0] : ['in', $selectedCateIds];
		}
		if ($priceRange === 'under_120') {
			$where['min_price'] = ['<=', 120];
		} elseif ($priceRange === '120_160') {
			$where['min_price'] = ['between', [120, 160]];
		} elseif ($priceRange === 'over_160') {
			$where['min_price'] = ['>', 160];
		}

		// 5. 获取筛选后总数与当前页列表
		$total = ($cid === 0 && $priceRange === 'all') ? $allNewTotal : (int)$spuService->getCountData($where);
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
			default:
				$orderBy = ['spu_id' => 'desc'];
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
				$productList[] = $this->formatProductCard($item, $enNames, $idx);
			}
		}

		// 6. 构建价格区间与排序选项数据
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
				'url'    => $this->buildListingUrl($cid, $sort, $pKey, 1),
			];
		}

		$sortLabels = [
			'newest'     => distT('sort_newest'),
			'popular'    => distT('sort_popular'),
			'price_asc'  => distT('sort_price_asc'),
			'price_desc' => distT('sort_price_desc'),
		];
		$sortOptions = [];
		foreach ($sortLabels as $sKey => $sLabel) {
			$sortOptions[] = [
				'key'    => $sKey,
				'label'  => $sLabel,
				'active' => ($sort === $sKey),
				'url'    => $this->buildListingUrl($cid, $sKey, $priceRange, 1),
			];
		}

		// 7. 构建面包屑与分页结构
		$crumbs = [
			['name' => 'Home', 'url' => url()],
			['name' => distT('page_heading'), 'url' => url('newIn')],
		];
		if ($activeCateName !== '') {
			$crumbs[] = [
				'name' => $activeCateName,
				'url'  => $this->buildListingUrl($cid, 'newest', 'all', 1),
			];
		}

		$pagination = $this->buildPagination($total, $page, $size, $cid, $sort, $priceRange);

		// 8. 赋值视图与 SEO 元信息
		$pageHeading = $activeCateName !== '' ? ($activeCateName . ' ' . distT('page_heading')) : distT('page_heading');
		$seoTitle = $pageHeading . ($page > 1 ? ' - Page ' . $page : '') . ' | ' . distT('seo_title_suffix') . ' - ' . $siteName;
		$seoDesc = distT('seo_desc', ['category' => $activeCateName ?: 'fashion, lighting & modern home']);
		$canonicalUrl = $this->buildListingUrl($cid, 'newest', 'all', $page);

		$this->assign('pageHeading', $pageHeading);
		$this->assign('activeCateName', $activeCateName);
		$this->assign('allNewTotal', $allNewTotal);
		$this->assign('total', $total);
		$this->assign('cid', $cid);
		$this->assign('sort', $sort);
		$this->assign('priceRange', $priceRange);
		$this->assign('hasActiveFilter', ($cid > 0 || $priceRange !== 'all' || $sort !== 'newest'));
		$this->assign('resetUrl', url('newIn'));
		$this->assign('crumbs', $crumbs);
		$this->assign('categoryPills', $categoryPills);
		$this->assign('priceFilters', $priceFilters);
		$this->assign('sortOptions', $sortOptions);
		$this->assign('productList', $productList);
		$this->assign('pagination', $pagination);

		$this->assign('_title', $seoTitle);
		$this->assign('_keyword', distT('seo_keyword'));
		$this->assign('_desc', $seoDesc);
		$this->assign('_canonical', $canonicalUrl);
		if (!empty($pagination['prev_url'])) {
			$this->assign('_prevUrl', $pagination['prev_url']);
		}
		if (!empty($pagination['next_url'])) {
			$this->assign('_nextUrl', $pagination['next_url']);
		}
	}

	protected function buildListingUrl(int $cid, string $sort, string $price, int $page): string
	{
		$params = [];
		if ($cid > 0) {
			$params['cid'] = $cid;
			$enNames = $this->getCategoryEnMap();
			if (!empty($enNames[$cid])) {
				$params['name'] = $enNames[$cid];
			}
		}
		if ($sort !== '' && $sort !== 'newest') {
			$params['sort'] = $sort;
		}
		if ($price !== '' && $price !== 'all') {
			$params['price'] = $price;
		}
		if ($page > 1) {
			$params['page'] = $page;
		}
		return url('newIn', $params);
	}

	protected function buildPagination(int $total, int $current, int $size, int $cid, string $sort, string $price): array
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
				'url'     => $this->buildListingUrl($cid, $sort, $price, 1),
			];

			if ($left > 2) {
				$pages[] = ['type' => 'ellipsis'];
			}

			for ($i = $left; $i <= $right; $i++) {
				$pages[] = [
					'type'    => 'page',
					'num'     => $i,
					'current' => ($current === $i),
					'url'     => $this->buildListingUrl($cid, $sort, $price, $i),
				];
			}

			if ($right < $totalPages - 1) {
				$pages[] = ['type' => 'ellipsis'];
			}

			$pages[] = [
				'type'    => 'page',
				'num'     => $totalPages,
				'current' => ($current === $totalPages),
				'url'     => $this->buildListingUrl($cid, $sort, $price, $totalPages),
			];
		}

		return [
			'total'         => $total,
			'size'          => $size,
			'current'       => $current,
			'total_pages'   => $totalPages,
			'showing_start' => $showingStart,
			'showing_end'   => $showingEnd,
			'prev_url'      => $current > 1 ? $this->buildListingUrl($cid, $sort, $price, $current - 1) : '',
			'next_url'      => $current < $totalPages ? $this->buildListingUrl($cid, $sort, $price, $current + 1) : '',
			'pages'         => $pages,
		];
	}

	protected function formatProductCard(array $item, array $enNames, int $index): array
	{
		$item['url'] = url('product', ['pid' => $item['spu_id']]);
		$minPrice = (float)($item['min_price'] ?? 0);
		$origPrice = (float)($item['original_price'] ?? 0);
		$discount = 0;
		if ($origPrice > $minPrice && $origPrice > 0) {
			$discount = (int)round((1 - $minPrice / $origPrice) * 100);
		}
		$item['discount_percent'] = $discount > 0 ? $discount : 20;
		$item['badge'] = !empty($item['is_hot']) ? 'HOT' : 'NEW';
		$cid = (int)($item['cate_id'] ?? 0);
		$item['cate_name'] = $enNames[$cid] ?? 'New Arrival';
		$item['is_eager'] = ($index < 4);
		return $item;
	}

	protected function getCategoryEnMap(): array
	{
		return service('category/Category')->getEnMap();
	}
}
