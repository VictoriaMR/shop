<?php

namespace frame;

final class Router
{
	protected $funcExcept = [
		'faq'      => ['f' => true, 'g' => true],
		'product'  => ['pid' => true, 'sid' => true, 'p' => true, 's' => true],
		'category' => ['cid' => true, 'c' => true],
		'newIn'    => ['cid' => true, 'c' => true],
	];

	protected $staticKeys = [
		'cid' => true,
		'c'   => true,
		'pid' => true,
		'p'   => true,
		'sid' => true,
		's'   => true,
		'f'   => true,
		'g'   => true,
		'id'  => true,
	];

	protected $pathAlias = [
		'newin'        => 'NewIn',
		'userinfo'     => 'UserInfo',
		'pagenotfound' => 'PageNotFound',
	];

	public function analyze($class)
	{
		$path = 'Index';
		$func = 'index';

		$key = key($_GET);
		if ($key !== null && $key !== '') {
			$cleanKey = trim(str_replace(['_html', '.html'], '', $key), '/');
			if ($cleanKey === '_well-known/ai-catalog_json' || $cleanKey === 'ai-catalog_json') {
				unset($_GET[$key]);
				return ['class' => 'home', 'path' => 'Api', 'func' => 'aiCatalog'];
			}
			if ($cleanKey === 'api/openapi_json' || $cleanKey === 'openapi_json' || $cleanKey === 'api/openapi') {
				unset($_GET[$key]);
				return ['class' => 'home', 'path' => 'Api', 'func' => 'openapi'];
			}
			if ($cleanKey !== '') {
				$pos = strpos($cleanKey, '/');
				if ($pos === false) {
					if ($cleanKey !== 'null') {
						$path = $this->parseSegment($cleanKey);
					}
				} else {
					$p0 = substr($cleanKey, 0, $pos);
					$p1 = substr($cleanKey, $pos + 1);
					if ($p0 !== '' && $p0 !== 'null') {
						$path = $this->parseSegment($p0);
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

	protected function parseSegment(string $segment): string
	{
		$dashPos = strpos($segment, '-');
		if ($dashPos === false) {
			$lower = strtolower($segment);
			return $this->pathAlias[$lower] ?? ucfirst($segment);
		}

		$parts = explode('-', $segment);
		$base = $parts[0] !== '' ? $parts[0] : 'Index';
		$lowerBase = strtolower($base);
		$resolvedPath = $this->pathAlias[$lowerBase] ?? ucfirst($base);

		$count = count($parts);
		for ($i = 1; $i < $count; $i += 2) {
			$paramKey = strtolower($parts[$i] ?? '');
			$paramVal = $parts[$i + 1] ?? '';
			if ($paramKey === '' || !isset($this->staticKeys[$paramKey])) {
				// 遇到尾随的语义化分类/商品名称 slug（如 -furniture、-outdoor-garden）直接结束参数提取
				break;
			}
			if (!isset($_GET[$paramKey])) {
				$_GET[$paramKey] = $paramVal;
			}
			if ($paramKey === 'c' && !isset($_GET['cid'])) {
				$_GET['cid'] = $paramVal;
			} elseif ($paramKey === 'p' && !isset($_GET['pid'])) {
				$_GET['pid'] = $paramVal;
			} elseif ($paramKey === 's' && !isset($_GET['sid'])) {
				$_GET['sid'] = $paramVal;
			}
		}

		return $resolvedPath;
	}

	public function url($name = '', $param = [], $joint = true)
	{
		$host = $_SERVER['HTTP_HOST'] ?? '';
		if (!empty($param)) {
			if (is_array($param)) {
				if (isset($this->funcExcept[$name])) {
					$staticRules = $this->funcExcept[$name];
					$staticParams = [];
					$queryParams = [];
					$slug = '';
					foreach ($param as $k => $v) {
						if ($v === '' || $v === null) {
							continue;
						}
						if ($k === 'name' || $k === 'slug') {
							$slug = $this->slugFormat((string)$v);
							continue;
						}
						if ($staticRules === true || isset($staticRules[$k])) {
							$staticParams[$k] = $v;
						} else {
							$queryParams[$k] = $v;
						}
					}
					if (!empty($staticParams)) {
						$staticPart = $this->nameFormat(http_build_query($staticParams));
						if ($slug !== '') {
							$staticPart .= '-' . $slug;
						}
						$name .= ($joint ? '-' : '/') . $staticPart . '.html';
					} elseif ($name !== '') {
						$name .= '.html';
					}
					if (!empty($queryParams)) {
						$name .= '?' . http_build_query($queryParams);
					}
				} else {
					$query = http_build_query($param);
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
		$queryString = !empty($param) ? '?' . http_build_query($param) : '';
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
		$name = preg_replace('/[^-a-z0-9\/\s\&=]/', '', strtolower($name));
		$name = strtr($name, [' ' => '-', '&' => '-', '=' => '-']);
		$name = preg_replace('/-+/', '-', $name);
		return trim($name, '-');
	}

	public function slugFormat(string $text): string
	{
		if ($text === '') return '';
		$text = strtolower(str_replace(["'", '"'], '', $text));
		$text = preg_replace('/[^a-z0-9]+/', '-', $text);
		return trim($text, '-');
	}
}
