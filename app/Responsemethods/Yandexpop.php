<?php
namespace App\Responsemethods;
use App\Singleapi;
use App\Single\Widget;
use App\Offers\Model;
class Yandexpop extends Abs
{
	public function getData($data){
	    $cnt=Widget::getOffersCount($data["count"]);
		$models=[];
		foreach($data["categories"]["gurulight"] as $k=>$v){
	    $params=array();
		$this->addGeoParams($params);
		$this->addDopParams($params);
		$page=rand(1,2);
        $params[]="count=".($cnt*2);
        $params[]="fields=discounts";
        $params[]="sort=POPULARITY";
        $params[]="filter=onstock";
        $params[]="page=".$page;
        $api_url="category/$k/models.json";
        $url=$api_url."?".implode("&",$params);  
		$str=Singleapi::getRemoteData(2,$url);
		if(!$str) continue;
		$cats=json_decode($str,true);
		if(!isset($cats["models"]) || !$cats["models"]["total"]){ 
		$data["categories"]["novisual"][$k]=0;
		continue;
		}
		$indexes = array_keys($cats["models"]["items"]);        
		shuffle($indexes);
		$indexes=array_slice($indexes,0,$cnt); 
		foreach($indexes as $j){
		$modelId=$cats["models"]["items"]["$j"]["id"];
		$modelName=$cats["models"]["items"]["$j"]["name"];
		$modelImg="";
		
		if(isset($cats["models"]["items"]["$j"]["previewPhoto"]) && $cats["models"]["items"]["$j"]["previewPhoto"]){
		$modelImg=$cats["models"]["items"]["$j"]["previewPhoto"]["url"];
		}elseif(isset($cats["models"]["items"]["$j"]["mainPhoto"]) && $cats["models"]["items"]["$j"]["mainPhoto"]){
		$modelImg=$cats["models"]["items"]["$j"]["mainPhoto"]["url"];
		}
			if(!isset(parent::$cachedModels[$modelId])){
			parent::$cachedModels[$modelId]=["id"=>$modelId,"name"=>$modelName,"photo"=>$modelImg];
			}
			$new = new \App\Responsemethods\Yandexmodel();
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
				}else{
				$data["categories"]["novisual"][$k]=0;
		        continue;
				}
			}
		}
		if(isset($data["categories"]["novisual"]) && $data["categories"]["novisual"]){
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