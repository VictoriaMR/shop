<?php

namespace template\template1\controller\computer;
use app\controller\Base;

class Index extends Base
{
	public function index()
	{
		$siteName = \App::get('domain', 'name', 'Shop');
		$lanId = lanId();
		$spuService = service('product/Spu');

		// 1. 轮播主图 / 促销横幅 (Hero Banner Carousel)
		$bannerList = [
			[
				'tag'         => '2026 NEW COLLECTION',
				'title'       => 'Elevate Your Everyday Style & Modern Living',
				'subtitle'    => 'Discover handpicked fashion apparel, statement lighting, and contemporary home essentials at direct-to-consumer prices.',
				'discount'    => 'UP TO 40% OFF',
				'btn_text'    => 'Shop New Arrivals',
				'btn_url'     => url('newIn'),
				'sub_text'    => 'Explore Categories',
				'sub_url'     => '#section-categories',
				'theme'       => 'theme-slate',
				'badge_title' => 'New Season Drop',
				'badge_sub'   => '150+ Curated Styles',
				'accent_icon' => 'sparkles',
			],
			[
				'tag'         => 'DESIGNER LIGHTING & DECOR',
				'title'       => 'Warm Minimalist Spaces, Crafted to Shine',
				'subtitle'    => 'Transform your living room and bedroom with Nordic ceiling lamps, crystal chandeliers, and artisan home accents.',
				'discount'    => 'FREE SHIPPING OVER $59',
				'btn_text'    => 'Shop Lighting & Home',
				'btn_url'     => url('category', ['cid' => 102, 'name' => 'Lighting']),
				'sub_text'    => 'View Best Sellers',
				'sub_url'     => '#section-bestsellers',
				'theme'       => 'theme-amber',
				'badge_title' => 'Artisan Quality',
				'badge_sub'   => 'Direct Studio Pricing',
				'accent_icon' => 'lamp',
			],
			[
				'tag'         => 'TRENDING APPAREL',
				'title'       => 'Effortless Comfort Meets Streetwear Aesthetics',
				'subtitle'    => 'Breathable cotton tees, vintage oversized knits, and versatile silhouettes tailored for every season.',
				'discount'    => 'BUY 2 GET EXTRA 15% OFF',
				'btn_text'    => 'Shop Trending Picks',
				'btn_url'     => url('category', ['cid' => 112, 'name' => 'Trending Fashion']),
				'sub_text'    => 'Search Catalog',
				'sub_url'     => url('search', ['keyword' => 'shirt']),
				'theme'       => 'theme-emerald',
				'badge_title' => 'Customer Favorites',
				'badge_sub'   => '4.9/5 Average Rating',
				'accent_icon' => 'shirt',
			],
		];

		// 2. 品牌服务保障栏 (Brand Service Guarantee Bar)
		$serviceList = [
			[
				'icon'  => 'truck',
				'title' => 'Free Express Shipping',
				'desc'  => 'Free shipping on orders over $59 with end-to-end tracking',
			],
			[
				'icon'  => 'shield',
				'title' => '100% Secure Checkout',
				'desc'  => '256-bit SSL encryption & trusted global payment methods',
			],
			[
				'icon'  => 'refresh',
				'title' => '30-Day Easy Returns',
				'desc'  => 'Hassle-free return & exchange policy for peace of mind',
			],
			[
				'icon'  => 'headset',
				'title' => '24/7 Dedicated Support',
				'desc'  => 'Responsive customer care team ready to assist anytime',
			],
		];

		// 3. 热门分类导航 (Popular Categories Grid)
		$popularCate = [
			[
				'cate_id'  => 112,
				'name'     => 'Trending Fashion',
				'subtitle' => 'Streetwear & Knitwear',
				'icon'     => 'fashion',
				'color'    => '#eef2ff',
				'accent'   => '#4f46e5',
				'url'      => url('category', ['cid' => 112, 'name' => 'Trending Fashion']),
			],
			[
				'cate_id'  => 101,
				'name'     => 'Modern Furniture',
				'subtitle' => 'Living & Bedroom',
				'icon'     => 'furniture',
				'color'    => '#fef3c7',
				'accent'   => '#d97706',
				'url'      => url('category', ['cid' => 101, 'name' => 'Modern Furniture']),
			],
			[
				'cate_id'  => 102,
				'name'     => 'Designer Lighting',
				'subtitle' => 'Lamps & Chandeliers',
				'icon'     => 'lighting',
				'color'    => '#fce7f3',
				'accent'   => '#db2777',
				'url'      => url('category', ['cid' => 102, 'name' => 'Designer Lighting']),
			],
			[
				'cate_id'  => 238,
				'name'     => 'Tops & Tees',
				'subtitle' => 'Everyday Cotton Basics',
				'icon'     => 'shirt',
				'color'    => '#e0f2fe',
				'accent'   => '#0284c7',
				'url'      => url('category', ['cid' => 238, 'name' => 'Tops & Tees']),
			],
			[
				'cate_id'  => 105,
				'name'     => 'Dresses & Skirts',
				'subtitle' => 'Seasonal Silhouettes',
				'icon'     => 'dress',
				'color'    => '#f3e8ff',
				'accent'   => '#9333ea',
				'url'      => url('category', ['cid' => 105, 'name' => 'Dresses & Skirts']),
			],
			[
				'cate_id'  => 109,
				'name'     => 'Outdoor & Active',
				'subtitle' => 'Leisure & Outerwear',
				'icon'     => 'outdoor',
				'color'    => '#dcfce7',
				'accent'   => '#16a34a',
				'url'      => url('category', ['cid' => 109, 'name' => 'Outdoor & Active']),
			],
			[
				'cate_id'  => 104,
				'name'     => 'Home Decor',
				'subtitle' => 'Accents & Aesthetics',
				'icon'     => 'decor',
				'color'    => '#ffedd5',
				'accent'   => '#ea580c',
				'url'      => url('category', ['cid' => 104, 'name' => 'Home Decor']),
			],
			[
				'cate_id'  => 118,
				'name'     => 'Beauty & Care',
				'subtitle' => 'Personal Essentials',
				'icon'     => 'beauty',
				'color'    => '#ffe4e6',
				'accent'   => '#e11d48',
				'url'      => url('category', ['cid' => 118, 'name' => 'Beauty & Care']),
			],
		];

		// 4. 新品上架展示区 (New Arrivals - 8 items)
		$openStatus = $spuService->getConst('STATUS_OPEN');
		$newArrivals = $spuService->getList(
			['status' => $openStatus],
			'spu_id,cate_id,attach_id,min_price,max_price,visit_total,free_ship,is_hot',
			1,
			8,
			['spu_id' => 'desc'],
			$lanId,
			true,
			true
		);
		$newArrivalIds = [];
		foreach ($newArrivals as $k => $item) {
			$newArrivalIds[] = (int)$item['spu_id'];
			$newArrivals[$k] = $this->enrichProductCard($item, 'NEW');
		}

		// 5. 热销 / 推荐商品列表 (Best Sellers - 10 items, no pagination, distinct from New Arrivals)
		$bestWhere = ['status' => $openStatus];
		if (!empty($newArrivalIds)) {
			$bestWhere['spu_id'] = ['not in', $newArrivalIds];
		}
		$bestSeller = $spuService->getList(
			$bestWhere,
			'spu_id,cate_id,attach_id,min_price,max_price,visit_total,free_ship,is_hot',
			1,
			10,
			['visit_total' => 'desc', 'is_hot' => 'desc', 'spu_id' => 'asc'],
			$lanId,
			true,
			true
		);
		foreach ($bestSeller as $k => $item) {
			$bestSeller[$k] = $this->enrichProductCard($item, 'HOT');
		}

		$this->assign('bannerList', $bannerList);
		$this->assign('serviceList', $serviceList);
		$this->assign('popularCate', $popularCate);
		$this->assign('newArrivals', $newArrivals);
		$this->assign('bestSeller', $bestSeller);

		$this->assign('_title', appT('_title') . ' - ' . $siteName);
		$this->assign('_keyword', appT('_keyword'));
		$this->assign('_desc', appT('_desc'));
	}

	protected function enrichProductCard(array $item, string $defaultBadge = ''): array
	{
		$item['url'] = url('product', ['pid' => $item['spu_id']]);
		$minPrice = (float)($item['min_price'] ?? 0);
		$origPrice = (float)($item['original_price'] ?? 0);
		$discount = 0;
		if ($origPrice > $minPrice && $origPrice > 0) {
			$discount = (int)round((1 - $minPrice / $origPrice) * 100);
		}
		$item['discount_percent'] = $discount > 0 ? $discount : 20;
		$item['badge'] = !empty($item['is_hot']) ? 'HOT' : $defaultBadge;
		return $item;
	}
}
