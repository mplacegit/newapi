<?php
namespace App\Responsemethods;
use App\Singleapi;
use App\Single\Widget;
use App\Offers\Model;
class Yandextop extends Abs
{
	public function getData($data){
		$cnt=Widget::getOffersCount($data["count"]);
		$models=[];
		foreach($data["categories"]["guru"] as $k=>$v){
	    $params=array();
		$this->addGeoParams($params);
		$this->addDopParams($params);
	    $api_url="popular/$k.json";

        $url=$api_url."?".implode("&",$params);  
		$str=Singleapi::getRemoteData(2,$url);
		if(!$str) continue;
		$cats=json_decode($str,true);
		if(!isset($cats["popular"])) continue;
		if(!isset($cats["popular"]["topCategoryList"]) || !$cats["popular"]["topCategoryList"]) continue;
		if(!isset($cats["popular"]["topCategoryList"][0]["topVendors"]) || !$cats["popular"]["topCategoryList"][0]["topVendors"]) continue;
		
		$indexes = array_keys($cats["popular"]["topCategoryList"][0]["topVendors"]);        
		shuffle($indexes);
		$indexes=array_slice($indexes,0,$cnt); 
		
		foreach($indexes as $j){

		$modelId=$cats["popular"]["topCategoryList"][0]["topVendors"][$j]["topModelId"];
			if(!isset(parent::$cachedModels[$modelId])){
			parent::$cachedModels[$modelId]=["id"=>$modelId,"name"=>"","photo"=>$cats["popular"]["topCategoryList"][0]["topVendors"][$j]["topModelImage"]];
			}
			$new = new Yandexmodel();
			$request=[
			"count"=>Widget::$offers_link_count,
			"id"=>$modelId
			];
			 $links = $new->getData($request);
			 if($links){
			 	$model=new 	Model();
		        $model->id=$modelId;
                $model->modelName = parent::$cachedModels[$modelId]["name"];
		        $model->modelImg = parent::$cachedModels[$modelId]["photo"];
				$model->modelLink=\App\Single\Encryption::cryptModel($model->id);
				$model->add_links($links);
				$models[]=$model;
				parent::$cntModels++;
				}
			}
		}

		if(isset($data["categories"]["gurulight"]) && $data["categories"]["gurulight"]){
		$pop = new Yandexpop();
		$popmodels=$pop->getData($data);
		if($popmodels){
		$models=array_merge($models,$popmodels);
		}
		}elseif(isset($data["categories"]["novisual"]) && $data["categories"]["novisual"]){
		$pop = new Yandexfilter();
		$filtermodels=$pop->getData($data);
		if($filtermodels){
		$models=array_merge($models,$filtermodels);
		}
		}
		
		if(!$models) return false;
		return $models;
	}
}