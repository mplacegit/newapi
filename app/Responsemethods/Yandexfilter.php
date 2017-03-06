<?php
namespace App\Responsemethods;
use App\Singleapi;
use App\Single\Widget;
use App\Offers\Model;
class Yandexfilter extends Abs
{
	public function getData($data){
	   	    $cnt=Widget::getOffersCount($data["count"]);
			$models=[];
			$items=[];
			foreach($data["categories"]["novisual"] as $k=>$v){
	        $params=array();
		    $this->addGeoParams($params);
		    $this->addDopParams($params);
			$page=rand(1,2);
			$params[]="count=".($cnt*2);
			$params[]="fields=discounts";
			$params[]="sort=POPULARITY";
			$params[]="filter=onstock";
			$params[]="page=".$page;
			$api_url="filter/{$k}.json";
            $url=$api_url."?".implode("&",$params); 
			$str=Singleapi::getRemoteData(2,$url);
		    if(!$str) continue;
			$cats=json_decode($str,true);
			if(!isset($cats["searchResult"]))
		    continue;
		    if(!isset($cats["searchResult"]["results"]))
		    continue;
			
				$indexes = array_keys($cats["searchResult"]["results"]);        
		        shuffle($indexes);
		        $indexes=array_slice($indexes,0,$cnt); 
			
			foreach($indexes as $j){
			$items[]=$cats["searchResult"]["results"][$j];
			}
			}
			if(!$items) return false;
			$models=$this->parseItems($items);
			//print "<pre>";	print_r($models); print "</pre>";
		    if(!$models) return false;
			return $models;
		}
}		