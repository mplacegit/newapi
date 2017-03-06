<?php
namespace App;
class Singlewidget
{
    public static $currentApi;
    public static $instaces=[];
    public static function getInstance($name){
	     if(!isset(self::$instaces[$name])){
			$class = 	"\\App\\Requestmethods\\$name";
			self::$instaces[$name]=new $class;
			}
		if(isset(self::$instaces[$name])){
			return self::$instaces[$name];
		}else{
			throw new \Exception('нет метода');
		}
	}
}