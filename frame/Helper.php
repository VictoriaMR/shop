<?php
function dd(...$arg){
	foreach ($arg as $value){
		print_r($value);
		echo PHP_EOL;
	}
	exit();
}
function config($type, $name='', $default=''){
	if (is_null(\App::get($type))){
		\App::set($type, require ROOT_PATH.'config/'.$type.'.php');
	}
	return \App::get($type, $name);
}
function redirect($url='', $return=true){
	$return && frame('Session')->set('return_url', trim($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], '/'));
	header('Location:'.$url);exit();	
}
function service($name, $params=null){
	return \App::make('app/service/'.$name, $params);
}
function model($name, $params=null){
	return \App::make('app/model/'.$name, $params);
}
function frame($name){
	return \App::make('frame/'.$name);
}
function page($size=0, $total=0){
	return frame('Paginator')->make($size, $total);
}
function siteUrl($name){
	return '/'.(isMobile()?'mobile':'computer').'/'.$name.'?v='.version();
}
function mediaUrl($url, $width='', $version=true){
	if ($width){
		$ext = pathinfo($url, PATHINFO_EXTENSION);
		$url = str_replace('.'.$ext, DS.$width.'.'.$ext, $url);
	}
	return siteUrl($url);
}
function url($c='', $p=[]) {
	return frame('Router')->url($c, $p);
}
function adminUrl($name='', $param=[]){
	return frame('Router')->adminUrl($name, $param);
}
function version(){
	return '1.0.0';
}
function isJson($string){
	if (is_array($string)) return $string;
	$temp = json_decode($string, true); 
	return json_last_error()==JSON_ERROR_NONE?$temp:$string;
}
function isAjax(){
	defined('IS_AJAX') || define('IS_AJAX', frame('Request')->isAjax());
	return IS_AJAX;
}
function isMobile(){
	defined('IS_MOBILE') || define('IS_MOBILE', frame('Request')->isMobile());
	return IS_MOBILE;
}
function isDebug() {
	defined('IS_DEBUG') || define('IS_DEBUG', \App::get('domain', 'debug'));
	return IS_DEBUG;
}
function isWin(){
	defined('IS_WIN') || define('IS_WIN', strtoupper(substr(PHP_OS, 0, 3))=='WIN');
	return IS_WIN;
}
function ipost($name='', $default=null){
	return frame('Request')->ipost($name, $default);
}
function iget($name='', $default=null){
	return frame('Request')->iget($name, $default);
}
function input($name='', $default=null){
	return frame('Request')->input($name, $default);
}
function appT($text, $replace=[], $lanId='', $type='common'){
	$lanId || $lanId = lanId('code');
	$key = 'translate_'.$type.'_'.$lanId;
	if (is_null(\App::get($key))){
		$file = ROOT_PATH.'template/'.config('domain', 'template').'/'.(isMobile()?'mobile':'computer').'/language/'.$type.'/'.$lanId.'.php';
		\App::set($key, is_file($file) ? require $file : []);
	}
	if (\App::get($key, $text)){
		$text = \App::get($key, $text);
		if ($replace){
			$text = strtr($text, $replace);
		}
	}
	return $text;
}
function distT($text, $replace=[], $lanId=''){
	return appT($text, $replace, $lanId, lcfirst(\App::get('router', 'path')));
}
function get1024Peck($size, $dec=2){
	$a = ['B', 'KB', 'MB', 'GB', 'TB'];
	$pos = 0;
	while ($size >= 1024){
		$size /= 1024;
		$pos++;
	}
	return round($size, $dec).' '.$a[$pos];
}
function getDirFile($path){
	if (is_file($path)){
		return $path;
	}
	$files = scandir($path);
	$fileItem = [];
	foreach ($files as $v){
		$newPath = $path.DS.$v;
		if (is_file($newPath)){
			$fileItem[] = $newPath;
		} elseif (is_dir($newPath) && $v!='.' && $v!='..'){
			$fileItem = array_merge($fileItem, getDirFile($newPath));
		}
	}
	return $fileItem;
}
function randString($len=16, $lower=true, $upper=true, $number=true){
	$str = '';
	$lower && $str .= 'abcdefghijnmqrt';
	$upper && $str .= 'ABCDEFGHIJLNMQRT';
	$number && $str .= '23456789';
	$rStr = '';
	$seedLen = strlen($str);
	while ($len > 0){
		$rStr .= $str[rand(0, $seedLen - 1)];
		$len--;
	}
	return $rStr;
}
function lanId($type='id'){
	return frame('Session')->get('site_language_'.$type, $type=='code'?'en':1);
}
function userId($login=true){
	$uid = frame('Session')->get(config('domain', 'class').'_info', 0, 'mem_id');
	if (!$login && !$uid) $uid = '10000';
	return $uid;
}
function createDir($dir){
	if (!is_dir($dir)){
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
function now($time=0) {
	return $time > 0 ? date('Y-m-d H:i:s', $time) : date('Y-m-d H:i:s');
}
function siteId() {
	return \App::get('domain', 'site_id');
}
function redis($db=0) {
	return frame('Redis')->setDb($db);
}