<?php

namespace frame;

final class Router
{
	protected $funcExcept = ['faq', 'product'];

	public function analyze($class)
	{		
		if (preg_match('/^\/?([A-Za-z0-9-_]*)\/?([A-Za-z0-9-_]*)/', rtrim(key($_GET), '_html'), $match)) {
			if (!empty($match[1])) {
				$path = ucfirst($match[1]);
			}
			if (!empty($match[2])) {
				if (in_array($match[1], $this->funcExcept)) {
					$_GET['id'] = $match[2];
				} else {
					$func = $match[2];
				}
			}
		}
		return ['class'=>$class, 'path'=>$path??'Index', 'func'=>$func??'index'];
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
		return 'https://'.$_SERVER['HTTP_HOST'].'/'.$name;
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