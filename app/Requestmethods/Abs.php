<?php
namespace App\Requestmethods;
use App\Single\Geoip;
use App\Single\Widget;
use App\Singleapi;
use Cache;
abstract class Abs
{

    public static $remoteUri='https://api.market-place.su';
	public static $clientIp='';
	public static $remoteHost='';
	public static $remotePath='';
	public static $_MD5='';
	

	abstract protected function getData($data);
	protected function getStaticData(){
	exit();
	$link="http://newapi.market-place.su/staticdata.php"; 
	$str=Singleapi::getRemoteData(0,$link);
	if(!$str){
	throw new \Exception('не получает товары');
	}
    #return $str;
	$data=json_decode($str,true);
	$str=json_encode($data);
	    $t=Cache::store('redis')->remember($this->iframeHash, 2, function() use($str){
          return $str;
        });
	}  
 	public function  getDefault(){ 
	$link="http://newapi.market-place.su/staticdata.php"; 
	return Singleapi::getRemoteData(0,$link);
	}
	public function  getTestData($data){ 

		Widget::InitData($data);
		$this->iframeSrc.="/".Widget::$affiliate_id."/".Widget::$wid."";
	    //throw new \Exception('оно не работает');
	if(defined("d_13")){
	$new = new \App\Responsemethods\Yandexsearch();
	$request=[
	"count"=>3,
	//"text"=>"Микроволновая печ Samsung"
	//"text"=>"Стиральная машина Bosh"
	"text"=>"Набор пластилина"
	];
	}else{
	
	$method=Widget::getMethod();
	//var_dump($method);
	switch($method){
	case "search":
	$new = new \App\Responsemethods\Yandexsearch();
	$text=Widget::getSearchText();
	$category_id=Widget::getSearchCategory();

	$request=[
	"count"=>Widget::$count_offers,
	//"text"=>"Микроволновая печ Samsung"
	//"text"=>"Стиральная машина Bosh"
	"text"=>$text,
	"category_id"=>$category_id
	];
	
	break;
	case "default":
	$cats=Widget::getDefaultCategories();
	if($cats){
	$request=[
	"count"=>Widget::$count_offers,
	"categories"=>$cats,
	];
	
	
	if(isset($cats["guru"])){
	$new = new \App\Responsemethods\Yandextop();
	}elseif(isset($cats["gurulight"])){
	$new = new \App\Responsemethods\Yandexpop();
	}elseif(isset($cats["visual"])){
	throw new \Exception('пока нет visual');
	}elseif(isset($cats["novisual"])){
	throw new \Exception('пока нет novisual');
	}else{
	throw new \Exception('не назначены категории для показа');
	}
	}else{
	throw new \Exception('не назначены категории для показа');
	}
	break;
	case "models":
	$mods=Widget::getModels();
	if($mods){
	$request=[
	"count"=>Widget::$count_offers,
	"models"=>$mods,
	];
	$new =new \App\Responsemethods\Yandexmodels();
	}
	break;
	}
	
	}
	if(!isset($new) || !$new){
	throw new \Exception('невозможно');
	}
	//var_dump($request); die();
 	$res=$new->getData($request);
	if(!$res){
	throw new \Exception('оно не работает');
	}

	$str=json_encode($new->convertItems($res));

	
	$this->iframeHash=Widget::GUIDv4();
	    $t=Cache::store('redis')->remember($this->iframeHash, 2, function() use($str){
        return $str;
		});
	}
    protected function parseUrl($url,$param){
    $parse = parse_url($url);
	if(isset($parse["host"]))
	self::$remoteHost=$parse["host"];
	else
	self::$remoteHost=isset($_SERVER["REMOTE_HOST"])?$_SERVER["REMOTE_HOST"]:"";
	if(!isset($parse["path"])){
    $parse["path"]='';
    }

	self::$remotePath=(isset($parse["query"]) && $parse["query"])?$parse["path"].'?'.$parse["query"]:$parse["path"];
    self::$_MD5=md5(self::$remotePath);

    if(isset($parse[$param])){
    return $parse[$param];
        }
	return '';
	}
	
}