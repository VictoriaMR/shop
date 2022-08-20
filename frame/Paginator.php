<?php

namespace frame;

class Paginator 
{
	protected $size = 20;
	protected $total = 0;
	protected $current = 1;
	protected $adminConfig = [
		'global' => '<nav>
		<ul class="pagination">
			<li class="disabled">
				<span>合计 {total} 条, 每页 {size} 条, 共 {totalPage} 页</span>
			</li>
			{first}
			{prev}
			{paging}
			{next}
			{last}
		</ul>
	</nav>',
		'first' => [
			'enabled' => '<li><a href="{url}">{text}</a></li>',
			'disabled' => '<li class="disabled"><span>{text}</span></li>',
		],
		'prev' => [
			'enabled' => '<li><a href="{url}">{text}</a></li>',
			'disabled' => '<li class="disabled"><span>{text}</span></li>',
		],
		'next' => [
			'enabled' => '<li><a href="{url}">{text}</a></li>',
			'disabled' => '<li class="disabled"><span>{text}</span></li>',
		],
		'last' => [
			'enabled' => '<li><a href="{url}">{text}</a></li>',
			'disabled' => '<li class="disabled"><span>{text}</span></li>',
		],
		'paging' => '<li><a href="{url}">{text}</a></li>',
		'current' => '<li class="active"><span>{text}</span></li>',
		'text' => [
			'first' => '首页',
			'last' => '尾页',
			'prev' => '前一页',
			'next' => '下一页',
		],
	];
	protected $config = [
		'global' => '<nav>
		<ul class="pagination">
			{prev}
			{paging}
			{next}
		</ul>
	</nav>',
		'first' => [
			'enabled' => '<li><a href="{url}">{text}</a></li>',
			'disabled' => '<li class="disabled">{text}</li>',
		],
		'prev' => [
			'enabled' => '<li><a href="{url}">{text}</a></li>',
			'disabled' => '<li class="disabled">{text}</li>',
		],
		'next' => [
			'enabled' => '<li><a href="{url}">{text}</a></li>',
			'disabled' => '<li class="disabled">{text}</li>',
		],
		'last' => [
			'enabled' => '<li><a href="{url}">{text}</a></li>',
			'disabled' => '<li class="disabled">{text}</li>',
		],
		'paging' => '<li><a href="{url}">{text}</a></li>',
		'current' => '<li class="active"><span>{text}</span></li>',
		'text' => [
			'first' => '',
			'last' => '',
			'prev' => '<span class="iconfont icon-xiangzuo1"></span>',
			'next' => '<span class="iconfont icon-xiangyou1"></span>',
		],
	];

	public function make($size=0, $total=0)
	{
		$this->setSize($size);
		$this->setTotal($total);
		$this->setCurrent();
		$config = IS_ADMIN ? $this->adminConfig : $this->config;
		if ($this->current==1) {
			$first = strtr($config['first']['disabled'],['{url}'=>$this->url(1),'{text}'=>$config['text']['first']]);
			$prev = strtr($config['prev']['disabled'],['{url}'=>$this->url($this->current-1),'{text}'=>$config['text']['prev']]);
		} else {
			$first = strtr($config['first']['enabled'],['{url}'=>$this->url(1),'{text}'=>$config['text']['first']]);
			$prev = strtr($config['prev']['enabled'],['{url}'=>$this->url($this->current-1),'{text}'=>$config['text']['prev']]);
		}
		//总页数
		$totalPage = ceil($this->total / $this->size);
		if ($totalPage==0) {
			$next = $next = strtr($config['next']['disabled'],['{url}'=>'','{text}'=>$config['text']['next']]);
			$last = strtr($config['last']['disabled'],['{url}'=>'','{text}'=>$config['text']['last']]);
		} else {
			if ($this->current < $totalPage) {
				$next = strtr($config['next']['enabled'],['{url}'=>$this->url($this->current+1),'{text}'=>$config['text']['next']]);
				$last = strtr($config['last']['enabled'],['{url}'=>$this->url($totalPage),'{text}'=>$config['text']['last']]);
			} else {
				$next = strtr($config['next']['disabled'],['{url}'=>'','{text}'=>$config['text']['next']]);
				$last = strtr($config['last']['disabled'],['{url}'=>'','{text}'=>$config['text']['last']]);
			}
		}

		$pageStr = '';
		if ($this->total > 0) {
			$totalPage = ceil($this->total / $this->size);
			for ($i=1; $i<= $totalPage; $i++) {
				$pageStr .= strtr($config[$i == $this->current ? 'current' : 'paging'], [
					'{url}' => $this->url($i),
					'{text}' => $i
				]);
			}
		}
		$replace = [
			'{total}' => $this->total,
			'{size}' => $this->size,
			'{current}' => $this->current,
			'{totalPage}' => $totalPage ?? 0,
			'{first}' => $first,
			'{prev}' => $prev,
			'{paging}' => $pageStr,
			'{next}' => $next,
			'{last}' => $last,
		];
		return strtr($config['global'], $replace);
	}

	protected function url($page)
	{
		if (IS_ADMIN) {
			$page = $page > 1 ? $page : 1;
			$param = iget();
			$param['page'] = $page;
			return adminUrl('', $param);
		} else {
			return url('', ['page'=>$page]);
		}
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