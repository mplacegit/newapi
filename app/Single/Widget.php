<?php
namespace App\Single;
use Illuminate\Database\Eloquent\Model;
use \App\Requestmethods\Abs as RequestAbs;
class Widget
{
  protected $table="mp_widgets";
  public static $id;
  public static $affiliate_id;
  public static $wid;
  public static $clid;
  public static $isLink;
  public static $idServer;
  public static $count_offers=3; 
  public static $offers_link_count=3;
  public static $noSearch=0;
  public static $search_categories;
  public static $search_text;
  public static $search_category =0;
  public static $default_categories;
  public static $initFlag=0;
  public static $sthSearch;
  public static $sthSearchFlag=0;
  public static $listDefault;
  public static $sthDefaultFlag=0; 
  public static $sthDefaultCnt=1; 
  public static $ParsedData;
  public static $maska;
  public static $DataFlag=[0,0,0]; // модели. поиск. заглушка  // 0 нет 1 робот 2 маска 3 запрос онлайн
  //public static  $modelsIds='1685512377,1685512158,1685511152,1685512705,1632737850,1715926030,1696076129,1685882115,1696077044,1715347557,1691829193,1687738202,1606506926,1696035654,1693207890,1690701669';
  //public static $modelsIds=[];
  
  public static function InitData($data){
  if(self::$initFlag) return;
  self::$initFlag=1;
  	    self::$wid=(isset($data["wid"]) && $data["wid"])?$data["wid"]:"97";
		self::$affiliate_id=(isset($data["affiliate_id"]) && $data["affiliate_id"])?$data["affiliate_id"]:"56015401b3da9";
		//$widget=\App\Widget::where("affiliate_id",self::$affiliate_id)->where("wid",self::$wid)->where("enabled",1)->first();
		$widget=\Db::table("mp_widgets")->where("affiliate_id",self::$affiliate_id)->where("wid",self::$wid)->where("enabled",1)->first();
		//var_dump($widget);
		if(!$widget){
		throw new \Exception('не тот виджет');
		}
		self::$idServer=(isset($widget->server_id) && $widget->server_id)?$widget->server_id:0;
		$alias=\App\ServerAlias::where("name","~*",RequestAbs::$remoteHost)->first();
		if(!defined("onclicklink") && (!$alias || $alias->id_server !=self::$idServer)){
		throw new \Exception('не совпадает домен '.RequestAbs::$remoteHost.' / '.self::$idServer .'');
		}
		//throw new \Exception('не туда виджет'); 
		self::$search_categories=$widget->search_categories;
		self::$default_categories=$widget->default_categories;
		self::$count_offers=$widget->offers_count; 
        self::$offers_link_count=$widget->offers_link_count;
		self::$isLink=$widget->islink;
		self::$clid=$widget->clid;
		self::$noSearch=$widget->no_search;
		//self::
		if(self::$idServer && \App\Requestmethods\Abs::$remotePath){
		$sql="select *
        from server_rules
        where id_server=".self::$idServer."
        and '".trim(\App\Requestmethods\Abs::$remotePath)."' ~~* regexp_replace(page,'\*$','%')
        order by length(page) desc
        limit 1
        ";
		
		$sql="select * from server_rules where phraze <>'' and cat_id <>'' ORDER BY random() LIMIT 1";
		$results = \DB::select($sql);
		if($results){
		
		self::$maska=$results[0];
	

		if(preg_match('/\*$/u',self::$maska->page)){
		}else{
		self::$maska->sof=1;
		}
		if(self::$maska->cat_id){
		self::$search_categories=self::$maska->cat_id;
		}
		}
		}
		
        if(isset($data["data"])){
		self::$ParsedData=json_decode($data["data"],true);
		
		//
		
		if(isset(self::$ParsedData["geo_id"]) && self::$ParsedData["geo_id"]){
		Geoip::$geo_id=self::$ParsedData["geo_id"];
		}
		if(1==1 && isset(self::$ParsedData["userText"]) && self::$ParsedData["userText"]){
		if(self::$search_text=self::returnText(self::$ParsedData["userText"])){
		self::$DataFlag[1]=3;
		
		}
		if(self::$ParsedData["categories"]){
		$tmpcc=explode(",",self::$ParsedData["categories"]);
		if(count($tmpcc)==1){
		self::$search_categories="";
		self::$search_category=$tmpcc[0];
		}else{
		self::$search_categories=self::$ParsedData["categories"];
		}
		}
		}elseif(isset(self::$maska->sof) && self::$maska->phraze){
		self::$search_text=self::$maska->phraze; 
		self::$DataFlag[1]=2;
		if(self::$maska->cat_id){
		
		//self::$search_categories=self::$maska->cat_id;
		$tmpcc=explode(",",self::$search_categories);
		if(count($tmpcc)==1){
		self::$search_categories="";
		self::$search_category=$tmpcc[0];
		}
		}
		}elseif(!self::$noSearch && isset(self::$ParsedData["text"]) && self::$ParsedData["text"] && self::$search_text=self::selectText(self::$ParsedData["text"])){
		self::$DataFlag[1]=1;
		}

		}
	    if(self::$default_categories){
		 self::$DataFlag[2]=1;
		}
		
  }
public static function clearGarbage($text){
$arr=preg_split('/[\s\.\t\r\,-]+/',$text);
$res=[];
foreach($arr as $a){

 $a=mb_strtolower(trim($a), 'UTF-8');
 $a=preg_replace('/[\?\=]+/','',$a);
 if($a && 1==0){}else{
 //if($this->redis->hget("garbage_words",$a)){}else{
 $res[$a]=1;
 }
}
if(!$res) return "";
return implode(" ",array_keys($res));
}

private static function returnText($text){
$text=urldecode($text);
$text=strip_tags($text);
$text=preg_replace('/((\s+[0-9]+))+$/','',$text);

$text=self::clearGarbage($text);
return trim($text);
}
private static function selectText($text){

$cachetext=[];
    if(!is_array($text)){
	return self::returnText($text);
    }
	$mak=array();
    $cachetext=array();
    foreach($text["models"] as $t){

	$t=self::returnText($t);
	if($t)
	$mak[$t]=1;

	}
	foreach($text["texts"] as $t){
	$t=self::returnText($t);
	if($t)
	$mak[$t]=2;
	}
   $cachetext=array_keys($mak);
   srand((double) microtime() * 1000000); 
   $skan=rand(0,count($cachetext)-1);
   return $cachetext[$skan];
   
 }
 public static function prepareSearchSth(){
        if(self::$sthSearchFlag) return 0;
		self::$sthSearchFlag=1;
  		if(self::$search_categories){
		self::$search_categories.=",90829";
		$sql="select count(*) as cnt from
		yandex_categories t1 
		inner join yandex_categories t2
		on t2.parent_path @> t1.parent_path and t2.id in(".self::$search_categories.")
		where t1.id=?
		";
		$pdo = \DB::connection()->getPdo();
		self::$sthSearch=$pdo->prepare($sql);
		return 1;
		}
		return 0;
  }
  static function getSearchText(){
  //print self::$search_text; exit();
  return self::$search_text;
  }
  static function getSearchCategory(){
  //print self::$search_text; exit();
  return self::$search_category;
  }
  static function checkSearchCategory($id){
       if(!self::prepareSearchSth()) return 1;
       if(!self::$sthSearch) return 1;
  	   self::$sthSearch->execute([$id]);
	   $result = self::$sthSearch->fetch(\PDO::FETCH_COLUMN);
	   if($result == 90829) return 0; // книги
	   return $result;
	}
	public static function getDefaultCategories(){
	if(!self::$sthDefaultFlag){
	if(self::$default_categories){
	$sql="select * from yandex_categories t1
	where id in(".self::$default_categories.")
	and id_parent <>90401";
	$pdo = \DB::connection()->getPdo();
    $tmp=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	foreach($tmp as $t){
	self::$sthDefaultCnt++;
	switch(true){
	case ($t["type"]=="guru"):
	self::$listDefault[$t["type"]][$t["id"]]=$t["visual"];
	break;
	case ($t["type"]=="gurulight"):
	self::$listDefault[$t["type"]][$t["id"]]=$t["visual"];
	break;
	case ($t["type"]=="nonguru" && $t["visual"]):
	self::$listDefault["novisual"][$t["id"]]=$t["visual"];
	break;	
	case ($t["type"]=="nonguru" && !$t["visual"]):
	self::$listDefault["novisual"][$t["id"]]=$t["visual"];
	break;
	default:
	break;
	}
	}
	}
	}
	self::$sthDefaultFlag=1;
	return self::$listDefault;
	}
  public static function getModels(){
	if(self::$modelsIds){
	return explode(",",self::$modelsIds);
	}
	return false;
	}
  public static function getMethod(){
  
      if(self::$DataFlag[0]>1 || (self::$DataFlag[0] && !self::$noSearch)){
       return "models";
	  }
	  if(self::$DataFlag[1]>1 || (self::$DataFlag[1] && !self::$noSearch)){
       return "search";
	  }
	  if(self::$DataFlag[2]){
	   return "default";
      }
	  throw new \Exception('не находит данных ');
	}
  public static function getOffersCount($all_count){
    if($all_count>self::$sthDefaultCnt) {
        $res=ceil($all_count/self::$sthDefaultCnt);
    }else {
        $res=1;
    }
    return $res;
	}	
public static function GUIDv4 ($trim = true)
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        if ($trim === true)
            return trim(com_create_guid(), '{}');
        else
            return com_create_guid();
    }
   //return "+";
    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Fallback (PHP 4.2+)
    mt_srand((double)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    $guidv4 = $lbrace.
              substr($charid,  0,  8).$hyphen.
              substr($charid,  8,  4).$hyphen.
              substr($charid, 12,  4).$hyphen.
              substr($charid, 16,  4).$hyphen.
              substr($charid, 20, 12).
              $rbrace;
    return $guidv4;
}
	
}