<?php

function dd(...$arg) {
	foreach ($arg as $value) {
		print_r($value);
		echo PHP_EOL;
	}
	exit();
}

function config($type, $name = '', $default = '') {
	static $configs = [];
	if ($type === 'domain' && $name !== '') {
		$domainContext = \App::get('domain');
		if (is_array($domainContext) && isset($domainContext[$name])) {
			return $domainContext[$name];
		}
	}
	if (!isset($configs[$type])) {
		$file = ROOT_PATH . 'config/' . $type . '.php';
		$configs[$type] = is_file($file) ? require $file : [];
	}
	if ($name === '') {
		return $configs[$type];
	}
	return $configs[$type][$name] ?? $default;
}

function redirect($url = '', $return = true) {
	if ($return && isset($_SERVER['HTTP_HOST'])) {
		$scheme = $_SERVER['REQUEST_SCHEME'] ?? 'http';
		$uri = $_SERVER['REQUEST_URI'] ?? '/';
		frame('Session')->set('return_url', trim($scheme . '://' . $_SERVER['HTTP_HOST'] . $uri, '/'));
	}
	header('Location:' . $url);
	\App::runOver(true);
}

function service($name, $params = null) {
	return \App::make('app/service/' . $name, $params);
}

function model($name, $params = null) {
	return \App::make('app/model/' . $name, $params);
}

function frame($name) {
	return \App::make('frame/' . $name);
}

function page($size = 0, $total = 0) {
	return frame('Paginator')->make($size, $total);
}

function siteUrl($name) {
	static $prefix = null;
	if ($prefix === null) {
		$prefix = '/' . (isMobile() ? 'mobile' : 'computer') . '/';
	}
	return $prefix . $name . '?v=' . version();
}

function mediaUrl($url, $width = '') {
	if ($width) {
		$pos = strrpos($url, '.');
		if ($pos !== false) {
			$url = substr($url, 0, $pos) . DS . $width . substr($url, $pos);
		}
	}
	return siteUrl($url);
}

function url($c = '', $p = []) {
	return frame('Router')->url($c, $p);
}

function adminUrl($name = '', $param = []) {
	return frame('Router')->adminUrl($name, $param);
}

function version() {
	return '1.0.0';
}

function isJson($string) {
	if (!is_array($string)) return $string;
	$temp = json_encode($string, JSON_UNESCAPED_UNICODE);
	return json_last_error() === JSON_ERROR_NONE ? $temp : $string;
}

function isArray($arr) {
	if (is_array($arr) || !is_string($arr)) return $arr;
	$temp = json_decode($arr, true);
	return json_last_error() === JSON_ERROR_NONE ? $temp : $arr;
}

function isAjax() {
	static $isAjax = null;
	if ($isAjax === null) {
		$isAjax = frame('Request')->isAjax();
	}
	return $isAjax;
}

