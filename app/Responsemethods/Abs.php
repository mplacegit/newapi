<?php
namespace App\Responsemethods;
use App\Single\Geoip;
use App\Single\Widget;
use App\Offers\Model;
abstract class Abs
{
    protected static $cntModels=0;
	protected static $cachedModels=[];
	protected static $cachedOffers=[];
	protected static $listModels=[];
	public static $marketsrc ="Данные Yandex Маркет";
    public static $ip = "79.122.189.122";
	public static $copyright = "https://market.yandex.ru/product/13747875/?hid=91491&pp=1001&clid=153409&distr_type=2";
	public static $foundCopyRight=["model"=>"","search"=>"","default"=>""];
	public static $cityName = "Караганда";
	public static $foundCity=0;

	abstract protected function getData($data);
	protected function addDopParams(&$params){
	if(Widget::$clid){
	$params[]="clid=".Widget::$clid;
	}
    }
	protected function addGeoParams(&$params){
	$params[]=Geoip::getGeoParams();
    }	
    protected function setLimitPrices(&$params){
    if(Widget::$affiliate_id=="573478b609117"){
	    $params[]="price_min=1000";
        $params[]="price_max=11000";
     }
    }
	protected function checkCopyRightModel(&$item){
	if(!self::$foundCopyRight["model"]){
	if(isset($item["link"])){
	self::$foundCopyRight["model"]=$item["link"];
	}
	}
	}
	public function checkCopyRightSearch($item){
	if(!self::$foundCopyRight["search"]){
	$clid="";
	if(Widget::$clid){
	$clid="&clid=".Widget::$clid;
		}
	self::$foundCopyRight["search"]="https://market.yandex.ru/catalog/?hid=".$item["category"]."&text=".$item["text"]."&onstock=1".$clid."";
	}
	}
	public function checkCopyRightDefault($idCategory){
	if(!self::$foundCopyRight["default"]){
	  self::$foundCopyRight["default"]="https://market.yandex.ru/catalog?hid=".$idCategory."&in-stock=1&adult=1";
	}
	}	
	protected function checkRegion(&$item){
	if(!self::$foundCity){
	if(isset($item["delivery"])){
	if(isset($item["delivery"]["userRegionName"])){
	self::$cityName = $item["delivery"]["userRegionName"];
	self::$foundCity=1; return;
	#print $item["delivery"]["userRegionName"]; print "\n";
	}
	if(isset($item["delivery"]["regionName"])){
	self::$cityName = $item["delivery"]["regionName"];
	self::$foundCity=1; return;
	#print $item["delivery"]["regionName"]; print "\n";
	}
	if(isset($item["delivery"]["userRegion"])){
	    self::$cityName = $item["delivery"]["userRegion"]["name"]; 
	    self::$foundCity=1; return;
		#print $item["delivery"]["userRegion"]["name"]; print "\n";
	}		
	if(isset($item["delivery"]["shopRegion"])){
		self::$cityName =  $item["delivery"]["shopRegion"]["name"]; 
	    self::$foundCity=1; return;
		#print $item["delivery"]["shopRegion"]["name"]; print "\n";
			}
		}
	}
	}
	protected function parseItems($items,$method=""){
    //print_r($items); die();
    $models=[];
	$i=0;
	foreach($items as $item){
	if(isset($item["model"])){
	    if($method == "search" && !Widget::checkSearchCategory($item["model"]["categoryId"])) continue;
		if(isset(self::$cachedModels[$item["model"]["id"]])) continue;
		self::$cachedModels[$item["model"]["id"]]=["id"=>$item["model"]["id"],"name"=>"","photo"=>""];
		if(isset($item["model"]["name"]))
		self::$cachedModels[$item["model"]["id"]]["name"]=$item["model"]["name"];
		if(isset($item["model"]["previewPhoto"]) && $item["model"]["previewPhoto"]){
		self::$cachedModels[$item["model"]["id"]]["photo"]=$item["model"]["previewPhoto"]["url"];
		}elseif(isset($item["model"]["mainPhoto"]) && $item["model"]["mainPhoto"]){
		self::$cachedModels[$item["model"]["id"]]["photo"]=$item["model"]["mainPhoto"]["url"];
		}elseif(isset($item["model"]["bigPhoto"]) && $item["model"]["bigPhoto"]){
		self::$cachedModels[$item["model"]["id"]]["photo"]=$item["model"]["bigPhoto"]["url"];
		}
		$new = new \App\Responsemethods\Yandexmodel();
		$request=[
	     "count"=>Widget::$offers_link_count,
	     "id"=>$item["model"]["id"]
	     ];
		
	    $links = $new->getData($request);
		
        if(!$links) continue;
		$this->checkCopyRightModel($item["model"]);
		$this->checkRegion($item["model"]);
		if(!isset($models[$item["model"]["id"]])){
		$model=new 	Model();
		$model->id=$item["model"]["id"];
		$model->add_links($links);
		if(isset($item["model"]["vendorId"]))
		$model->vendorId=$item["model"]["vendorId"];
        $model->modelName = self::$cachedModels[$item["model"]["id"]]["name"];
        if(self::$cachedModels[$item["model"]["id"]]["photo"])
		$model->modelImg=self::$cachedModels[$item["model"]["id"]]["photo"];
		$model->modelLink=\App\Single\Encryption::cryptModel($model->id);
		self::$cntModels++;
		self::$listModels[$model->id]=$i;
		$models[$i]=$model;
		$i++;
		}else{
		$model=$models[$item["model"]["id"]];
		$model->add_links($links);
		if(isset($item["model"]["vendorId"]))
		$model->vendorId=$item["model"]["vendorId"];
		$model->modelName = self::$cachedModels[$item["model"]["id"]]["name"];
        if(self::$cachedModels[$item["model"]["id"]]["photo"])
		$model->modelImg=self::$cachedModels[$item["model"]["id"]]["photo"];
		}
		}
	if(isset($item["offer"])){
    if($method == "search" && !Widget::checkSearchCategory($item["offer"]["categoryId"])) continue;
		if(isset(self::$cachedOffers[$item["offer"]["id"]])) continue;
		$this->checkRegion($item["offer"]);
		
		self::$cachedOffers[$item["offer"]["id"]]=1;
		//print "<pre>"; print_r($item["offer"]); print "<hr>";
		$new = new \App\Offers\Offer();
		$new->id=$item["offer"]["id"];
		if(isset($item["offer"]["categoryId"])){
		$this->checkCopyRightDefault($item["offer"]["categoryId"]);
		$new->categoryId=$item["offer"]["categoryId"];
		}
		if(isset($item["offer"]["vendor"]) && isset($item["offer"]["vendor"]["name"])){
	       $new->vendor=$item["offer"]["vendor"]["name"];
		}
		if(isset($item["offer"]["modelId"]))
		$new->model_id=$item["offer"]["modelId"];
		$new->name=$item["offer"]["name"];
		$new->shop_id=$item["offer"]["shopInfo"]["id"];
	    $new->shopName=$item["offer"]["shopInfo"]["shopName"];
		if(isset($item["offer"]["shopInfo"]["rating"]))
		$new->Rating=$item["offer"]["shopInfo"]["rating"];
		else
		$new->Rating=4;
		$new->price=$item["offer"]["price"]["value"]." ".$item["offer"]["price"]["currencyName"];
        if(isset($item["offer"]["price"]["discount"]) && $item["offer"]["price"]["discount"]){
        $new->discount=$item["offer"]["price"]["discount"];
        }
        else
        $new->discount=0;
		if(isset($item["offer"]["delivery"]["brief"])){
		$new->Brief=$item["offer"]["delivery"]["brief"];
		}else{
		$new->Brief="";
		}
		if(isset($item["offer"]["delivery"]["delivery"]))
        $new->Delivery=$item["offer"]["delivery"]["delivery"];
		$new->shopLink=\App\Single\Encryption::cryptOffer($new->id);
		if(isset($item["offer"]["modelId"])){
		if(isset(self::$listModels[$item["offer"]["modelId"]]) && isset($models[self::$listModels[$item["offer"]["modelId"]]])){
		$model=$models[self::$listModels[$item["offer"]["modelId"]]];
		}else{
		$model=new 	Model();
		$model->id=$item["offer"]["modelId"];
		$model->modelName = $new->name;
		$model->modelLink=\App\Single\Encryption::cryptModel($model->id);
		self::$cntModels++;
		self::$listModels[$model->id]=$i;
		$models[$i]=$model;
		$i++;
		}
		}else{
				
		$model=new 	Model();
		$model->id=uniqid().'_';
		$model->modelName = $new->name;
		$model->modelLink=\App\Single\Encryption::cryptModel($model->id);
		self::$cntModels++;
		self::$listModels[$model->id]=$i;
		$models[$i]=$model;
		$i++;
		}
		if(!$model->modelImg){
		if(isset($item["offer"]["previewPhotos"]) && $item["offer"]["previewPhotos"]){
		$model->modelImg=$item["offer"]["previewPhotos"][0]["url"];
		}elseif(isset($item["offer"]["photos"]) && $item["offer"]["photos"]){
		$model->modelImg=$item["offer"]["photos"][0]["url"];
		}elseif(isset($item["offer"]["bigPhoto"]) && $item["offer"]["bigPhoto"]){
		$model->modelImg=$item["offer"]["bigPhoto"]["url"];
		}
		}
		if(!$model->modelName){
		}
		$model->add_link($new);
		}	
	}
	return $models;
	}
	protected function parseModelsItems($items,&$config){
	$offers=[];
	
	foreach($items as $item){

	    if(isset(self::$cachedOffers[$item["id"]])) continue;
		self::$cachedOffers[$item["id"]]=1;
				$this->checkRegion($item);
	    $new = new \App\Offers\Offer();
		$new->id=$item["id"];
		if(isset($item["categoryId"])){
		$new->categoryId=$item["categoryId"];
		}
		if(isset($item["vendor"]) && isset($item["vendor"]["name"])){
	       $new->vendor=$item["vendor"]["name"];
		}
		if(isset($item["modelId"]))
		$new->model_id=$item["modelId"];
		$new->shop_id=$item["shopInfo"]["id"];
		$new->name=$item["name"];
		if(!$config["name"]){
		$config["name"]=$new->name;
		}
		$new->shopName=$item["shopInfo"]["shopName"];
		if(isset($item["shopInfo"]["rating"]))
		$new->Rating=$item["shopInfo"]["rating"];
		else
		$new->Rating=4;
		$new->price=$item["price"]["value"]." ".$item["price"]["currencyName"];
        if(isset($item["price"]["discount"]) && $item["price"]["discount"]){
        $new->discount=$item["price"]["discount"];
        }
        else
        $new->discount=0;
		if(isset($item["delivery"]["brief"])){
		$new->Brief=$item["delivery"]["brief"];
		}else{
		$new->Brief="";
		}
	    if(isset($item["delivery"]["delivery"]))
        $new->Delivery=$item["delivery"]["delivery"];

		$new->shopLink=\App\Single\Encryption::cryptOffer($new->id);
		
			
		if(!$config["photo"]){
		if(isset($item["previewPhotos"]) && $item["previewPhotos"]){
		$config["photo"]=$item["previewPhotos"][0]["url"];
		}elseif(isset($item["photos"]) && $item["photos"]){
		$config["photo"]=$item["photos"][0]["url"];
		}elseif(isset($item["bigPhoto"]) && $item["bigPhoto"]){
		$config["photo"]=$item["bigPhoto"]["url"];
		}
		}
		$offers[]=$new;
	}
	return $offers;
	}	
	public function GetOfferLink($data){
		Widget::InitData($data);
	    $res=$this->getData($data);
		if(Widget::$isLink){
		return $res["link"];
		}else{
		return $res["url"];
		}
	}	
	public function convertItems(&$items){
	#print self::$cityName; die();
	shuffle($items); 
	if(self::$foundCopyRight["model"]){
	self::$copyright=self::$foundCopyRight["model"];
	}elseif(self::$foundCopyRight["search"]){
	self::$copyright=self::$foundCopyRight["search"];
	}elseif(self::$foundCopyRight["default"]){
	self::$copyright=self::$foundCopyRight["default"];
	}
	

	    return ["marketsrc"=>self::$marketsrc
		,"ip"=>self::$ip
		,"Copyright"=>self::$copyright
		,"CityName"=>self::$cityName
		,"offers"=>$items];	
	}
	
}