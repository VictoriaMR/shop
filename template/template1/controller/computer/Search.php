<?php

namespace template\template1\controller\computer;
use app\controller\Base;

class Search extends Base
{
	public function index()
	{
		$siteName = \App::get('domain', 'name', 'Shop');
		$lanId = lanId();
		$spuService = service('product/Spu');
		$cateService = service('category/Category');
		$openStatus = $spuService->getConst('STATUS_OPEN');
		$enMap = $cateService->getEnMap();

		$keyword = trim((string)iget('keyword', ''));
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

		$where = ['status' => $openStatus];
		if ($keyword !== '') {
			$matchedSpuIds = $spuService->getSpuIdByKeyword($keyword);
			$where['spu_id'] = !empty($matchedSpuIds) ? ['in', $matchedSpuIds] : 0;
		}

		$siteCateIds = $cateService->getSiteCateIds();
		if (!empty($siteCateIds)) {
			$where['cate_id'] = ['in', $siteCateIds];
		}

		$rate = $this->getCurrencyRate();
		if ($priceRange === 'under_120') {
			$where['min_price'] = ['<', round(120 * $rate, 2)];
		} elseif ($priceRange === '120_160') {
			$where['min_price'] = ['between', [round(120 * $rate, 2), round(160 * $rate, 2)]];
		} elseif ($priceRange === 'over_160') {
			$where['min_price'] = ['>', round(160 * $rate, 2)];
		}

		switch ($sort) {
			case 'popular':
				$orderBy = ['visit_total' => 'desc', 'is_hot' => 'desc', 'spu_id' => 'desc'];
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
			case 'featured':
			default:
				$orderBy = ['is_hot' => 'desc', 'visit_total' => 'desc', 'spu_id' => 'desc'];
				break;
		}

		$total = (int)$spuService->getCountData($where);
		$totalPages = $total > 0 ? (int)ceil($total / $size) : 1;
		if ($page > $totalPages) {
			$page = $totalPages;
		}

		$productList = [];
		$recommendList = [];
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
				$cId = (int)$item['cate_id'];
				$item['cate_name'] = $enMap[$cId] ?? '';
				$productList[] = $this->formatProductCard($item, $idx < 4);
			}
		} else {
			$recRaw = $spuService->getList(
				['status' => $openStatus],
				'spu_id,cate_id,attach_id,min_price,max_price,visit_total,free_ship,is_hot',
				1,
				8,
				['visit_total' => 'desc', 'is_hot' => 'desc', 'spu_id' => 'desc'],
				$lanId,
				true,
				true
			);
			foreach ($recRaw as $idx => $item) {
				$cId = (int)$item['cate_id'];
				$item['cate_name'] = $enMap[$cId] ?? '';
				$recommendList[] = $this->formatProductCard($item, false);
			}
		}

		$sortOptions = [
			[
				'key'    => 'featured',
				'label'  => distT('sort_featured'),
				'active' => ($sort === 'featured'),
				'url'    => $this->buildFilterUrl($keyword, 'featured', $priceRange, 1),
			],
			[
				'key'    => 'popular',
				'label'  => distT('sort_popular'),
				'active' => ($sort === 'popular'),
				'url'    => $this->buildFilterUrl($keyword, 'popular', $priceRange, 1),
			],
			[
				'key'    => 'newest',
				'label'  => distT('sort_newest'),
				'active' => ($sort === 'newest'),
				'url'    => $this->buildFilterUrl($keyword, 'newest', $priceRange, 1),
			],
			[
				'key'    => 'price_asc',
				'label'  => distT('sort_price_asc'),
				'active' => ($sort === 'price_asc'),
				'url'    => $this->buildFilterUrl($keyword, 'price_asc', $priceRange, 1),
			],
			[
				'key'    => 'price_desc',
				'label'  => distT('sort_price_desc'),
				'active' => ($sort === 'price_desc'),
				'url'    => $this->buildFilterUrl($keyword, 'price_desc', $priceRange, 1),
			],
		];

		$priceFilters = [
			[
				'key'    => 'all',
				'label'  => distT('price_all'),
				'active' => ($priceRange === 'all'),
				'url'    => $this->buildFilterUrl($keyword, $sort, 'all', 1),
			],
			[
				'key'    => 'under_120',
				'label'  => distT('price_under_120'),
				'active' => ($priceRange === 'under_120'),
				'url'    => $this->buildFilterUrl($keyword, $sort, 'under_120', 1),
			],
			[
				'key'    => '120_160',
				'label'  => distT('price_120_160'),
				'active' => ($priceRange === '120_160'),
				'url'    => $this->buildFilterUrl($keyword, $sort, '120_160', 1),
			],
			[
				'key'    => 'over_160',
				'label'  => distT('price_over_160'),
				'active' => ($priceRange === 'over_160'),
				'url'    => $this->buildFilterUrl($keyword, $sort, 'over_160', 1),
			],
		];

		$pagination = $this->buildPagination($page, $totalPages, $total, $size, $keyword, $sort, $priceRange);
		$hasActiveFilter = ($priceRange !== 'all' || $sort !== 'featured');
		$resetUrl = $this->buildFilterUrl($keyword, 'featured', 'all', 1);

		$seoTitle = $keyword !== ''
			? distT('seo_title', ['{keyword}' => $keyword]) . ' - ' . $siteName
			: distT('seo_title_default') . ' - ' . $siteName;

		$this->assign('keyword', $keyword);
		$this->assign('sort', $sort);
		$this->assign('priceRange', $priceRange);
		$this->assign('page', $page);
		$this->assign('size', $size);
		$this->assign('total', $total);
		$this->assign('totalPages', $totalPages);
		$this->assign('productList', $productList);
		$this->assign('recommendList', $recommendList);
		$this->assign('sortOptions', $sortOptions);
		$this->assign('priceFilters', $priceFilters);
		$this->assign('pagination', $pagination);
		$this->assign('hasActiveFilter', $hasActiveFilter);
		$this->assign('resetUrl', $resetUrl);
		$this->assign('_title', $seoTitle);
		$this->assign('_keyword', distT('seo_keyword'));
		$this->assign('_desc', distT('seo_desc'));
		$this->assign('_canonical', $keyword !== '' ? url('search', ['keyword' => $keyword]) : url('search'));
	}

	protected function getCurrencyRate(): float
	{
		$info = service('system/Currency')->getInfoCache(currencyId());
		$rate = (float)($info['rate'] ?? 6.6814);
		return $rate > 0 ? $rate : 6.6814;
	}

	protected function formatProductCard(array $item, bool $eagerLoad = false): array
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
		$item['is_eager'] = $eagerLoad;
		return $item;
	}

	protected function buildFilterUrl(string $keyword, string $sort = 'featured', string $price = 'all', int $page = 1): string
	{
		$params = [];
		if ($keyword !== '') {
			$params['keyword'] = $keyword;
		}
		if ($sort !== 'featured') {
			$params['sort'] = $sort;
		}
		if ($price !== 'all') {
			$params['price'] = $price;
		}
		if ($page > 1) {
			$params['page'] = $page;
		}
		return url('search', $params);
	}

	protected function buildPagination(int $page, int $totalPages, int $total, int $size, string $keyword, string $sort, string $price): array
	{
		$showingStart = $total > 0 ? (($page - 1) * $size + 1) : 0;
		$showingEnd = min($total, $page * $size);
		$pages = [];
		if ($totalPages > 1) {
			$window = 2;
			$start = max(1, $page - $window);
			$end = min($totalPages, $page + $window);
			if ($start > 1) {
				$pages[] = ['type' => 'page', 'num' => 1, 'url' => $this->buildFilterUrl($keyword, $sort, $price, 1), 'current' => ($page === 1)];
				if ($start > 2) {
					$pages[] = ['type' => 'ellipsis'];
				}
			}
			for ($i = $start; $i <= $end; $i++) {
				$pages[] = [
					'type'    => 'page',
					'num'     => $i,
					'url'     => $this->buildFilterUrl($keyword, $sort, $price, $i),
					'current' => ($i === $page),
				];
			}
			if ($end < $totalPages) {
				if ($end < $totalPages - 1) {
					$pages[] = ['type' => 'ellipsis'];
				}
				$pages[] = [
					'type'    => 'page',
					'num'     => $totalPages,
					'url'     => $this->buildFilterUrl($keyword, $sort, $price, $totalPages),
					'current' => ($page === $totalPages),
				];
			}
		}

		return [
			'current'       => $page,
			'total_pages'   => $totalPages,
			'total'         => $total,
			'showing_start' => $showingStart,
			'showing_end'   => $showingEnd,
			'has_prev'      => ($page > 1),
			'prev_url'      => $page > 1 ? $this->buildFilterUrl($keyword, $sort, $price, $page - 1) : '',
			'has_next'      => ($page < $totalPages),
			'next_url'      => $page < $totalPages ? $this->buildFilterUrl($keyword, $sort, $price, $page + 1) : '',
			'pages'         => $pages,
		];
	}
}
