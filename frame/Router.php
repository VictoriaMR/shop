<?php

namespace frame;

final class Router
{
	private $include_param = ['rid', 'vid', 'sort', 'page', 'size'];
	private $include_path = ['c', 'p', 's', 'f'];
	private $path_format = [
		'c'=> 'Category',
		'p' => 'Product',
		's' => 'Product',
		'f' => 'Faq',
	];

	public function analyze()
	{
		array_shift($_GET);
		unset($_GET['/']);
		$pathInfo = pathinfo($_SERVER['REQUEST_URI']);
		$name = array_reverse(explode('-', explode('?', pathinfo($_SERVER['REQUEST_URI'])['filename'])[0]));
		$path = trim($pathInfo['dirname'], DIRECTORY_SEPARATOR);
		$pathInfo['filename'] = explode('?', $pathInfo['filename'])[0];
		if ($path) {
			$path = ucfirst(explode('?', trim($path, '/'))[0]);
			$func = $pathInfo['filename'];
		} else {
			$path = $pathInfo['filename'] ? ucfirst($pathInfo['filename']) : 'Index';
			$func = 'index';
		}
		if (strpos($pathInfo['filename'], '-') !== false) {
			$pathInfo = array_reverse(explode('-', $pathInfo['filename']));
			$tempPath = '';
			foreach ($pathInfo as $key=>$value) {
				if (!isset($_GET[$value]) && in_array($value, $this->include_param)) {
					$_GET[$value] = $pathInfo[$key-1] ?? '';
				}
				if (!isset($_GET[$value.'id']) && in_array($value, $this->include_path)) {
					$tempPath = $this->formatPath($value);
					$_GET[$value.'id'] = $pathInfo[$key-1] ?? '';
				}
			}
			if ($tempPath) {
				$path = $tempPath;
			} else {
				throw new \Exception('analyze router failed!', 1);
			}
		}
		return ['path'=>$path?:'Index', 'func'=>$func?:'index'];
	}

	protected function formatPath($type)
	{
		return $this->path_format[$type]??'';
	}

	public function url($name='', $param=[])
	{
		if ($name) {
			if (strpos($name, '/') === false) {
				$name = $this->nameFormat($name);
			} else {
				$name = trim($name, '/');
			}
		} elseif ($param) {
			$name = array_reverse(explode('-', explode('?', pathinfo($_SERVER['REQUEST_URI'])['filename'])[0]));
			foreach ($name as $key=>$value) {
				if (isset($param[$value])) {
					if ($param[$value]) {
						$name[$key-1] = $param[$value];
					} else {
						unset($name[$key-1]);
						unset($name[$key]);
					}
					unset($param[$value]);
				}
			}
			$param = array_filter($param);
			foreach ($param as $key=>$value) {
				if (in_array($key, $this->include_path) || in_array($key, $this->include_param)) {
					array_unshift($name, $key);
					array_unshift($name, $value);
					unset($param[$key]);
				}
			}
			$name = implode('-', array_reverse($name));
		}
		if ($name) $name .= $this->paramFormat($param);
		return APP_DOMAIN.$name;
	}

	public function adminUrl($name='', $param=[])
	{
		if (!$name) {
			$name = \App::get('router', 'path');
			if (\App::get('router', 'func') != 'index') {
				$name .= DS.\App::get('router', 'path');
			}
		}
		return APP_DOMAIN.$name.($param?'?'.http_build_query($param):'');
	}

	public function nameFormat($name, $param=[])
	{
		$name = preg_replace('/[^-A-Za-z0-9\/\s]/', '', strtolower($name));
		$name = preg_replace('/( ){2,}/', ' ', $name);
		$name = str_replace(' ', '-' , $name);
		$name = preg_replace('/-{1,}/', '-', $name);
		$name = ltrim($name, '-');
		if (isset($param['id'])) $name .= '-'.$param['id'];
		if (isset($param['page'])) $name .= '-page-'.$param['page'];
		if (isset($param['size'])) $name .= '-size-'.$param['size'];
		return $name;
	}

	public function paramFormat($param)
	{
		$str = '';
		foreach ($param as $key=>$value) {
			if (in_array($key, $this->include_param) || in_array($key, $this->include_path)) {
				$str .= '-'.$key.'-'.$value;
				unset($param[$key]);
			}
		}
		$str .= '.html';
		if ($param) {
			$str .= '?'.http_build_query($param);
		}
		return $str;
	}
}