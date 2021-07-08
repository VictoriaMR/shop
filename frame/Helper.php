<?php
function dd(...$arg){
	foreach ($arg as $value) {
		print_r($value);
		echo '<br />';
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
	return env('APP_DOMAIN').$name;
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