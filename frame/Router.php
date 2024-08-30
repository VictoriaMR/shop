<?php

namespace frame;

final class Router
{
	private $include_param = ['rid', 'vid', 'aid', 'sort', 'page', 'size'];
	private $include_path = ['c', 'p', 's', 'f', 'g', 'search'];
	private $path_format = [
		'a' => 'Article',
		'c'=> 'Category',
		'p' => 'Product',
		's' => 'Product',
		'f' => 'Faq',
		'g' => 'Faq',
		'search' => 'Search',
	];

	public function analyze($class)
	{
		if (!empty($_SERVER['REQUEST_URI'])) {
			$tempArr = array_reverse(array_filter(explode('-', str_replace('.html', '', ltrim(parse_url($_SERVER['REQUEST_URI'])['path'], '/')))));
			array_shift($_GET);
			foreach ($tempArr as $key=>$value) {
				if ($key%2 == 0) {
					if (is_numeric($value)) {
						$_GET[$tempArr[$key+1]??'id'] = $value;
					} else {
						$tempArr = explode('/', $value);
						$path = ucfirst($tempArr[0]);
						isset($tempArr[1]) && $func = $tempArr[1];
						break;
					}
				}
			}
		}
		return ['class'=>$class, 'path'=>$path??'Index', 'func'=>$func??'index'];
	}

	protected function formatPath($type)
	{
		return $this->path_format[$type]??'';
	}

	public function url($name='', $param=[], $joint=true)
	{
		if ($param) {
			if (is_array($param)) {
				$param = $this->nameFormat(http_build_query($param));
			}
			$name .= ($joint?'-':'/'). $param;
		}
		if ($name) $name .= '.html';
		return domain().$name;
	}

	public function adminUrl($name='', $param=[])
	{
		if (!$name) {
			$name = \App::get('router', 'path');
			if (\App::get('router', 'func') != 'index') {
				$name .= DS.\App::get('router', 'func');
			}
		}
		return $name.($param?'?'.http_build_query($param):'');
	}

	public function nameFormat($name, $param=[])
	{
		if (!$name) return '';
		$name = preg_replace('/[^-A-Za-z0-9\/\s\&\=]/', '', strtolower($name));
		$name = str_replace([' ', '&', '='], '-' , $name);
		$name = preg_replace('/-{1,}/', '-', $name);
		return trim($name, '-');
	}
}