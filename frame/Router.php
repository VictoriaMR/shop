<?php

namespace frame;

final class Router
{
	protected $funcExcept = ['faq' => true, 'product' => true];

	public function analyze($class)
	{
		$path = 'Index';
		$func = 'index';

		$key = key($_GET);
		if ($key !== null && $key !== '') {
			$cleanKey = str_replace('_html', '', $key);
			$cleanKey = trim($cleanKey, '/');
			if ($cleanKey !== '') {
				$pos = strpos($cleanKey, '/');
				if ($pos === false) {
					if ($cleanKey !== 'null') {
						$path = ucfirst($cleanKey);
					}
				} else {
					$p0 = substr($cleanKey, 0, $pos);
					$p1 = substr($cleanKey, $pos + 1);
					if ($p0 !== '' && $p0 !== 'null') {
						$path = ucfirst($p0);
					}
					if ($p1 !== '' && $p1 !== 'null') {
						$pos2 = strpos($p1, '/');
						$func = $pos2 !== false ? substr($p1, 0, $pos2) : $p1;
					}
				}
			}
			unset($_GET[$key]);
		}

		return ['class' => $class, 'path' => $path, 'func' => $func];
	}

	public function url($name = '', $param = [], $joint = true)
	{
		$host = $_SERVER['HTTP_HOST'] ?? '';
		if ($param) {
			if (is_array($param)) {
				$query = http_build_query($param);
				if (isset($this->funcExcept[$name])) {
					$name .= ($joint ? '-' : '/') . $this->nameFormat($query) . '.html';
				} else {
					$name .= ($name !== '' ? '.html?' : '?') . $query;
				}
			} elseif (is_string($param) && $param !== '') {
				$name .= '?' . $param;
			}
		}
		return 'https://' . $host . '/' . ltrim($name, '/');
	}

	public function adminUrl($name = '', $param = [])
	{
		if (!$name) {
			$name = \App::get('router', 'path', 'Index');
			$func = \App::get('router', 'func', 'index');
			if ($func !== 'index') {
				$name .= '/' . $func;
			}
		}
		$queryString = $param ? '?' . http_build_query($param) : '';
		return '/' . ltrim($name, '/') . $queryString;
	}

	public function setParam(array $param = [])
	{
		$merged = !empty($_GET) ? array_merge($_GET, $param) : $param;
		$isHome = \App::get('router', 'class') === 'home';
		$path = \App::get('router', 'path', 'Index');
		$func = \App::get('router', 'func', 'index');
		$routeName = lcfirst($path) . ($func !== 'index' ? '/' . $func : '');
		if ($isHome) {
			return $this->url($routeName, $merged);
		}
		return $this->adminUrl($routeName, $merged);
	}

	public function nameFormat($name, $param = [])
	{
		if (!$name) return '';
		$name = preg_replace('/[^-A-Za-z0-9\/\s\&=]/', '', strtolower($name));
		$name = str_replace([' ', '&', '='], '-', $name);
		$name = preg_replace('/-+/', '-', $name);
		return trim($name, '-');
	}
}