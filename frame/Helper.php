<?php
function dd(...$arg){
	$enter = isCli() ? PHP_EOL : '<br />';
	foreach ($arg as $value) {
		print_r($value);
		echo $enter;
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
	if ($name) return isset($GLOBALS[$type][$name])?$GLOBALS[$type][$name]:$default;
	return isset($GLOBALS[$type])?$GLOBALS[$type]:$default;
}
function redirect($url=''){
	header('Location:'.$url);exit();	
}
function make($name, $params=null, $static=true){
	return \App::make($name, $params, $static);
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
function db($db=null){
	return \App::make('frame/Connection')->setDb($db);
}
function page($size=0, $total=0){
	return \App::make('frame/Paginator')->make($size, $total);
}
function url($name='', $param=[], $joint=true) {
    return router()->url($name, $param, $joint);
}
function adminUrl($name='', $param=[]){
	return router()->adminUrl($name, $param);
}
function siteUrl($name, $version=true){
	$extension = pathinfo($name)['extension']??false;
	if (!$extension) {
		return $name;
	}
	if (in_array($extension, ['png', 'jpeg', 'jpg']) && is_file(ROOT_PATH.'storage'.DS.str_replace('.'.$extension, '.webp', $name))) {
		$name = str_replace('.'.$extension, '.webp', $name);
	}
	$name = domain().trim($name, DS);
	if ($version) $name .= '?v='.version();
	return $name;
}
function mediaUrl($url, $width='', $version=true){
	if (!empty($width)) {
		$ext = pathinfo($url, PATHINFO_EXTENSION);
		$url = str_replace('.'.$ext, DS.$width.'.'.$ext, $url);
	}
	return siteUrl($url);
}
function version(){
	return \App::getVersion();
}
function isWin(){
	if (!defined('IS_WIN')) define('IS_WIN', strtoupper(substr(PHP_OS, 0, 3))=='WIN');
	return IS_WIN;
}
function isJson($string){
	if (is_array($string)) return $string;
	$temp = json_decode($string, true); 
	return json_last_error()==JSON_ERROR_NONE?$temp:$string;
}
function isAjax(){
	if (!defined('IS_AJAX')) define('IS_AJAX', \App::make('frame/Request')->isAjax());
	return IS_AJAX;
}
function isMobile(){
	if (!defined('IS_MOBILE')) define('IS_MOBILE', \App::make('frame/Request')->isMobile());
	return IS_MOBILE;
}
function isCli(){
	return defined('IS_CLI');
}
function isDebug(){
	return config('env', 'APP_DEBUG');
}
function isAdmin(){
	return siteId() == 80;
}
function domain(){
	return 'https://'.\App::get('base_info', 'domain').'/';
}
function template() {
	return \App::get('base_info', 'template');
}
function type() {
	return isAdmin()?'admin':'home';
}
function ipost($name='', $default=null){
	return \App::make('frame/Request')->ipost($name, $default);
}
function iget($name='', $default=null){
	return \App::make('frame/Request')->iget($name, $default);
}
function input($name='', $default=null){
	return \App::make('frame/Request')->input($name, $default);
}
function now($time=null){
	return date('Y-m-d H:i:s', $time?$time:time());
}
function appT($text, $replace=[], $lanId='', $type='common'){
	if (empty($lanId)) $lanId = lanId('code');
	$key = 'translate_'.$type.'_'.$lanId;
	if (!isset($GLOBALS[$key])) {
		$file = ROOT_PATH.'template'.DS.template().DS.'language'.DS.$type.DS.$lanId.'.php';
		if (is_file($file)) $GLOBALS[$key] = include $file;
		else $GLOBALS[$key] = null;
	}
	if (isset($GLOBALS[$key][$text])) {
		$text = $GLOBALS[$key][$text];
		if (!empty($replace)) {
			$tempArr = [];
			foreach ($replace as $key=>$value) {
				$tempArr['{'.$key.'}'] = $value;
			}
			$text = strtr($text, $tempArr);
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
	$lower && $str .= 'abcdefghijklnmopqrstuvwxyz';
	$upper && $str .= 'ABCDEFGHIJKLNMOPQRSTUVWXYZ';
	$number && $str .= '0123456789';
	$rStr = '';
	$seedLen = strlen($str);
	while ($len > 0) {
		$rStr .= $str[rand(0, $seedLen - 1)];
		$len--;
	}
	return $rStr;
}
function nameFormat($name){
	return strtr($name, ['\\'=>'-', DS=>'-']);
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
function cateId(){
	return \App::get('base_info', 'cate_id');
}
function userId(){
	return session()->get(type().'_info', 0, 'mem_id');
}
function userName(){
	$info = session()->get(type().'_info');
	$name = trim($info['first_name'].' '.$info['last_name']);
	if (!$name) {
		$name = $info['email'];
	}
	return $name;
}
function userEmail(){
	return session()->get(type().'_info', '', 'email');
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