<?php
namespace App\Responsemethods;
use App\Singleapi;
use App\Single\Widget;
use App\Offers\Model;
class Yandexmodels extends Abs
{
	public function getData($data){
	    $cnt=$data["count"];
		$models=[];
		$dopCategory=0;
		$dopVendor="";
		$indexes = array_keys($data["models"]);        
		shuffle($indexes);
		//$indexes=array_slice($indexes,0,$cnt); 
		foreach($indexes as $j){
		if($cnt<=0) break;
			$modelId=$data["models"][$j];
			if(!isset(parent::$cachedModels[$modelId])){
			parent::$cachedModels[$modelId]=["id"=>$modelId,"name"=>"","photo"=>""];
			}
			$new = new \App\Responsemethods\Yandexmodel();
			$request=[
			"count"=>Widget::$offers_link_count,
			"id"=>$modelId
			];
			 $links = $new->getData($request);
			 if(!$links){
			// print $cnt." $modelId ".print_r($links,true)."\n";
			 }
			 if($links){
			  if(!$dopCategory && $links[0]->categoryId){
			  $dopCategory=$links[0]->categoryId;
			  if(!$dopVendor && $links[0]->vendor){
			    $dopVendor=$links[0]->vendor;
			   }
			  }

			  //print $cnt." $modelId ".count($links)."\n";	
			  $cnt--;
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
		if($cnt>0 &&  $dopVendor){
		$new = new \App\Responsemethods\Yandexsearch();
			 $request=[
	         "count"=>$cnt,
			 "category_id"=>$dopCategory,
	         "text"=> $dopVendor
	        ];
		    $searchmodels=$new->getData($request);
			if($searchmodels)
			$models=array_merge($models,$searchmodels);
		}

		if(!$models)
		return false;
		return $models;
	}
}	