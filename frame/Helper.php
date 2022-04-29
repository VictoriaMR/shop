<?php
function dd(...$arg){
	foreach ($arg as $value) {
		print_r($value);
		echo IS_CLI ? PHP_EOL : '<br />';
	}
	exit();
}
function config($type, $name='', $default=''){
	if (!isset($GLOBALS[$type])) {
		$file = ROOT_PATH.'config'.DS.$type.'.php';
		if (is_file($file)) {
			$GLOBALS[$type] = require $file;
		} else {
			$GLOBALS[$type] = null;
			return $default;
		}
	}
	if (empty($name)) {
		return empty($GLOBALS[$type])?$default:$GLOBALS[$type];
	}
	return empty($GLOBALS[$type][$name])?$default:$GLOBALS[$type][$name];
}
function redirect($url=''){
	header('Location:'.$url);exit();	
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
	return APP_DOMAIN.$name.'?v='.config('env', 'APP_VERSION');
}
function mediaUrl($url, $width=''){
	if (!empty($width)) {
		$ext = pathinfo($url, PATHINFO_EXTENSION);
		$url = str_replace('.'.$ext, DS.$width.'.'.$ext, $url);
	}
	return APP_DOMAIN.$url.'?v='.config('env', 'APP_VERSION');
}
function isCli(){
	return stripos(php_sapi_name(), 'cli')!==false;
}
function isWin(){
	return strtoupper(substr(PHP_OS, 0, 3))=='WIN';
}
function isJson($string){
	if (is_array($string)) return $string;
	$temp = json_decode($string, true); 
	return json_last_error()==JSON_ERROR_NONE?$temp:$string;
}
function isAjax(){
	return \App::make('frame/Request')->isAjax();
}
function isMobile(){
	return \App::make('frame/Request')->isMobile();
}
function ipost($name='', $default=null){
	return \App::make('frame/Request')->ipost($name, $default);
}
function iget($name='', $default=null){
	return \App::make('frame/Request')->iget($name, $default);
}
function input($name='', $default=null) {
	return \App::make('frame/Request')->input($name, $default);
}
function now($time=null){
	return date('Y-m-d H:i:s', $time?$time:time());
}
function appT($text, $replace=[], $lanId='', $type='common'){
	if (empty($lanId)) $lanId = lanId('code');
	$key = 'translate_'.$type.'_'.$lanId;
	if (!isset($GLOBALS[$key])) {
		$file = ROOT_PATH.'template'.DS.APP_TEMPLATE_PATH.DS.'language'.DS.$type.DS.$lanId.'.php';
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
		$newPath = $path.DS.$v;
		if (is_file($newPath)) {
			$fileItem[] = $newPath;
		} elseif (is_dir($newPath)&&$v!='.'&&$v!='..') {
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
	return str_replace([':', ' ', '-', '0.'], '', now().explode(' ', microtime())[0]);
}
function lanId($type='id'){
	return session()->get('site_language_'.$type, $type=='code'?'en':1);
}
function siteId(){
	return \App::get('base_info', 'site_id');
}
function userId(){
	return session()->get(APP_TEMPLATE_TYPE.'_info', 0, 'mem_id');
}
function currencyId(){
	return session()->get('site_currency_id', 'USD');
}
function uuId(){
	return \App::make('frame/Cookie')->get('uuid');
}
function hasZht($str){
	return preg_match('/[\x{4e00}-\x{9fa5}]/u', $str)>0;
}