function isCli() {
	static $isCli = null;
	if ($isCli === null) {
		$isCli = defined('IS_CLI') ? IS_CLI : (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg');
	}
	return $isCli;
}

function isPost() {
	static $isPost = null;
	if ($isPost === null) {
		$isPost = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
	}
	return $isPost;
}

function isMobile() {
	static $isMobile = null;
	if ($isMobile === null) {
		$isMobile = frame('Request')->isMobile();
	}
	return $isMobile;
}

function isDebug() {
	static $isDebug = null;
	if ($isDebug === null) {
		$isDebug = isCli() ? true : (bool)config('domain', 'debug', false);
	}
	return $isDebug;
}

function isWin() {
	static $isWin = null;
	if ($isWin === null) {
		$isWin = DIRECTORY_SEPARATOR === '\\' || (defined('PHP_OS_FAMILY') && PHP_OS_FAMILY === 'Windows');
	}
	return $isWin;
}

function ipost($name = '', $default = null) {
	return frame('Request')->ipost($name, $default);
}

function iget($name = '', $default = null) {
	return frame('Request')->iget($name, $default);
}

function input($name = '', $default = null) {
	return frame('Request')->input($name, $default);
}

function appT($text, $replace = [], $lanId = '', $type = 'common') {
	static $langCache = [];
	$lanId = $lanId ?: lanId('code');
	$cacheKey = $type . '.' . $lanId;
	if (!isset($langCache[$cacheKey])) {
		$file = ROOT_PATH . 'template/' . config('domain', 'template') . '/' . (isMobile() ? 'mobile' : 'computer') . '/language/' . $type . '/' . $lanId . '.php';
		$langCache[$cacheKey] = is_file($file) ? require $file : [];
	}
	$translated = $langCache[$cacheKey][$text] ?? $text;
	return $replace ? strtr($translated, $replace) : $translated;
}

function distT($text, $replace = [], $lanId = '') {
	return appT($text, $replace, $lanId, lcfirst(\App::get('router', 'path')));
}

function get1024Peck($size, $dec = 2) {
	static $units = ['B', 'KB', 'MB', 'GB', 'TB'];
	if ($size <= 0) return '0 B';
	$pos = min((int)floor(log($size, 1024)), 4);
	return round($size / (1024 ** $pos), $dec) . ' ' . $units[$pos];
}

function getDirFile($path, &$result = null) {
	if ($result === null) {
		$result = [];
	}
	if (is_file($path)) {
		$result[] = $path;
		return $result;
	}
	if (!is_dir($path)) {
		return $result;
	}
	$files = scandir($path);
	foreach ($files as $v) {
		if ($v === '.' || $v === '..') continue;
		$newPath = $path . DS . $v;
		if (is_file($newPath)) {
			$result[] = $newPath;
		} elseif (is_dir($newPath)) {
			getDirFile($newPath, $result);
		}
	}
	return $result;
}

function randString($len = 16, $lower = true, $upper = true, $number = true) {
	static $poolCache = [];
	$key = ($lower ? 1 : 0) | ($upper ? 2 : 0) | ($number ? 4 : 0);
	if (!isset($poolCache[$key])) {
		$str = '';
		if ($lower) $str .= 'abcdefghijnmqrt';
		if ($upper) $str .= 'ABCDEFGHIJLNMQRT';
		if ($number) $str .= '23456789';
		$poolCache[$key] = $str;
	}
	$str = $poolCache[$key];
	$seedLen = strlen($str);
	if ($seedLen === 0) return '';
	$rStr = '';
	$max = $seedLen - 1;
	for ($i = 0; $i < $len; $i++) {
		$rStr .= $str[random_int(0, $max)];
	}
	return $rStr;
}

function strTrim($str) {
	return trim($str, " \t\n\r\0\x0B\xC2\xA0");
}

function lanId($type = 'id') {
	return frame('Session')->get('site_language_' . $type, $type === 'code' ? 'en' : 1);
}

function currencyId($type = 'id') {
	return frame('Session')->get('site_currency_' . $type, $type === 'code' ? 'USD' : 1);
}

function userId($login = true) {
	$uid = (int)frame('Session')->get(config('domain', 'class') . '_info', 0, 'mem_id');
	if (!$login && $uid <= 0) return 10000;
	return $uid;
}

function createDir($dir) {
	if (!is_dir($dir)) {
		mkdir($dir, 0755, true);
	}
	return $dir;
}

function sys() {
	return service('system/System');
}

function site() {
	return service('site/Site');
}

function category() {
	return service('category/Category');
}

function purchase() {
	return service('purchase/Purchase');
}

function attr() {
	return service('attr/Attr');
}

function now($time = 0) {
	return date('Y-m-d H:i:s', $time > 0 ? $time : time());
}

function siteId() {
	static $id = null;
	if ($id === null) {
		$id = (int)\App::get('domain', 'site_id', 0);
	}
	return $id;
}

function redis($db = 0) {
	return frame('Redis')->setDb($db);
}

function db($db = null) {
	return frame('Connection')->setDb($db);
}