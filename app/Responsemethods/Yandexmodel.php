<?php
namespace App\Responsemethods;
use App\Singleapi;
class Yandexmodel extends Abs
{
	public function getData($data){
	    $params=array();
		$this->addGeoParams($params);
		$this->addDopParams($params);
		$params[]="count=".$data["count"];
		$params[]="fields=DISCOUNTS";
        $params[]="all_modification=1";
        $params[]="groupBy=SHOP";
	    $id=$data["id"];
		$api_url='model/'.$id.'/offers.json';
	    $url=$api_url."?".implode("&",$params); 	
 		$str=Singleapi::getRemoteData(1,$url);
		if(!$str) return false;
		$data=json_decode($str,true);
		if(!isset($data["offers"]))
		return false;
		if(!isset($data["offers"]["items"]))
		return false;
		if(!isset(parent::$cachedModels[$id])){
		parent::$cachedModels[$id]=["id"=>$id,"name"=>"","photo"=>""];
		}
		return $this->parseModelsItems($data["offers"]["items"],parent::$cachedModels[$id]);

	}
}