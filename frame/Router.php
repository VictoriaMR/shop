<?php

namespace frame;

final class Router
{
	protected $funcExcept = ['faq', 'product'];

	public function analyze($class)
	{
		$key = key($_GET);
		$path = 'Index';
		$func = 'index';
		if ($key) {
			$parts = explode('/', trim(str_replace('_html', '', $key), '/'));
			if (!empty($parts[0]) && $parts[0] !== 'null') {
				$path = ucfirst($parts[0]);
			}
			if (!empty($parts[1]) && $parts[1] !== 'null') {
				$func = $parts[1];
			}
			unset($_GET[$key]);
		}
		return ['class'=>$class, 'path'=>$path, 'func'=>$func];
	}

	public function url($name='', $param=[], $joint=true)
	{
		if ($param) {
			if (is_array($param)) {
				$param = http_build_query($param);
				if (in_array($name, $this->funcExcept)) {
					$param = $this->nameFormat($param);
					$name .= ($joint?'-':'/'). $param.'.html';
				} else {
					$name .= '.html?'.$param;
				}
			}
		}
		return 'https://'.($_SERVER['HTTP_HOST'] ?? '').'/'.$name;
	}

	public function adminUrl($name='', $param=[])
	{
		if (!$name) {
			$name = \App::get('router', 'path');
			if (\App::get('router', 'func') != 'index') {
				$name .= '/'.\App::get('router', 'func');
			}
		}
		return '/'.$name.($param?'?'.http_build_query($param):'');
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