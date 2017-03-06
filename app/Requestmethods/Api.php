<?php
namespace App\Requestmethods;
class Api extends Abs
{
	private $wid;
	private $affiliate_id;
    public $iframeSrc="https://widget.market-place.su/widget/render";
	public $iframeHash="new_api_test1";
    public function  getData($data){ 
	
		
		
		parent::$remoteUri=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:$this->getUrlFromData($data);
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']){
		parent::$clientIp=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']){
		parent::$clientIp=$_SERVER['REMOTE_ADDR'];
		}else{
		}
		
		$this->parseUrl(parent::$remoteUri,"host");
	    $this->getTestData($data);
		return ["src"=>$this->iframeSrc,"hash"=>$this->iframeHash];
    }
	private function getUrlFromData(&$data){
	$url="";
        if(isset($data["data"])){
	    $tmpdata=json_decode($data["data"],true);
		if(isset($tmpdata["url"])){
		$url=urldecode($tmpdata["url"]);
		}
		unset($tmpdata);
		}
	return $url;
	}

}