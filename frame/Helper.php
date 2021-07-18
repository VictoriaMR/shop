<?php
function dd(...$arg){
	foreach ($arg as $value) {
		print_r($value);
		echo IS_CLI ? PHP_EOL : '<br />';
	}
	exit();
}
function config($name=''){
	if (empty($name)) return $GLOBALS;
	$name = explode('.', $name);
	$data = $GLOBALS;
	foreach ($name as $value) {
		if (empty($data[$value])) {
			return false;
		}
		$data = $data[$value];
	}
	return $data;
}
function env($name='', $replace=''){
	if (defined($name))
		return constant($name);
	return config('ENV')[$name] ?? $replace;
}
function redirect($url){
	header('Location:'.$url);
	exit();
}
function make($name){
	return \App::make($name);
}
function view(){
	return make('frame/View');
}
function html(){
	return make('frame/Html');
}
function page($size, $total, $current=null){
	return make('frame/Paginator')->make($size, $total, $current);
}
function request(){
	return make('frame/Request');
}
function redis($db=0){
	return make('frame/Redis')->setDb($db);
}
function session(){
	return make('frame/Session');
}
function router(){
	return make('frame/Router');
}
function db($db=null){
	return make('frame/Connection')->setDb($db);
}
function url($url=null, $param=null) {
    return router()->buildUrl($url, $param);
}
function siteUrl($name){
	return env('APP_DOMAIN').$name.'?v='.config('version');
}
function mediaUrl($url='', $width=''){
	if (!empty($width)) {
		$ext = pathinfo($url, PATHINFO_EXTENSION);
		$url = str_replace('.'.$ext, DS.$width.'.'.$ext, $url);
	}
	if (strpos($url, 'http') === false) {
		$url = env('FILE_CENTER_DOMAIN').$url;
	}
	return $url.'?v='.config('version');
}
function isCli(){
	return stripos(php_sapi_name(), 'cli') !== false;
}
function isJson($string){
	if (is_array($string)) return false;
	$string = json_decode($string, true); 
	return json_last_error() == JSON_ERROR_NONE ? $string : false;
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
function now(){
	return date('Y-m-d H:i:s');
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