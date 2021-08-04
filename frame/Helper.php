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
	if (empty($GLOBALS[$name[0]])) {
		if (is_file($file = ROOT_PATH.'config'.DS.$name[0].'.php')) {
			$GLOBALS[$name[0]] = require $file;
		} else {
			return $default;
		}
	}
	$data = $GLOBALS;
	foreach ($name as $value) {
		if (empty($data[$value])) {
			return $default;
		}
		$data = $data[$value];
	}
	return $data;
}
function env($name='', $default=''){
	return config('env.'.$name, $default);
}
function redirect($url=''){
	if (empty($url)) {
		$url = session()->get('callback_url');
		$url = $url ? $url : env('APP_DOMAIN');
	}
	header('Location:'.$url);
	exit();
}
function make($name, $params=null){
	return \App::make($name, $params);
}
function view() {
	return \App::make('frame/View');
}
function html(){
	return \App::make('frame/Html');
}
function page($size, $total, $current=null){
	return \App::make('frame/Paginator')->make($size, $total, $current);
}
function request(){
	return \App::make('frame/Request');
}
function redis($db=0){
	return \App::make('frame/Redis')->setDb($db);
}
function session(){
	return \App::make('frame/Session');
}
function router(){
	return \App::make('frame/Router');
}
function debug(){
	return \App::make('frame/Debug');
}
function db($db=null){
	return \App::make('frame/Connection')->setDb($db);
}
function site(){
	return make('app/service/SiteService');
}
function url($url=null, $param=null, $domain=null) {
    return router()->buildUrl($url, $param, $domain);
}
function siteUrl($name){
	return env('APP_DOMAIN').$name.'?v='.config('version.'.APP_TEMPLATE_TYPE);
}
function mediaUrl($url, $width=''){
	if (!empty($width)) {
		$ext = pathinfo($url, PATHINFO_EXTENSION);
		$url = str_replace('.'.$ext, DS.$width.'.'.$ext, $url);
	}
	if (strpos($url, 'http') === false) {
		$url = env('FILE_CENTER_DOMAIN').$url;
	}
	return $url.'?v='.config('version.'.APP_TEMPLATE_TYPE);
}
function isCli(){
	return stripos(php_sapi_name(), 'cli') !== false;
}
function isJson($string){
	if (is_array($string)) return false;
	$temp = json_decode($string, true); 
	return json_last_error() == JSON_ERROR_NONE ? $temp : $string;
}
function ipost($name='', $default=null){
	return request()->ipost($name, $default);
}
function iget($name='', $default=null){
	return request()->iget($name, $default);
}
function input($name='', $default=null){
	return request()->input($name, $default);
}
function now($time=null){
	return date('Y-m-d H:i:s', $time ? $time : time());
}
function appT($text){
	return make('App/Services/TranslateService')->getText($text);
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
	if ($lower) {
	    $str .= 'abcdefghijklnmopqrstuvwxyz';
	}
	if ($upper) {
	    $str .= 'ABCDEFGHIJKLNMOPQRSTUVWXYZ';
	}
	if ($number) {
	    $str .= '0123456789';
	}
	$rStr = '';
	$seedLen = strlen($str);
	while ($len > 0) {
	    $rStr .= $str[rand(0, $seedLen - 1)];
	    $len--;
	}
	return $rStr;
}
function getUniqueName(){
	$arr = explode(' ', microtime());
	return str_replace('.', '', $arr[0] + $arr[1]);
}
function lanId(){
	$lanId = session()->get('site_language_id');
	if (empty($lanId)) {
		$lanId = defined('APP_LANGUAGE') ? APP_LANGUAGE : 2;
	}
	return $lanId;
}
function siteId(){
	return APP_SITE_ID;
}
function userId(){
	return session()->get(APP_TEMPLATE_TYPE.'_info.mem_id', 0);
}