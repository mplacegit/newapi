<?php
namespace App\Responsemethods;
use App\Singleapi;
class Yandexoffer extends Abs
{
	public function getData($data){
	    $params=array();
		$this->addGeoParams($params);
		$this->addDopParams($params);
        $params[]="fields=LINK";
        $api_url='offer/'.$data["id"].'.json';
        $url=$api_url."?".implode("&",$params); 
        $str=Singleapi::getRemoteData(11, $url);
		if(!$str) return false;
		$data=json_decode($str,true);
		if(!isset($data["offer"]) || ! $data["offer"]) return false;
		return $data["offer"];

	}

}