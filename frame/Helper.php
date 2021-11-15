<?php
function dd(...$arg){
	foreach ($arg as $value) {
		print_r($value);
		echo IS_CLI ? PHP_EOL : '<br />';
	}
	exit();
}
function config($name='', $default=''){
	$name = explode('.', $name);
	if (!isset($GLOBALS[$name[0]])) {
		if (is_file($file = ROOT_PATH.'config'.DS.$name[0].'.php')) {
			$GLOBALS[$name[0]] = require $file;
		} else {
			$GLOBALS[$name[0]] = null;
			return $default;
		}
	}
	if ($name[0] == 'domain') {
		unset($name[0]);
		return $GLOBALS['domain'][implode('.', $name)] ?? $default;
	}
	$data = $GLOBALS;
	foreach ($name as $value) {
		if (isset($data[$value])) {
			$data = $data[$value];
		} else {
			return $default;
		}
	}
	return $data;
}
function redirect($url=''){
	header('Location:'.$url);
	exit();
}
function make($name, $params=null){
	return \App::make($name, $params);
}
function html(){
	return \App::make('frame/Html');
}
function session(){
	return \App::make('frame/Session');
}
function router(){
	return \App::make('frame/Router');
}
function request(){
	return \App::make('frame/Request');
}
function redis($db=0){
	return \App::make('frame/Redis')->setDb($db);
}
function cache($db=0){
	return \App::make('frame/Redis')->setDb($db, true);
}
function db($db=null){
	return \App::make('frame/Connection')->setDb($db);
}
function page($size=null, $total=null, $current=null){
	return \App::make('frame/Paginator')->make($size, $total, $current);
}
function url($url=null, $param=null, $domain=null) {
    return router()->buildUrl($url, $param, $domain);
}
function siteUrl($name){
	return config('env.APP_DOMAIN').$name.'?v='.config('env.APP_VERSION');
}
function mediaUrl($url, $width=''){
	if (!empty($width)) {
		$ext = pathinfo($url, PATHINFO_EXTENSION);
		$url = str_replace('.'.$ext, DS.$width.'.'.$ext, $url);
	}
	if (strpos($url, 'http') === false) {
		$url = config('env.APP_DOMAIN').FILE_CENTER.DS.$url;
	}
	return $url.'?v='.config('env.APP_VERSION');
}
function isCli(){
	return stripos(php_sapi_name(), 'cli') !== false;
}
function isWin(){
	return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}
function isJson($string){
	if (is_array($string)) return $string;
	$temp = json_decode($string, true); 
	return json_last_error() == JSON_ERROR_NONE ? $temp : $string;
}
function ipost($name='', $default=null){
	return \App::make('frame/Request')->ipost($name, $default);
}
function iget($name='', $default=null){
	return \App::make('frame/Request')->iget($name, $default);
}
function now($time=null){
	return date('Y-m-d H:i:s', $time ? $time : time());
}
function appT($text, $replace=[], $lanId='', $type='common'){
	if (empty($lanId)) $lanId = lanId();
	$key = 'translate_'.$type.'_'.$lanId;
	if (!isset($GLOBALS[$key])) {
		$file = ROOT_PATH.'template'.DS.APP_TEMPLATE_TYPE.DS.'language'.DS.$type.DS.$lanId.'.php';
		if (is_file($file)) $GLOBALS[$key] = include $file;
		else $GLOBALS[$key] = null;
	}
	if (isset($GLOBALS[$key][$text])) {
		$text = $GLOBALS[$key][$text];
		if (!empty($replace)) {
			$text = strtr($text, $replace);
		}
	}
	return $text;
}
function distT($text, $replace=[], $lanId=''){
	return appT($text, $replace, $lanId, lcfirst(\App::get('router', 'path')));
}
function utf8len($string){
	return mb_strlen($string, 'UTF-8');
}
function get1024Peck($size, $dec=2){
	$a = ['B', 'KB', 'MB', 'GB', 'TB'];
	$pos = 0;
	while ($size >= 1024) {
		$size /= 1024;
		$pos++;
	}
	return round($size, $dec).' '.$a[$pos];
}
function getDirFile($path){
	if (is_file($path)) {
		return $path;
	}
	$files = scandir($path);
	$fileItem = [];
	foreach ($files as $v) {
		$newPath = $path.DIRECTORY_SEPARATOR.$v;
		if (is_file($newPath)) {
			$fileItem[] = $newPath;
		} else if (is_dir($newPath) && $v != '.' && $v != '..') {
			$fileItem = array_merge($fileItem, getDirFile($newPath));
		}
	}
	return $fileItem;
}
function randString($len=16, $lower=true, $upper=true, $number=true){
	$str = '';
	if ($lower) $str .= 'abcdefghijklnmopqrstuvwxyz';
	if ($upper) $str .= 'ABCDEFGHIJKLNMOPQRSTUVWXYZ';
	if ($number) $str .= '0123456789';
	$rStr = '';
	$seedLen = strlen($str);
	while ($len > 0) {
		$rStr .= $str[rand(0, $seedLen - 1)];
		$len--;
	}
	return $rStr;
}
function strTrim($str){
	return ltrim($str, " \t\n\r\0\x0B ");
}
function getUniqueName(){
	$arr = explode(' ', microtime());
	return str_replace([':', ' ', '-', '0.'], '', now().$arr[0]);
}
function lanId(){
	$id = \App::get('site_language_id');
	if (empty($id)) {
		$id = session()->get('site_language_id', 'en');
		\App::set('site_language_id', $id);
	}
	return $id;
}
function siteId(){
	return APP_SITE_ID;
}
function userId(){
	$id = \App::get('site_mem_id');
	if (empty($id)) {
		$id = session()->get(APP_TEMPLATE_TYPE.'_info.mem_id', 0);
		\App::set('site_mem_id', $id);
	}
	return $id;
}
function currencyId(){
	$id = \App::get('site_currency_id');
	if (empty($id)) {
		$id = session()->get('site_currency_id', 'USD');
		\App::set('site_currency_id', $id);
	}
	return $id;
}
function uuId(){
	return session()->get(APP_TEMPLATE_TYPE.'_info.uuid', '');
}