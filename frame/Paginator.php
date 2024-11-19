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
		'text_zh' => [
			'prev' => '<span>前一页</span>',
			'next' => '<span>下一页</span>',
		],
		'text' => [
			'prev' => '<span class="iconfont icon-xiangzuo1"></span>',
			'next' => '<span class="iconfont icon-xiangyou1"></span>',
		],
	];

	public function make($size=0, $total=0, $showPage=7)
	{
		$this->setSize($size);
		$this->setTotal($total);
		$this->setCurrent();
		
		$config = $this->config;
		$prev = strtr($config['prev'][$this->current==1?'disabled':'enabled'], ['{url}'=>$this->url($this->current-1),'{text}'=>$config[siteId()<=80?'text_zh':'text']['prev']]);
		//总页数
		$pageStr = '';
		$totalPage = ceil($this->total / $this->size);
		if ($totalPage==0) {
			$next = strtr($config['next']['disabled'],['{url}'=>'','{text}'=>$config[siteId()<=80?'text_zh':'text']['next']]);
		} else {
			if ($this->current < $totalPage) {
				$next = strtr($config['next']['enabled'],['{url}'=>$this->url($this->current+1),'{text}'=>$config[siteId()<=80?'text_zh':'text']['next']]);
			} else {
				$next = strtr($config['next']['disabled'],['{url}'=>'','{text}'=>$config[siteId()<=80?'text_zh':'text']['next']]);
			}
			//默认拼接第一页
			$pageStr .= strtr($config[1 == $this->current ? 'current' : 'paging'], [
				'{url}' => $this->url(1),
				'{text}' => 1,
			]);
			if ($totalPage > 1) {
				//计算中间分多少页显示
				$middlePage = ceil($showPage / 2) - 1;
				//计算
				$ldiff = 0;
				$rdiff = 0;
				$leftPage = $this->current - $middlePage;
				$rightPage = $this->current + $middlePage;
				if ($leftPage <= 0) {
					$ldiff = -$leftPage + 1;
				}
				if ($rightPage >= $totalPage) {
					$rdiff = $rightPage - $totalPage;
				}
				$leftPage -= $rdiff;
				$rightPage += $ldiff;
				if ($rightPage >= $totalPage) {
					$rightPage = $totalPage - 1;
				}
				//左侧页码
				if ($leftPage > 2) {
					$pageStr .= strtr($config['paging'], [
						'{url}' => '',
						'{text}' => '...',
					]);
				}
				for ($i=$leftPage; $i<$this->current; $i++) {
					if ($i > 1) {
						$pageStr .= strtr($config[$i == $this->current ? 'current' : 'paging'], [
							'{url}' => $this->url($i),
							'{text}' => $i
						]);
					}
				}
				// 中间当前页
				if ($this->current != 1) {
					$pageStr .= strtr($config['current'], [
						'{url}' => '',
						'{text}' => $this->current,
					]);
				}
				//中间过度...
				for ($i=$this->current+1; $i<=$rightPage; $i++) {
					if ($i < $totalPage) {
						$pageStr .= strtr($config[$i == $this->current ? 'current' : 'paging'], [
							'{url}' => $this->url($i),
							'{text}' => $i
						]);
					}
				}
				if ($totalPage > $rightPage+2) {
					$pageStr .= strtr($config['paging'], [
						'{url}' => '',
						'{text}' => '...',
					]);
				}
				// 拼接上尾页
				if ($this->current != $totalPage) {
					$pageStr .= strtr($config['paging'], [
						'{url}' => $this->url($totalPage),
						'{text}' => $totalPage
					]);
				}
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
			'{extra}' => siteId()<=80 ? strtr($config['extra'], ['{total}'=>$this->total, '{size}'=>$this->size, '{totalPage}'=>$totalPage]) : '',
		];
		return strtr($config['global'], $replace);
	}

	protected function url($page)
	{
		return siteId()<=80?adminUrl('', array_merge(iget(), ['page'=>$page])):url('', ['page'=>$page]);
	}

	protected function setSize($size)
	{
		$this->size = (int) $size;
	}

	protected function setTotal($total)
	{
		$this->total = (int) $total;
	}

	protected function setCurrent()
	{
		$this->current = iget('page', 1);
	}
}