<?php
namespace App\Responsemethods;
use App\Singleapi;
class Yandexsearch extends Abs
{
	public function getData($data){
	    $dopCategory=0;
		$dopVendor="";
	    $params=array();
		$this->addGeoParams($params);
		$this->addDopParams($params);
		$params[]="count=".$data["count"];
		$params[]="onstock=1";
		$params[]="check_spelling=1";
		$params[]="fields=DISCOUNTS";
		$this->setLimitPrices($params);
		
		$params[]="text=".urlencode($data["text"]);
		if(isset($data["category_id"]) && $data["category_id"])
		$params[]="category_id=".$data["category_id"];
        $api_url="search.json";
		$url=$api_url."?".implode("&",$params);  
		$str=Singleapi::getRemoteData(1,$url);
		
		if(!$str) return false;
		$searches=json_decode($str,true);
		if(!isset($searches["searchResult"]))
		return false;
		if(!isset($searches["searchResult"]["results"]))
		return false;
		
		$models=$this->parseItems($searches["searchResult"]["results"],"search");
		if(!$models) return false;
		if($models[0]->links[0]->categoryId){
		//$this->checkCopyRightSearch([]);
		$this->checkCopyRightSearch(array("category"=>trim($models[0]->links[0]->categoryId),"text"=>urlencode($data["text"])));
		$dopCategory=$models[0]->links[0]->categoryId;
		if($models[0]->links[0]->vendor){
		$dopVendor=$models[0]->links[0]->vendor;
		}
		} 
	    return $models;
	}
}