<?php

namespace frame;

class Paginator 
{
	protected $size = 20;
	protected $total = 0;
	protected $current = 1;
	protected $config = [
		'global' => '<nav>
		<ul class="pagination">
			{extra}
			{prev}
			{paging}
			{next}
		</ul>
	</nav>',
		'extra' => '<li class="disabled"><span>共 {total} 条, 每页 {size} 条, 共 {totalPage} 页</span></li>',
		'prev' => [
			'enabled' => '<li><a href="{url}" title="page-prev">{text}</a></li>',
			'disabled' => '<li class="disabled">{text}</li>',
		],
		'next' => [
			'enabled' => '<li><a href="{url}" title="page-next">{text}</a></li>',
			'disabled' => '<li class="disabled">{text}</li>',
		],
		'paging' => '<li><a href="{url}" title="page-{text}">{text}</a></li>',
		'current' => '<li class="active"><span>{text}</span></li>',
		'ellipsis' => '<li class="disabled"><span class="ellipsis">...</span></li>',
		'text_zh' => [
			'prev' => '<span>前一页</span>',
			'next' => '<span>下一页</span>',
		],
		'text' => [
			'prev' => '<span class="iconfont icon-xiangzuo1"></span>',
			'next' => '<span class="iconfont icon-xiangyou1"></span>',
		],
	];

	public function make($size = 0, $total = 0, $showPage = 5)
	{
		$this->setSize($size);
		$this->setTotal($total);
		$this->setCurrent();

		$config = $this->config;
		$isZh = siteId() <= 80;
		$textConf = $isZh ? $config['text_zh'] : $config['text'];

		$totalPage = $this->size > 0 ? (int)ceil($this->total / $this->size) : 0;
		if ($totalPage > 0 && $this->current > $totalPage) {
			$this->current = $totalPage;
		}

		$prev = strtr($config['prev'][$this->current <= 1 ? 'disabled' : 'enabled'], [
			'{url}' => $this->url($this->current - 1),
			'{text}' => $textConf['prev'],
		]);

		$pageStr = '';
		if ($totalPage <= 0) {
			$next = strtr($config['next']['disabled'], ['{url}' => '', '{text}' => $textConf['next']]);
		} else {
			if ($this->current < $totalPage) {
				$next = strtr($config['next']['enabled'], ['{url}' => $this->url($this->current + 1), '{text}' => $textConf['next']]);
			} else {
				$next = strtr($config['next']['disabled'], ['{url}' => '', '{text}' => $textConf['next']]);
			}

			$showPage = max(5, (int)$showPage);
			if ($totalPage <= $showPage) {
				for ($i = 1; $i <= $totalPage; $i++) {
					$pageStr .= strtr($config[$i === $this->current ? 'current' : 'paging'], [
						'{url}' => $this->url($i),
						'{text}' => $i,
					]);
				}
			} else {
				// 固定显示第一页
				$pageStr .= strtr($config[$this->current === 1 ? 'current' : 'paging'], [
					'{url}' => $this->url(1),
					'{text}' => 1,
				]);

				// 计算当前页左右保留的页码范围 (夹在 2 与 $totalPage - 1 之间)
				$radius = max(1, (int)floor(($showPage - 3) / 2));
				$leftPage = max(2, $this->current - $radius);
				$rightPage = min($totalPage - 1, $this->current + $radius);

				if ($this->current <= 1 + $radius) {
					$rightPage = max($rightPage, min($totalPage - 1, 1 + 2 * $radius));
				}
				if ($this->current >= $totalPage - $radius) {
					$leftPage = min($leftPage, max(2, $totalPage - 2 * $radius));
				}

				// 左侧省略号
				if ($leftPage > 2) {
					$pageStr .= $config['ellipsis'];
				}

				// 中间连续页码
				for ($i = $leftPage; $i <= $rightPage; $i++) {
					$pageStr .= strtr($config[$i === $this->current ? 'current' : 'paging'], [
						'{url}' => $this->url($i),
						'{text}' => $i,
					]);
				}

				// 右侧省略号
				if ($rightPage < $totalPage - 1) {
					$pageStr .= $config['ellipsis'];
				}

				// 固定显示末页
				$pageStr .= strtr($config[$this->current === $totalPage ? 'current' : 'paging'], [
					'{url}' => $this->url($totalPage),
					'{text}' => $totalPage,
				]);
			}
		}

		$replace = [
			'{total}' => $this->total,
			'{size}' => $this->size,
			'{current}' => $this->current,
			'{totalPage}' => $totalPage,
			'{prev}' => $prev,
			'{paging}' => $pageStr,
			'{next}' => $next,
			'{extra}' => $isZh ? strtr($config['extra'], ['{total}' => $this->total, '{size}' => $this->size, '{totalPage}' => $totalPage]) : '',
		];
		return strtr($config['global'], $replace);
	}

	protected function url($page)
	{
		return siteId() <= 80 ? adminUrl('', array_merge(iget(), ['page' => $page])) : url('', ['page' => $page]);
	}

	protected function setSize($size)
	{
		$this->size = max(1, (int)$size);
	}

	protected function setTotal($total)
	{
		$this->total = max(0, (int)$total);
	}

	protected function setCurrent()
	{
		$this->current = max(1, (int)iget('page', 1));
	}
}