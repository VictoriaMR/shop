<?php

namespace frame;

class Paginator 
{
	protected $size = 20;
	protected $total = 0;
	protected $current = 1;
	protected $config = [
		'global' => '<nav><ul class="pagination"><li  class="disabled"><span>合计 {total} 条, 每页 {size} 条, 共 {totalPage} 页</span></li>{first}{prev}{paging}{next}{last}</ul></nav>',
		'first' => [
			'enabled' => '<li><a href="{url}">{text}</a></li>',
			'disabled' => '<li  class="disabled"><span>{text}</span></li>',
		],
		'prev' => [
			'enabled' => '<li><a href="{url}">{text}</a></li>',
			'disabled' => '<li  class="disabled"><span>{text}</span></li>',
		],
		'next' => [
			'enabled' => '<li><a href="{url}">{text}</a></li>',
			'disabled' => '<li  class="disabled"><span>{text}</span></li>',
		],
		'last' => [
			'enabled' => '<li><a href="{url}">{text}</a></li>',
			'disabled' => '<li  class="disabled"><span>{text}</span></li>',
		],
		'paging' => '<li><a href="{url}">{text}</a></li>',
		'current' => '<li class="active"><span>{text}</span></li>',
		'text' => [
			'first' => '首页',
			'last'  => '尾页',
			'prev'  => '前一页',
			'next'  => '后一页',
		],
	];

	public function make($size=null, $total=null, $current=null)
	{
		$this->setSize($size);
		$this->setTotal($total);
		$this->setCurrent($current);
		if ($this->current==1) {
			$first = strtr($this->config['first']['disabled'],['{url}'=>$this->url(1),'{text}'=>$this->config['text']['first']]);
			$prev = strtr($this->config['prev']['disabled'],['{url}'=>$this->url($this->current-1),'{text}'=>$this->config['text']['prev']]);
		} else {
			$first = strtr($this->config['first']['enabled'],['{url}'=>$this->url(1),'{text}'=>$this->config['text']['first']]);
			$prev = strtr($this->config['prev']['enabled'],['{url}'=>$this->url($this->current-1),'{text}'=>$this->config['text']['prev']]);
		}
		//总页数
		$totalPage = ceil($this->total / $this->size);
		if ($totalPage==0) {
			$next = $next = strtr($this->config['next']['disabled'],['{url}'=>'','{text}'=>$this->config['text']['next']]);
			$last = strtr($this->config['last']['disabled'],['{url}'=>'','{text}'=>$this->config['text']['last']]);
		} else {
			if ($this->current < $totalPage) {
				$next = strtr($this->config['next']['enabled'],['{url}'=>$this->url($this->current+1),'{text}'=>$this->config['text']['next']]);
				$last = strtr($this->config['last']['enabled'],['{url}'=>$this->url($totalPage),'{text}'=>$this->config['text']['last']]);
			} else {
				$next = strtr($this->config['next']['disabled'],['{url}'=>'','{text}'=>$this->config['text']['next']]);
				$last = strtr($this->config['last']['disabled'],['{url}'=>'','{text}'=>$this->config['text']['last']]);
			}
		}

		$pageStr = '';
		if ($this->total > 0) {
			$totalPage = ceil($this->total / $this->size);
			for ($i=1; $i<= $totalPage; $i++) {
				$pageStr .= strtr($this->config[$i == $this->current ? 'current' : 'paging'], [
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
		return strtr($this->config['global'], $replace);
	}

	protected function url($page)
	{
		$page = $page > 1 ? $page : 1;
		$param = iget();
		$param['page'] = $page;
		return url().'?'.http_build_query($param);
	}

	protected function setSize($size)
	{
		$this->size = (int) $size;
	}

	protected function setTotal($total)
	{
		$this->total = (int) $total;
	}

	protected function setCurrent($current)
	{
		if (is_null($current)) {
			$current = iget('page');
		}
		$current = (int) $current;
		$this->current = $current > 0 ? $current : 1;
	}
}