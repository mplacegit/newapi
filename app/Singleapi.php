<?php
namespace App;
class Singleapi
{
    public static $instaces=[];
    public static function getRemoteData($key,$url){
	switch($key){
	case 1:
	
	
	return app()->make("ApiA")->executeSearch($url);
	break;
	case 2:
	return app()->make("ApiB")->executeSearch($url);
	break;
	case 3:
	return self::getRandom($url);
	break;
	case 11:
	return app()->make("ApiA")->executeSearch($url,2);
	break;
    default:
	return self::getUrl($url);
	break;	
	 }
	    return [];
	}
	private static function getRandom($url){
	
	}
	//public static function getModelOffers($congig){
	
	//}
	private static function getUrl($url){
	if($curl=curl_init()){
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
	curl_setopt($curl, CURLOPT_FRESH_CONNECT, false);
	curl_setopt($curl, CURLOPT_URL, $url);
	$output= curl_exec($curl);
 	curl_close($curl);
	return  $output;
		}
    }
}